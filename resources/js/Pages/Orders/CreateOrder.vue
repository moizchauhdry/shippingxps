<template>
    <MainLayout>
    <div>
    
        <section>
      <div class="container">

        <div class="stock-subscription-form">

          <form @submit.prevent="submit" enctype="multipart/form-data">          
            <div class="order-form">                
                <breeze-validation-errors class="mb-4" />
                <flash-messages class="mb-4" />    
                
                <div class="row">
                  <div class="col-md-3">

                    <div class="form-group">
                      <breeze-label for="customer_id" value="Customer" />
                      <select name="customer_id" class="form-select" v-model="form.customer_id" :disabled="selected_customer != 0" required>
                        <option selected>Select</option>
                        <option v-for="customer in customers" :value="customer.id" :key="customer.id" >{{ customer.name}}</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <breeze-label for="warehouse_id" value="Received At" />
                      <select name="warehouse_id" class="form-select" v-model="form.warehouse_id" required>
                        <option selected>Select</option>
                        <option v-for="warehouse in warehouses" :value="warehouse.id"  :key="warehouse.id" >{{ warehouse.name}}</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <breeze-label for="tracking_number_in" value="Tracking #" />
                      <input v-model="form.tracking_number_in" name="tracking_number_in" id="tracking_number_in" type="text" class="form-control" placeholder="Tracking Number" required />
                    </div>

                    </div>
                  
                    <div class="col-md-3">

                      <div class="form-group">
                        <breeze-label for="received_from" value="Received From" />
                        <input v-model="form.received_from" name="received_from" id="received_from" type="text" class="form-control" placeholder="Like Amazon, Ebay, etc" required />
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <breeze-label for="warehouse_id" value="Weight Unit" />
                            <select name="weight_unit" class="form-select" v-model="form.weight_unit" required>
                              <option value="lb">Lb</option>
                              <option value="kg">Kg</option> 
                            </select>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <breeze-label for="dim_unit" value="Dimention Unit" />
                            <select name="dim_unit" class="form-select" v-model="form.dim_unit" required>
                              <option value="in">Inch</option>
                              <option value="cm">Cm</option> 
                            </select>
                          </div>  
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <breeze-label for="package_weight" value="Package Weight" />
                        <input v-model="form.package_weight" name="package_weight" id="package_weight" type="number" class="form-control" placeholder="Package Weight" required />
                      </div>

                    </div> 

                    <div class="col-md-3">                    
                    
                      <div class="form-group">
                        <breeze-label for="package_length" value="Package Length" />
                        <input v-model="form.package_length" name="package_length" id="package_length" type="number" class="form-control" placeholder="Package Length" required />
                      </div>

                        <div class="form-group">
                          <breeze-label for="package_height" value="Package Height" />
                          <input v-model="form.package_height" name="package_height" id="package_height" type="number" class="form-control" placeholder="Package Height" required />
                        </div>

                        <div class="form-group">
                          <breeze-label for="package_weight" value="Package Width" />
                          <input v-model="form.package_width" name="package_width" id="package_width" type="number" class="form-control" placeholder="Package Width" required />
                        </div>                    

                    </div>
                                      
                    <div  class="col-md-3">
                      <div class="form-group">
                        <breeze-label for="notes" value="Notes" />
                        <textarea v-model="form.notes" 
                        name="notes" 
                        id="notes" 
                        class="form-control" 
                        placeholder="Notes" 
                        rows="7"
                        style="resize:none;"
                        required >
                        </textarea>
                      </div>
                    </div>
                </div>
     
                <div class="row">
                  <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                          <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                              Items
                          </h2>
                       </div>   
                       <div class="col-md-6">
                        <a v-on:click="addItem" class="btn btn-primary" style="float:right;">
													<span>Add Item </span>
												</a>
                       </div>         
                    </div>            

                    <div class="row">
                        <div class="col-md-4">
                          <breeze-label for="name" value="Name" />
                        </div>                        
                        <div class="col-md-5">
                            <breeze-label for="description" value="Description" />
                        </div>
                        <div class="col-md-1">
                            <breeze-label for="quantity" value="Qty" />
                        </div>
                        <div class="col-md-3" v-show="false">
                            <breeze-label for="image" value="Image" />
                        </div>
                        <div class="col-md-1">
                        </div>
                    </div>

                    <div v-for="(item,index) in form.items" :key="item.id" class="row">
                      
                      <div class="col-md-4">
                        <div class="form-group">                          
                          <input v-model="item.name" name="name" id="name" type="text" class="form-control" placeholder="Name" required />
                        </div>
                      </div>

                      <div class="col-md-5">
                        <div class="form-group">
                          <input v-model="item.description" name="description" id="description" type="text" class="form-control" placeholder="Description" required />
                        </div>
                      </div>

                      <div class="col-md-1">
                        <div class="form-group">
                          <input v-model="item.quantity" name="quantity" id="quantity" type="number" class="form-control" placeholder="Qty" required />
                        </div>
                      </div>

                      <div class="col-md-3" v-show="false">
                        <div class="form-group">
                            <input type="file"  @input="item.image = $event.target.files[0]" />    
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                {{ form.progress.percentage }}%
                            </progress>
                        </div>
                      </div>

                      <div class="col-md-1" v-show="index!=0">
                        <div class="form-group">
                          <a v-on:click="removeItem(index)" class="btn btn-primary" style="margin-top:10px;">
                            <span>Remove</span>
                          </a>
                        </div>
                      </div>

                    </div>

                  </div> 

                 </div>



                <div class="row">
                  <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                          <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                              Order Images
                          </h2>
                       </div>   
                       <div class="col-md-6">
                          <a v-on:click="addImage" class="btn btn-primary" style="float:right;">
                            <span>Add More Image </span>
                          </a>
                       </div>         
                    </div>            

                    <div class="row">
                        <div class="col-md-3">
                            <breeze-label for="name" value="Image" />
                        </div>                        
                    </div>

                    <div v-for="(image,index) in form.images" :key="image.id" class="row">
                      
                      <div class="col-md-3">
                        <div class="form-group">
                            <input type="file"  @input="image.image = $event.target.files[0]" />    
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                {{ form.progress.percentage }}%
                            </progress>
                        </div>
                      </div>

                      <div class="col-md-1" v-show="index!=0">
                        <div class="form-group">
                          <a v-on:click="removeImage(index)" class="btn btn-primary" style="margin-top:10px;">
                            <span>Remove</span>
                          </a>
                        </div>
                      </div>

                    </div>

                  </div> 

                 </div>
               

              <div class="order-button">
                <input type="submit" value="Create Order" class="btn btn-danger" />
              </div>
            </div>
           
          </form>
        </div><!-- subscription -->
      </div><!-- container -->
    </section>
         
    </div>
    </MainLayout>
</template>

<script>
    import MainLayout from '@/Layouts/Main'
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
    import BreezeLabel from '@/Components/Label'

    export default {
        components: {
            BreezeAuthenticatedLayout,
            MainLayout,
            BreezeLabel
        },
        data() {
            return {
                form: this.$inertia.form({
                    customer_id: this.selected_customer,        
                    warehouse_id: '',      
                    received_from: '',
                    tracking_number_in: '',
                    notes: '', 
                    weight_unit: 'lb',
                    dim_unit: 'in',
                    package_weight:'',
                    package_length:'',
                    package_width:'',
                    package_height:'',
                    images : [
                        {
                          image: ""
                        }                      
                    ],
                    items:[
                        {
                          name: "",
                          description: "",
                          quantity: "",
                          image: ""
                        }
                    ],
                })
            };
        },
        props: {
            auth: Object,
            customers:Object,
            selected_customer:Number,
            warehouses: Object,           
        },
        methods : {
          	submit() {
              this.form.post(this.route('orders.store'))
            },
            addItem(){
                this.form.items.push(
                      {
                        name: "",
                        description: "",
                        quantity: "",
                        image: ""
                      }
              )
            },
            removeItem(index){
              this.form.items.splice(index, 1);
            },
            addImage(){
                this.form.images.push(
                      {
                        name: ""
                      }
              )
            },
            removeImage(index){
              this.form.images.splice(index, 1);
            },
            storePhoto() {
                if (this.$refs.photo) {
                    this.form.post_image = this.$refs.photo.files[0]
                }
                this.form.post(route('photo.store'), {
                    preserveScroll: true
                });
            }, 
        }
    }
</script>
