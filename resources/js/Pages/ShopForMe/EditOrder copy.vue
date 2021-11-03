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

              <form @submit.prevent="submit" enctype="multipart/form-data">
                <div class="order-form">
                  <breeze-validation-errors class="mb-4" />
                  <flash-messages class="mb-4" />

                  <div :class="getTabPaneClass('tab1')" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <breeze-label for="warehouse_id" value="Warehouse" />
                          <select name="warehouse_id" class="form-select" v-model="form.warehouse_id" :required="setRequired('tab1')">
                            <option selected>Select</option>
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
                          <input v-model="form.site_name" name="site_name" id="site_name" type="text" class="form-control" placeholder="Site Name" :required="setRequired('tab1')"/>
                        </div>

                        <div class="form-group">
                          <breeze-label for="package_length" value="Shop URL" />
                          <input v-model="form.shop_url" name="shop_url" id="shop_url" type="url" class="form-control" placeholder="Shop URL" :required="setRequired('tab1')"/>
                        </div>

                      </div>

                      <div  class="col-md-4">
                        <div class="form-group">
                          <breeze-label for="notes" value="Notes" />
                          <textarea v-model="form.note"
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
                  </div>

                  <div :class="getTabPaneClass('tab2')" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <breeze-label for="warehouse_id" value="Warehouse" />
                          <select name="warehouse_id" class="form-select" v-model="form.warehouse_id" @change="filterStores()" required>
                            <option selected>Select</option>
                            <option v-for="warehouse in warehouses" :value="warehouse.id"  :key="warehouse.id" >{{ warehouse.name}}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <breeze-label for="store_id" value="Store" />
                          <select name="store_id" class="form-select" v-model="form.store_id" :required="setRequired('tab2')">
                            <option selected>Select</option>
                            <option v-for="store in stores" :value="store.id"  :key="store.id" >{{ store.name }}</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div  class="col-md-12">
                        <div class="form-group">
                          <breeze-label for="notes" value="Notes" />
                          <textarea v-model="form.note"
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
                      <div class="col">
                        <breeze-label for="only_pickup" value="Only Pickup" />
                        <input type="radio" v-model="form.only_pickup" name="only_pickup" value="1" :required="setRequired('tab2')"> Yes
                        <input type="radio" v-model="form.only_pickup" name="only_pickup" value="0" :required="setRequired('tab2')"> No
                      </div>
                      <div class="col">
                        <breeze-label for="shipping_xps" value="Shipping XPS Purchase" />
                        <input type="radio" v-model="form.shipping_xps" name="shipping_xps" value="1" :required="setRequired('tab2')"> Yes
                        <input type="radio" v-model="form.shipping_xps" name="shipping_xps" value="0" :required="setRequired('tab2')"> No
                      </div>
                      <div class="col">
                        <breeze-label for="pickup_date" value="Pickup Date" />
                        <input type="date" v-model="form.pickup_date" name="pickup_date" class="form-control" placeholder="Pickup Data" :required="setRequired('tab2')">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col form-group">
                      <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">Additional Charges</h2>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <breeze-label for="package_weight" value="Shipping From Shop" />
                        <input v-model="form.shipping_from_shop" name="shipping_from_shop" id="shipping_from_shop" type="text" class="form-control" placeholder="Shipping From Shop" required />
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group">
                        <breeze-label for="package_length" value="Sales Tax" />
                        <input v-model="form.sales_tax" name="sales_tax" id="sales_tax" type="number" class="form-control" placeholder="Sales Tax" required />
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
                        <div class="col-md-2">
                          <breeze-label for="name" value="Name" />
                        </div>
                        <div class="col-md-2">
                          <breeze-label for="option" value="Option" />
                        </div>
                        <div class="col-md-2">
                          <breeze-label for="url" value="Url" />
                        </div>
                        <div class="col-md-2">
                          <breeze-label for="price" value="Price" />
                        </div>
                        <div class="col-md-2">
                          <breeze-label for="price_with_tax" value="Price after Tax" />
                        </div>
                        <div class="col-md-1">
                          <breeze-label for="qty" value="Qty" />
                        </div>
                      </div>

                      <div v-for="(item,index) in form.items" :key="item.id" class="row">

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

                        <div class="col-md-2">
                          <div class="form-group">
                            <input v-model="item.url" name="url" id="url" type="url" class="form-control" placeholder="URL" required />
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <input v-model="item.price" name="price" id="price" type="number" class="form-control" placeholder="Price" required />
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <input v-model="item.price_with_tax" name="price_with_tax" id="price_with_tax" type="number" class="form-control" placeholder="Price After Tax" required />
                          </div>
                        </div>
                        <div class="col-md-1">
                          <div class="form-group">
                            <input v-model="item.qty" name="qty" id="qty" type="number" class="form-control" placeholder="Qty" required />
                          </div>
                        </div>
                        <div class="col-md-1" v-show="index!=0">
                          <div class="form-group">
                            <a v-on:click="removeItem(index)" class="btn btn-primary">
                              <span>Remove</span>
                            </a>
                          </div>
                        </div>

                      </div>

                    </div>

                  </div>

                  <div class="order-button">
                    <input type="submit" value="Update Shopping" class="btn btn-danger" />

                    <template v-if="$page.props.auth.user.type == 'admin' && form.status == 'pending'">
                      <a v-on:click="changeToCompleteShopping()" class="btn btn-primary float-right">
                        <span>Complete Shopping</span>
                      </a>
                    </template>
                  </div>
                </div>

              </form>
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
      form: this.$inertia.form({
        form_type: (this.order.order_type == 'shopping') ? 'shop' : 'pickup',
        id:this.order.id,
        warehouse_id: this.order.warehouse_id,
        store_id: this.order.store_id,
        site_name: this.order.site_name,
        shop_url: this.order.site_url,
        status: this.order.status,
        note:  this.order.notes,
        shipping_from_shop:  this.order.shipping_from_shop,
        sales_tax: this.order.sales_tax,
        order_origin: this.order.order_origin,
        items: this.order.items,
        only_pickup: (this.order.order_type == 'shopping') ? '' : this.order.only_pickup,
        shipping_xps: (this.order.order_type == 'shopping') ? '' : this.order.shipping_xps,
        pickup_date: (this.order.order_type == 'shopping') ? '' : this.order.pickup_date,
        is_complete_shopping: 0
      }),
      tabs : {
        tab1: (this.order.order_type == 'shopping') ? true : false,
        tab2: (this.order.order_type == 'pickup') ? true : false
      },
      stores: []
    };
  },
  props: {
    auth: Object,
    warehouses: Object,
    order:Object
  },
  mounted() { 
    this.filterStores();    
  },
  created(){
    if(this.form.form_type ==='pickup'){
      this.setActiveTabAB('tab2'); 
    }else{
      this.setActiveTabAB('tab1'); 
    } 
  },
  methods : {
    submit() {
      this.form.post(this.route('shop-for-me.update'))
    },

    changeToCompleteShopping() {
      this.form.is_complete_shopping = 1;
      this.submit();
    },

    addItem(){
      this.form.items.push({
        name: "",
        option: "",
        url: "",
        price: "",
        price_with_tax: "",
        qty: ""
      })
    },

    removeItem(index){
      this.form.items.splice(index, 1);
    },

    setActiveTabAB(tab){
      for (var key in this.tabs) {
        if(key === tab){
          this.tabs[key] = true;
        }else{
          this.tabs[key] = false;
        }
        // to handle different form submit behaviour for the same form
        if (tab === 'tab1') {
          this.form.form_type = 'shop';
        } else {
          this.form.form_type = 'pickup';
        }
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
        warehouse_id: this.form.warehouse_id
      };
      axios.get("/shop-for-me/filter-stores/" + this.form.warehouse_id)
      .then(({ data }) => {
            this.stores = data.stores;
          }
      );
    }
  }
}
</script>
