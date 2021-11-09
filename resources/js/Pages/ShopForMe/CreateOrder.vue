<template>
  <MainLayout>
    <div>
      <section>
        <div class="container">
          <ul class="nav nav-pills nav-justified mb-3" id="pills-tab " role="tablist">
            <li class="nav-item" role="presentation">
              <button
                  v-on:click="setActiveTabAB('tab1')"
                  :class="getTabClass('tab1')"
                  id="pills-home-tab"
                  data-bs-toggle="pill"
                  data-bs-target="#pills-home"
                  type="button"
                  role="tab"
                  aria-controls="pills-home"
                  aria-selected="true">Online Order</button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                  v-on:click="setActiveTabAB('tab2')"
                  :class="getTabClass('tab2')"
                  id="pills-profile-tab"
                  data-bs-toggle="pill"
                  data-bs-target="#pills-profile"
                  type="button"
                  role="tab"
                  aria-controls="pills-profile"
                  aria-selected="false">Pickup Order</button>
            </li>
          </ul>
          <div class="tab-content" id="pills-tabContent">
            <div class="stock-subscription-form">              
                <div class="order-form">
                  <div :class="getTabPaneClass('tab1')" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">                    
                    <form @submit.prevent="submitFormOnline" enctype="multipart/form-data">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <breeze-label for="warehouse_id" value="Warehouse" />
                          <select name="warehouse_id" class="form-select" v-model="form_online.warehouse_id" required @change="wareHouseChangeOnline()">
                            <option selected disabled>Select</option>
                            <option v-for="warehouse in warehouses" :value="warehouse.id"  :key="warehouse.id" >{{ warehouse.name}}</option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col form-group">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">Shop Information</h2>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-8">
                        <div class="form-group">
                          <breeze-label for="package_weight" value="Site Name" />
                          <input v-model="form_online.site_name" name="site_name" id="site_name" type="text" class="form-control" placeholder="Site Name" :required="setRequired('tab1')" />
                        </div>

                        <div class="form-group">
                          <breeze-label for="package_length" value="Shop URL" />
                          <input v-model="form_online.shop_url" name="shop_url" id="shop_url" type="url" class="form-control" placeholder="Shop URL" :required="setRequired('tab1')"/>
                        </div>

                      </div>

                      <div  class="col-md-4">
                        <div class="form-group">
                          <breeze-label for="notes" value="Notes" />
                          <textarea v-model="form_online.notes"
                                    name="notes"
                                    id="notes"
                                    class="form-control"
                                    placeholder="Notes"
                                    rows="5"
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
                            <a v-on:click="addItemOnline" class="btn btn-primary" style="float:right;">
                              <span>Add Item </span>
                            </a>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-md-2">
                            <breeze-label for="name" value="Name" />
                          </div>
                          <div class="col-md-2  ">
                            <breeze-label for="option" value="Description" />
                          </div>
                          <div class="col-md-2">
                            <breeze-label for="url" value="Url" />
                          </div>
                          <div class="col-md-1 p-0">
                            <breeze-label for="price" value="Price(USD)" />
                          </div>
                          <div class="col-md-2">
                            <breeze-label for="price_with_tax" value="Price with Tax (USD)" />
                          </div>
                          <div class="col-md-1">
                            <breeze-label for="qty" value="Qty" />
                          </div>
                          <div class="col-md-1">
                            <breeze-label for="total" value="Total" />
                          </div>
                        </div>

                        <div v-for="(item,index) in form_online.items" :key="item.id" class="row" :id="'order-'+index" :data-id="index">

                          <div class="col-md-2">
                            <div class="form-group">
                              <input v-model="item.name" name="name" id="name" type="text" class="form-control" placeholder="Name" required />
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <input v-model="item.option" name="option" id="option" type="text" class="form-control"  placeholder="Description" />
                            </div>
                          </div>

                          <div class="col-md-2">
                            <div class="form-group">
                              <input v-model="item.url" name="url" id="url" type="url" class="form-control" placeholder="URL" required />
                            </div>
                          </div>
                          <div class="col-md-1 p-0">
                            <div class="form-group">
                              <input v-model="item.price" v-on:change="addShopTax($event)" @click="addShopTax($event)" ref="price_online" name="price" type="number" class="form-control" placeholder="Price" required />
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <input v-model="item.price_with_tax" name="price_with_tax" id="price_with_tax" type="number" class="form-control" placeholder="Price With Tax ($)" required readonly />
                            </div>
                          </div>
                          <div class="col-md-1">
                            <div class="form-group">
                              <input v-model="item.qty" v-on:change="addShopTax($event)" name="qty" id="qty" type="number" class="form-control" placeholder="Qty" min="0" max="9" required />
                            </div>
                          </div>
                          <div class="col-md-1 p-0">
                            <div class="form-group">
                              <input v-model="item.sub_total" name="sub_total" id="sub_total" type="number" class="form-control sub_total" placeholder="T.Price" required readonly/>
                            </div>
                          </div>
                          <div class="col-md-1" v-show="index!=0">
                            <div class="form-group">
                              <a v-on:click="removeItemOnline(index)" class="btn btn-primary">
                                <span>Remove</span>
                              </a>
                            </div>
                          </div>

                        </div>
                        <div class="row">
                          <div class="col-1 offset-md-9">
                            <breeze-label for="grand_total" value="Grand Total" />
                          </div>
                          <div class="col-1 p-0">
                            <input v-model="form_online.grand_total" name="grand_total" id="grand_total" type="number" class="form-control grand_total"  placeholder="T.Price" required readonly/>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="order-button">
                        <input type="submit" value="Finish Order" class="btn btn-danger" />
                    </div>
                  </form>
                  </div>

                  <div :class="getTabPaneClass('tab2')" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <form @submit.prevent="submitFormPickup" enctype="multipart/form-data">
                      
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <breeze-label for="warehouse_id" value="Warehouse" />
                            <select name="warehouse_id" class="form-select" v-model="form_pickup.warehouse_id" @change="filterStores()" required>
                              <option selected disabled>Select</option>
                              <option v-for="warehouse in warehouses" :value="warehouse.id"  :key="warehouse.id" >{{ warehouse.name}}</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <breeze-label for="store_id" value="Shopping Mall" />
                            <select name="store_id" class="form-select" v-model="form_pickup.store_id" :required="setRequired('tab2')" v-on:change="setPickupCharges($event)" >
                              <option selected disabled>Select</option>
                              <option v-for="store in stores" :value="store.id"  :key="store.id" >{{ store.name }}</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-2" v-show="this.form_pickup.store_id !==''">
                          <div class="form-group">
                            <breeze-label for="pickup_charges" value="Pickup Charges (USD)" />
                            <input type="text" id="pickup_charges" v-model="form_pickup.pickup_charges" readonly>
                          </div>
                        </div>
                      </div>
                      <div class="row">

                        <div class="col-md-4">
                          <div class="form-group">
                            <breeze-label for="store_name" value="Store Name" />
                            <input type="text" id="store_name" v-model="form_pickup.store_name">
                          </div>
                        </div>

                        <div class="col-md-8">
                          <div class="form-group">
                              <breeze-label for="pickup_image" value="Image" />
                              <input id="pickup_image" type="file"  @input="form_pickup.image = $event.target.files[0]" />    
                              <progress v-if="form_pickup.progress" :value="form_pickup.progress.percentage" max="100">
                                  {{ form_pickup.progress.percentage }}%
                              </progress>
                              <p style="color:red;">Upload image/screenshort that helps in order pickup, like receipt,email,ordernumber, etc</p>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div  class="col-md-12">
                          <div class="form-group">
                            <breeze-label for="notes" value="Notes" />
                            <textarea v-model="form_pickup.notes"
                                      name="notes"
                                      id="note"
                                      class="form-control"
                                      placeholder="Notes"
                                      rows="3"
                                      style="resize:none;"
                                      required >
                            </textarea>
                          </div>
                        </div>
                      </div>
                      <div class="row mb-2">
                        <div class="col-md-8">
                          <input type="radio" v-model="form_pickup.pickup_type" name="pickup_type" value="pickup_only" :required="setRequired('tab2')"> Only Pickup                       
                          <input type="radio" v-model="form_pickup.pickup_type" name="pickup_type" value="shipping_xps_purchase" :required="setRequired('tab2')"> Shipping XPS Purchase                      
                        </div>
                        
                        <div class="col">
                          <breeze-label for="pickup_date" value="Pickup Date" />
                          <input type="datetime-local" v-model="form_pickup.pickup_date" name="pickup_date" class="form-control" placeholder="Pickup Data" :required="setRequired('tab2')">
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
                              <a v-on:click="addItemPickup" class="btn btn-primary" style="float:right;">
                                <span>Add Item </span>
                              </a>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-2">
                              <breeze-label for="name" value="Name" />
                            </div>
                            <div class="col-md-2  ">
                              <breeze-label for="option" value="Description" />
                            </div>
                            <!-- <div class="col-md-2">
                              <breeze-label for="url" value="Url" />
                            </div> -->
                            <div class="col-md-2">
                              <breeze-label for="price" value="Price(USD)" />
                            </div>
                            <div class="col-md-2">
                              <breeze-label for="price_with_tax" value="Price with Tax (USD)" />
                            </div>
                            <div class="col-md-1">
                              <breeze-label for="qty" value="Qty" />
                            </div>
                            <div class="col-md-1">
                              <breeze-label for="total" value="Total" />
                            </div>
                          </div>

                          <div v-for="(item,index) in form_pickup.items" :key="item.id" class="row" :id="'order-'+index" :data-id="index">

                            <div class="col-md-2">
                              <div class="form-group">
                                <input v-model="item.name" name="name" id="name" type="text" class="form-control" placeholder="Name" required />
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <input v-model="item.option" name="option" id="option" type="text" class="form-control" placeholder="Option" />
                              </div>
                            </div>

                            <!-- <div class="col-md-2">
                              <div class="form-group">
                                <input v-model="item.url" name="url" id="url" type="url" class="form-control" placeholder="URL" required />
                              </div>
                            </div> -->

                            <div class="col-md-2">
                              <div class="form-group">
                                <input v-model="item.price" v-on:change="addPickUpTax($event)" v-on:click="addPickUpTax($event)" name="price" ref="price" type="number" class="form-control price" placeholder="Price" required />
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <input v-model="item.price_with_tax" name="price_with_tax" id="price_with_tax" type="text" class="form-control" placeholder="Price After Tax ($)" required readonly/>
                              </div>
                            </div>
                            <div class="col-md-1">
                              <div class="form-group">
                                <input v-model="item.qty" v-on:change="addPickUpTax($event)" name="qty" id="qty" type="number" class="form-control" placeholder="Qty" min="0" max="9" required />
                              </div>
                            </div>
                            <div class="col-md-1 p-0">
                              <div class="form-group">
                                <input v-model="item.sub_total" name="sub_total" id="sub_total" type="number" class="form-control sub_total" placeholder="T.Price" required readonly/>
                              </div>
                            </div>
                            <div class="col-md-1" v-show="index!=0">
                              <div class="form-group">
                                <a v-on:click="removeItemPickup(index)" class="btn btn-primary">
                                  <span>Remove</span>
                                </a>
                              </div>
                            </div>

                          </div>

                          <!-- sub_total -->
<!--                          <div class="row mb-2">
                            <div class="col-2 offset-md-8">
                              <breeze-label class="float-right" for="form_pickup.subtotal" value="Sub Total" />
                            </div>
                            <div class="col-1 p-0">
                              <input v-model="form_pickup.sub_total" name="sub_total" id="form_pickup.subtotal" type="number" class="form-control sub_total"  placeholder="T.Price" required readonly/>
                            </div>
                          </div>
                    &lt;!&ndash; Coupon Code &ndash;&gt;
                          <div class="row mb-2">
                            <div class="col-1 offset-md-9">
                              <breeze-label class="float-right"  value="Coupon Code" />
                            </div>
                            <div class="col-1 p-0">
                              <input v-model="form_pickup.coupon_code" name="coupon_code" id="coupon_code" type="text" class="form-control coupon_code"  placeholder="Enter Coupon" />
                            </div>
                            <div class="col-1 p-0">
                              <a v-on:click="checkCouponCode(this.form_pickup.coupon_code)" class="btn btn-primary">
                                <span>Apply</span>
                              </a>
                            </div>

                          </div>
                          &lt;!&ndash; discount &ndash;&gt;
                          <div class="row mb-2">
                            <div class="col-1 offset-md-9">
                              <breeze-label class="float-right" for="discount" value="Discount" />
                            </div>
                            <div class="col-1 p-0">
                              <input v-model="form_pickup.discount" name="discount" id="discount" type="number" class="form-control discount"  placeholder="Discount" required readonly/>
                            </div>
                          </div>-->
<!--                          &lt;!&ndash; service_charges &ndash;&gt;
                          <div class="row mb-2">
                            <div class="col-2 offset-md-8">
                              <breeze-label class="float-right" for="service_charges" value="Services Charges" />
                            </div>
                            <div class="col-1 p-0">
                              <input v-model="form_pickup.service_charges" name="service_charges" id="service_charges" type="number" class="form-control service_charges"  placeholder="T.Price" required readonly/>
                            </div>
                          </div>-->
                          <!-- grand_total -->
                          <div class="row mb-2">
                            <div class="col-2 offset-md-7">
                              <breeze-label class="float-right" for="grand_total" value="Grand Total" />
                            </div>
                            <div class="col-1 p-0">
                              <input v-model="form_pickup.grand_total" name="grand_total" id="grand_total" type="text" class="form-control grand_total"  placeholder="G.Price" required readonly/>
                            </div>
                          </div>
                        </div>

                      </div>
                      <div class="order-button">
                        <input type="submit" value="Finish Order" class="btn btn-danger" />
                      </div>
                    </form>
                  </div>

                  <!-- <div class="row" v-show="false">
                    <div class="col form-group">
                      <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">Additional Charges</h2>
                    </div>
                  </div>
                  <div class="row" v-show="false">
                    <div class="col">
                      <div class="form-group">
                        <breeze-label for="package_weight" value="Shipping From Shop" />
                        <input v-model="form.shipping_from_shop" name="shipping_from_shop" id="shipping_from_shop" type="text" class="form-control" placeholder="Shipping From Shop" />
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group">
                        <breeze-label for="package_length" value="Sales Tax" />
                        <input v-model="form.sales_tax" name="sales_tax" id="sales_tax" type="number" class="form-control" placeholder="Sales Tax" />
                      </div>
                    </div>
                  </div> -->
                </div>
            </div><!-- subscription -->
          </div>
        </div><!-- container -->
      </section>
    </div>
  </MainLayout>
</template>

<style>
.active {
  background-color: white;
  border: none;
  border-radius: none;
}
</style>

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
      form_online: this.$inertia.form({
        form_type:'shopping',
        warehouse_id: '',        
        site_name: '',
        shop_url: '',        
        notes: '',
        shipping_from_shop: '',
        sales_tax:'',
        pickup_type: '',
        pickup_date: '',
        pickup_charges:'',
        grand_total: 0,
        coupon_code: '',
        discount:0,
        sub_total:0,
        service_charges:0,
        items:[
          {
            name: "",
            option: "",
            url: "",
            price: "",
            price_with_tax: "",
            qty: "",
            sub_total: ""
          }
        ],
      }),
      form_pickup: this.$inertia.form({
        form_type:'pickup',
        warehouse_id: '',        
        store_id: '',
        site_name: '',
        store_name: '',
        image:'',
        shop_url: '',        
        notes: '',
        shipping_from_shop: '',
        sales_tax:'',
        pickup_type: '',
        pickup_date: '',
        pickup_charges:'',
        grand_total: 0,
        coupon_code: '',
        discount:0,
        sub_total:0,
        service_charges:0,
        items:[
          {
            name: "",
            option: "",
            url: "",
            price: "",
            price_with_tax: "",
            qty: "",
            sub_total: ""
          }
        ],
      }),
      discount_percentage: 0,
      tabs : {
      tab1:true,
      tab2:false
      },
      stores: []
    };
  },
  props: {
    auth: Object,
    warehouses: Object,
  },
  methods : {
    submitFormOnline() {
      this.form_online.post(this.route('shop-for-me.store'))
    },
    submitFormPickup() {
      this.form_pickup.post(this.route('shop-for-me.store'))
    },
    addItemOnline(){
      this.form_online.items.push({
        name: "",
        option: "",
        url: "",
        price: "",
        price_with_tax: "",
        qty: "",
        sub_total: ""
      })
    },
    removeItemPickup(index){
      this.form_pickup.items.splice(index, 1);
    },
    addItemPickup(){
      this.form_pickup.items.push({
        name: "",
        option: "",
        url: "",
        price: "",
        price_with_tax: "",
        qty: "",
        sub_total: ""
      })
    },
    removeItemOnline(index){
      this.form_online.items.splice(index, 1);
    },
    setActiveTabAB(tab){
      for (var key in this.tabs) {
        if(key === tab){
          this.tabs[key] = true;
        }else{
          this.tabs[key] = false;
        }
        // to handle different form submit behaviour for the same form
        // if (tab === 'tab1') {
        //   this.form.form_type = 'shop';
        // } else {
        //   this.form.form_type = 'pickup';
        // }
      }
    },

    getTabClass(tab){

      if(this.tabs[tab] === true){
        return 'nav-link active';
      }else{
        return 'nav-link';
      }

    },

    setRequired(tab){
      return this.tabs[tab];
    },

    getTabPaneClass(tab){

      if(this.tabs[tab] === true){
        return 'tab-pane show active';
      }else{
        return 'tab-pane fade d-none';
      }
    },

    filterStores() {
      const params = {
        warehouse_id: this.form_pickup.warehouse_id
      };

      this.$refs.price.click();

      axios.get("/shop-for-me/filter-stores/" + this.form_pickup.warehouse_id)
      .then(({ data }) => {
            this.stores = data.stores;
          }
      );
    },
    wareHouseChangeOnline(){
      this.$refs.price_online.click();
    },
    setPickupCharges(event){
      var store_id = event.target.value;
      var pickup_charges = 0;
      for (var i = 0; i < this.stores.length; i++) {
        if(this.stores[i]['id'] == store_id){
          pickup_charges = this.stores[i]['pickup_charges'];
        }
      }
      this.form_pickup.pickup_charges = pickup_charges;
    },
    addShopTax(event){
      console.log('triggered...');
      var mainParent = event.target.parentNode.parentNode.parentNode;
      var row = document.getElementById(mainParent.id);
      var index = row.dataset.id;
      var quantity = this.form_online.items[index].qty;
      var price = this.form_online.items[index].price;
      var sale_tax = 0;
      for (var i = 0; i < this.warehouses.length; i++) {
        if(this.warehouses[i]['id'] == this.form_online.warehouse_id){
          sale_tax = this.warehouses[i]['sale_tax'];
        }
      }
      var gross_total = (price * (sale_tax/100));
      var net_total = this.form_online.items[index].price_with_tax = (parseFloat(gross_total) + parseFloat(price)).toFixed(2);
      this.form_online.items[index].sub_total = net_total * quantity;

      this.getShopGrandTotal();
    },
    getShopGrandTotal(){
      var sum = 0;
      this.form_online.items.forEach(function(n){sum += n['sub_total']});
      console.log(sum);

      this.form_online.grand_total = sum;
    },
    addPickUpTax(event){
      console.log('triggered...');
      var mainParent = event.target.parentNode.parentNode.parentNode;
      var row = document.getElementById(mainParent.id);
      var index = row.dataset.id;
      var quantity = this.form_pickup.items[index].qty;
      var price = this.form_pickup.items[index].price;
      var sale_tax = 0;
      for (var i = 0; i < this.warehouses.length; i++) {
        if(this.warehouses[i]['id'] == this.form_pickup.warehouse_id){
          sale_tax = this.warehouses[i]['sale_tax'];
        }
      }
      var gross_total = (price * (sale_tax/100));
      var net_total = this.form_pickup.items[index].price_with_tax = (parseFloat(gross_total) + parseFloat(price)).toFixed(2);
      this.form_pickup.items[index].sub_total = net_total * quantity;

      this.getPickUpGrandTotal();
    },
    getPickUpGrandTotal(){
      var sum = 0;
      this.form_pickup.items.forEach(function(n){sum += n['sub_total']});
      console.log(sum);
      console.log(this.discount_percentage);

      this.form_pickup.sub_total = parseFloat(sum).toFixed(2);
      // this.form_pickup.service_charges = parseFloat(sum).toFixed(2)* 0.05;
      this.form_pickup.discount = sum * this.discount_percentage/100;
      var charges = parseFloat(this.form_pickup.shipping_from_shop)
      this.form_pickup.shipping_charges = charges
      this.form_pickup.grand_total = sum;
    },
    checkCouponCode(coupon_code){
      axios.post(this.route('checkCoupon'),{
        code:coupon_code,
      }).then(function (response) {
        console.log(response);
        var data  = response.data;
        if(data.status == 1){
          alert(data.message);
          document.querySelector('.coupon_code').disabled = true;
          this.discount_percentage = data.discount;
          this.getPickUpGrandTotal();
        }else{
          alert(data.message)
          console.log('in here')
          this.discount_percentage = 0;
          this.getPickUpGrandTotal();
        }
      }).catch(function (error) {
        console.log(error);
      });
    }

  }
}
</script>
