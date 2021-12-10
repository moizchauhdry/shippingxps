<template>
    <MainLayout>
    <div>
    
    <section>
      <div class="container">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Orders </h2>
        <div class="stock-subscription-form">

          <form @submit.prevent="submit" enctype="multipart/form-data">          
            <div class="order-form">                
                <breeze-validation-errors class="mb-4" />
                <flash-messages class="mb-4" />    
                
                <div class="row">
                  <div class="col-md-3">

                    <div class="form-group">
                      <breeze-label for="customer_id" value="Customer" />
                      <select name="customer_id" class="form-select" v-model="form.customer_id" disabled required>
                        <option selected>Select</option>
                        <option v-for="customer in customers" :value="customer.id" >{{ customer.name}}</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <breeze-label for="status" value="Status" />
                      <select name="status" class="form-select" v-model="form.status"  required>
                        <option v-for="status in status_list" :value="status" >{{ status}}</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <breeze-label for="order_id" value="Order Id" />
                      <input v-model="form.order_id" name="order_id" id="tracking_number" type="text" class="form-control" placeholder="Order id" required />
                    </div>
                  </div>

                  <div class="col-md-3">

                      <div class="form-group">
                        <breeze-label for="tracking_number" value="Tracking #" />
                        <input v-model="form.tracking_number" name="tracking_number" id="tracking_number" type="text" class="form-control" placeholder="Tracking Number" required />
                      </div>
                  
                      <div class="form-group">
                        <breeze-label for="shipping_total" value="Shipping Total" />
                        <input v-model="form.shipping_total" name="tracking_number" id="shipping_total" type="number" class="form-control" placeholder="Shippinng Total" required />
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

                  <div class="col-md-3">

                    <div class="form-group">
                      <breeze-label for="package_weight" value="Declared Value" />
                      <input v-model="form.declared_value" name="declared_value" id="declared_value" type="number" class="form-control" placeholder="Declared Value" required />
                    </div>

                    <div class="form-group">
                      <breeze-label for="notes" value="Notes" />
                      <textarea v-model="form.notes" 
                      name="notes" 
                      id="notes" 
                      class="form-control" 
                      placeholder="Notes" 
                      rows="4"
                      style="resize:none;"
                       >
                      </textarea>
                    </div>

                  </div> 

                </div>

                <div class="row">

                  <h1>Items</h1>

                  <div class="col-md-12">

                    <div v-for="(item,index) in form.items" :key="item.id" class="row">
                      
                      <div class="col-md-3">
                        <div class="form-group">
                          <breeze-label for="name" value="Name" />
                          <input v-model="item.name" name="name" id="name" type="text" class="form-control" placeholder="Name" required />
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="form-group">
                          <breeze-label for="description" value="Description" />
                          <input v-model="item.description" name="description" id="description" type="text" class="form-control" placeholder="Description" required />
                        </div>
                      </div>

                      <div class="col-md-1">
                        <div class="form-group">
                          <breeze-label for="quantity" value="Qty" />
                          <input v-model="item.quantity" name="quantity" id="quantity" type="number" class="form-control" placeholder="Qty" required />
                        </div>
                      </div>

                      <div class="col-md-2">
                          <div class="text-center" v-if="item.image !=''">
                              <img style="width:100px;height:auto;" class="img-thumbnail" :src="imgURL(item.image)" />
                          </div>                        
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                            <input type="file" @input="item.image = $event.target.files[0]" />    
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                {{ form.progress.percentage }}%
                            </progress>
                        </div>
                      </div>

                      <div class="col-md-1" v-show="index!=0">
                        <div class="form-group">
                          <a v-on:click="removeItem(index,item.id)" class="btn btn-primary" style="margin-top:10px;">
                            <span>Remove</span>
                          </a>
                        </div>
                      </div>

                    </div>

                    <div class="row">
                      <div class="col-md-2 offset-md-10">
                        <a v-on:click="addItem" class="btn btn-primary" style="float:right;">
													<span>Add Item </span>
												</a>
                      </div>
                    </div>

                  </div> 

            </div>
                                        
              <div class="order-button">
                <input type="submit" value="Update Order" class="btn btn-danger" />
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
                    id:this.order.id,
                    status : this.order.status,
                    customer_id: this.order.customer_id,          
                    order_id: this.order.order_id,     
                    tracking_number: this.order.tracking_number,     
                    shipping_total: this.order.shipping_total,
                    package_weight:this.order.package_weight,
                    package_length:this.order.package_length,
                    package_width:this.order.package_width,
                    package_height:this.order.package_height,
                    declared_value:this.order.declared_value,    
                    notes: this.order.notes,                       
                    address: this.order.address, 
                    post_image: '',    
                    items:this.order.items,
                })
            };
        },
        props: {
            auth: Object,
            customers:Object,
            order:Object, 
            status_list:Object          
        },
        methods : {
          	submit() {
                this.form.post(this.route('orders.update'))
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
            removeItem(index,item_id){
              //when deleting already saved item, remove from server also.
              if (typeof item_id !== 'undefined'){
                 
                axios.post(this.route('orders.removeItem'),{item_id:item_id})
                .then(({ data }) => {                      
                      this.form.items.splice(index, 1);
                  }
                );
              }else{
                  this.form.items.splice(index, 1);
              }

            },
            imgURL(url) {
                return "/public/uploads/"+url;
            },
        }
    }
</script>
