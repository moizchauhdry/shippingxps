<template>
    <MainLayout>
    <div>
      <section>
        <div class="container">
          <div class="stock-subscription-form mb-4 border p-4">
            <h2 class="font-bold text-xl text-gray-800 leading-tight form-title text-center mb-4">
                Customs Declaration Form 
            </h2>
                <template v-if="printable.includes(packag.status)">
                  <h4 class="text-center text-white bg-danger p-2 m-4"><i class="fas fa-exclamation-circle mr-1"></i> Customs declaration form already filled and package has been processed. Changes will not be saved.</h4>
                </template>

            <form @submit.prevent="submit" enctype="multipart/form-data">   
              <input type="hidden" name="packag_id" v-model="packag_id" >       
              <div class="packag-form">                
                  <breeze-validation-errors class="mb-4" />
                  <flash-messages class="mb-4" />    
                  
                  <div class="row">
                      
                    <div class="col-md-6" style="border : 1px solid #6b7280;">
                      <h2>Ship From : {{ warehouse.name }}</h2>
                      <p><strong>Contact Person:</strong> {{ warehouse.contact_person }} </p>
                      <p><strong>Phone:</strong> {{ warehouse.phone }} </p>
                      <p><strong>Email:</strong> {{ warehouse.email }} </p>
                      <p><strong>Company Name/Address: </strong> {{ warehouse.address }} </p>
                      <p><strong>Country:</strong> {{ warehouse.country == undefined ? 'USA' : warehouse.country }} </p>
                      <p><strong>Incoterms:</strong> DDU/DAP </p>                                                                                                                             
                    </div>

                    <div class="col-md-6" style="border : 1px solid #6b7280;">
                        <p><strong>Tracking No's:</strong></p>
                        <!--<template v-for="tracking_num in tracking_numbers" :key="tracking_num">
                          <p>{{ tracking_num }}</p>
                        </template>-->
                        <p><strong>Date :</strong> {{ package_date }} </p>
                        <p><strong>Package-Id :</strong> {{ packag.package_no }} </p>
                    </div>

                  </div>

                  <div class="row">
                      
                    <div class="col-md-6" style="border : 1px solid #6b7280;">
                      
                      <h2>Ship To : </h2>

                      <div class="form-group">
                        <select v-on:change="selectAddress($event)" name="address_book_id" class="form-select" v-model="address_book_id" required>
                            <template v-for="(address) in address_book" :key="address.id">
                            <option  :value="address.id" >{{ address.label}}</option>
                            </template>
                        </select>
                      </div>
                      <div v-html="current_address"></div>
                    </div>

                    <div class="col-md-6" style="border : 1px solid #6b7280;">
                        <p><strong>Sold To :</strong> Same as Shipped </p>
                    </div>
                  </div>

                  <div class="row" style="margin-top:10px;">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                        Items
                    </h2>

                    <div class="col-md-12">                                          
                      <div class="row" style="margin-bottom:20px;">
                        <div class="col-md-4">
                              <breeze-label value="Description" />
                        </div>
                        
                        <div class="col-md-1">
                              <breeze-label value="Qty" />
                        </div>

                        <div class="col-md-2">
                              <breeze-label value="Value(Unit Price)" />
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
                            <input v-model="item.quantity" name="quantity" min="1" id="quantity" type="number" class="form-control" placeholder="Qty" required />
                          </div>
                        </div>

                        <div class="col-md-2">
                          <div class="form-group">
                            <input v-model="item.unit_price" name="value" min="1" id="value" type="number" step="0.01" class="form-control" placeholder="Value" required />
                          </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                              <select name="origin_country" class="form-select" v-model="item.origin_country" required>
                                  <option selected value="">Select</option>
                                  <template v-for="country in countries" :key="country.id">
                                    <option :value="country.id" >{{ country.name}}</option>
                                  </template>
                              </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                              <select name="batteries" class="form-select" v-model="item.batteries" required>
                                  <option selected value="">Select</option>
                                  <option value="0">No Battery</option>
                                  <option value="1">Simple Batteries (Shipped on on Fedex)</option>
                                  <option value="2">Batteries Packaed with Equipment</option>
                                  <option value="3">Batteries Contained in Equipment</option>
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

                      <div class="col-md-1 offset-md-4" style="padding-right: 0px;">                        
                          <div class="form-group">
                              Total : 
                          </div>
                        </div>

                        <div class="col-md-2" style="padding-right: 0px;">                        
                          <div class="form-group">
                            <input v-model="shipping_total" readonly name="shipping_total" id="shipping_total" type="text" class="form-control" placeholder="Value" required />
                          </div>
                        </div>

                        <div class="col-md-2 offset-md-3" style="padding-right: 0px;">
                          <a v-on:click="addItem" class="btn btn-primary" style="float:right;">
                            <span>Add Item </span>
                          </a>
                        </div>
                      </div>
                    </div> 
                </div>
                <div class="row">
                    <div class="form-group">
                      <breeze-label value="Package Type" />
                      <label style="margin-bottom:10px;">
                          <input v-model="form.package_type" type="radio" id="merchandise" value="merchandise" /> Merchandise 
                      </label>
                      <br>
                      <label>
                          <input v-model="form.package_type" type="radio" id="gift" value="gift" /> Gift 
                      </label>
                    </div> 

                    <div class="form-group">
                       <template v-if="packag.status=='open'">
                          <button type="submit" class="btn btn-success float-right">Save & Submit</button>
                      </template>
                    </div>
                   
                </div>

              </div>
            
            </form>
          </div>
        </div>
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
                    package_id: this.packag.id,                                           
                    address_book_id: this.address_book_id,
                    items:this.items,
                    shipping_total :this.packag.shipping_total,
                    package_type: this.packag.package_type,
                }),
                current_address: this.selected_address,
                //list is status which have printable invoice filled.
                printable:['filled','labeled','shipped','delivered']
            };
        },
        props: {
            auth: Object,
            countries:Object,
            address_book_id:Number,
            items : Object,  
            address_book : Object,
            selected_address : String,
            packag : Object,
            warehouse: Object,
            tracking_numbers : Object,
            package_date : String 
        },
        methods : {
          	submit() {
              if(this.printable.includes(this.packag.status)){
                alert('Customs declaration form already filled and package has been processed. Changes will not be saved.');
                return false;
              }
              this.form.post(this.route('package.store'))
            },
            addItem(){
                this.form.items.push(
                        {
                          description: "",
                          quantity: "",
                          value:0,
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
            getTotalValue(){
              return this.items.reduce(function(a, c){return a + Number((c.quantity*c.value) || 0)}, 0)
            },
            selectAddress(event){                
                var address = this.address_book[event.target.value];
                this.current_address = address.full_address;
              console.log('target value ' + event.target.value)
              console.log('old value ' + this.form.address_book_id)
              this.form.address_book_id = event.target.value
              console.log('new value ' + this.form.address_book_id)
            }
        },
        computed: {
          shipping_total() {  

            return this.form.items.reduce((acc, item) => {
              return acc + (parseInt(item.unit_price)*parseInt(item.quantity));
            }, 
            0);    

          }
        }
    }
</script>