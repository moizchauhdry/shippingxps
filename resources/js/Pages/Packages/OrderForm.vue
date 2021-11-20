<template>
    <MainLayout>
    <div>
    
        <section>
      <div class="container">

        <div class="stock-subscription-form">
          <h2> Form</h2>
          <form @submit.prevent="submit" enctype="multipart/form-data">          
            <div class="order-form">                
                <breeze-validation-errors class="mb-4" />
                <flash-messages class="mb-4" />    
                

                <div class="row">

                  <h1>Items</h1>

                  <div class="col-md-12">

                                          
                    <div class="row" style="margin-bottom:20px;">
                      <div class="col-md-4">
                            <breeze-label value="Description" />
                      </div>
                      <div class="col-md-1">
                            <breeze-label value="Qty" />
                      </div>
                      <div class="col-md-2">
                            <breeze-label value="Value" />
                      </div>
                      <div class="col-md-2">
                            <breeze-label value="Origin" />
                      </div>
                      <div class="col-md-2">
                            <breeze-label value="Batteries" />
                      </div>
                      <div class="col-md-1">
                      </div>
                    </div>

                    <div v-for="(item,index) in form.items" :key="item.id" class="row">
                      
                      <div class="col-md-4">
                        <div class="form-group">
                          <input v-model="item.description" name="description" id="description" type="text" class="form-control" placeholder="Description" required />
                        </div>
                      </div>

                      <div class="col-md-1">
                        <div class="form-group">
                          <input v-model="item.quantity" name="quantity" id="quantity" type="number" class="form-control" placeholder="Qty" required />
                        </div>
                      </div>

                      <div class="col-md-2">
                        <div class="form-group">
                          <input v-model="item.value" name="value" id="value" type="number" class="form-control" placeholder="Value" required />
                        </div>
                      </div>

                      <div class="col-md-2">
                          <div class="form-group">
                            <select name="origin_country" class="form-select" v-model="item.origin_country" required>
                                <option selected value="">Select</option>
                                <option v-for="country in countries" :value="country.id" >{{ country.name}}</option>
                            </select>
                          </div>
                      </div>

                      <div class="col-md-2">
                          <div class="form-group">
                            <select name="batteries" class="form-select" v-model="item.batteries" required>
                                <option selected value="">Select</option>
                                <option value="0">No Battery</option>
                                <option value="1">1 Battery</option>
                                <option value="2">2 Battery</option>
                                <option value="3">3 Battery</option>
                            </select>
                          </div>
                      </div>

                      <div class="col-md-1" v-show="index!=0">
                        <div class="form-group">
                          <a v-on:click="removeItem(index)" class="btn btn-primary">
                            <span>Delete</span>
                          </a>
                        </div>
                      </div>

                    </div>

                    <div class="row">
                      <div class="col-md-2 offset-md-10" style="padding-right: 0px;">
                        <a v-on:click="addItem" class="btn btn-primary" style="float:right;">
													<span>Add Item </span>
												</a>
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
                    order_id: '',                                    
                    address: '',
                      items:[
                        {
                          description: "",
                          quantity: "",
                          value:"",
                          origin_country: "",
                          batteries:""
                        }
                    ],
                })
            };
        },
        props: {
            auth: Object,
            countries:Object,
            selected_customer:Number           
        },
        methods : {
          	submit() {
              this.form.post(this.route('orders.store'))
            },
            addItem(){
                this.form.items.push(
                        {
                          description: "",
                          quantity: "",
                          value:"",
                          origin_country: "",
                          batteries:""
                        }
              )
            },
            removeItem(index){
              this.form.items.splice(index, 1);
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
