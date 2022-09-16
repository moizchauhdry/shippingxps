<template>
  <MainLayout>
    <div>

      <section>
        <div class="container">

          <div v-if="form.updated_by_admin == '1' && $page.props.auth.user.type == 'customer'" class="alert alert-warning d-block">
            Admin Has updated or Changed you order please review and approve changes
          </div>

          <div v-if="form.changes_approved == '1' && $page.props.auth.user.type == 'customer'" class="alert alert-success d-block">
            You have approved changes
          </div>

          <template v-if="$page.props.auth.user.type == 'admin'">
            <div v-if="form.changes_approved == '1'" class="alert alert-success d-block">
              Customer has approved, you can complete shopping
            </div>
            <div v-else-if="form.changes_approved == '0'" class="alert alert-warning d-block">
              Customer has not approved the order please wait until customer approves
            </div>
          </template>

          <ul class="nav nav-pills nav-justified mb-3" id="pills-tab " role="tablist">
            <li class="nav-item" role="presentation" v-show="form.form_type == 'shopping'">
              <button
                  v-on:click="setActiveTabAB('tab1')"
                  :class="getTabClass('tab1')"
                  id="pills-home-tab"
                  data-bs-toggle="pill"
                  data-bs-target="#pills-home"
                  type="button"
                  role="tab"
                  aria-controls="pills-home"
                  aria-selected="true">Online Order
              </button>
            </li>
            <li class="nav-item" role="presentation" v-show="form.form_type != 'shopping'">
              <button
                  v-on:click="setActiveTabAB('tab2')"
                  :class="getTabClass('tab2')"
                  id="pills-profile-tab"
                  data-bs-toggle="pill"
                  data-bs-target="#pills-profile"
                  type="button"
                  role="tab"
                  aria-controls="pills-profile"
                  aria-selected="false">Pickup Order
              </button>
            </li>
          </ul>
          <div class="tab-content" id="pills-tabContent">
            <div class="stock-subscription-form">

              <form @submit.prevent="submit" enctype="multipart/form-data">
                <div class="order-form">
                  <breeze-validation-errors class="mb-4"/>
                  <flash-messages class="mb-4"/>

                  <div :class="getTabPaneClass('tab1')" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <breeze-label for="warehouse_id" value="Warehouse"/>
                          <select name="warehouse_id" class="form-select" v-model="form.warehouse_id" :required="setRequired('tab1')" @change="wareHouseChangeOnline()">
                            <option selected>Select</option>
                            <option v-for="warehouse in warehouses" :value="warehouse.id" :key="warehouse.id">{{ warehouse.name }}</option>
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
                          <breeze-label for="package_weight" value="Site Name"/>
                          <input v-model="form.site_name" name="site_name" id="site_name" type="text" class="form-control" placeholder="Site Name" :required="setRequired('tab1')"/>
                        </div>

                        <div class="form-group">
                          <breeze-label for="package_length" value="Shop URL"/>
                          <input v-model="form.shop_url" name="shop_url" id="shop_url" type="url" class="form-control" placeholder="Shop URL" :required="setRequired('tab1')"/>
                        </div>

                      </div>

                      <div class="col-md-4">
                        <div class="form-group">
                          <breeze-label for="notes" value="Notes"/>
                          <textarea v-model="form.notes"
                                    name="notes"
                                    id="notes"
                                    class="form-control"
                                    placeholder="Notes"
                                    rows="5"
                                    style="resize:none;"
                          >
                          </textarea>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div :class="getTabPaneClass('tab2')" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <breeze-label for="warehouse_id" value="Warehouse"/>
                          <select name="warehouse_id" class="form-select" v-model="form.warehouse_id" @change="filterStores()" required>
                            <option selected>Select</option>
                            <option v-for="warehouse in warehouses" :value="warehouse.id" :key="warehouse.id">{{ warehouse.name }}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <breeze-label for="store_id" value="Store"/>
                          <select name="store_id" class="form-select" v-model="form.store_id" :required="setRequired('tab2')" v-on:change="setPickupCharges($event)">
                            <option selected>Select</option>
                            <option v-for="store in stores" :value="store.id" :key="store.id">{{ store.name }}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-2" v-show="this.form.store_id !==''">
                        <div class="form-group">
                          <breeze-label for="pickup_charges" value="Pickup Charges (USD)"/>
                          <input type="text" name="pickup_charges" id="pickup_charges" v-model="form.pickup_charges" readonly>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <breeze-label for="notes" value="Notes"/>
                          <textarea v-model="form.notes"
                                    name="notes"
                                    id="note"
                                    class="form-control"
                                    placeholder="Notes"
                                    rows="3"
                                    style="resize:none;"
                          >
                                </textarea>
                        </div>
                      </div>
                    </div>
                    <div class="row mb-2">
                      <div class="col-md-8">
                        <input type="radio" @change="getGrandTotal(1)" v-model="form.pickup_type" name="pickup_type" value="pickup_only" :required="setRequired('tab2')" :checked="form.pickup_type == 'pickup_only'"> Only Pickup
                        <input type="radio" @change="getGrandTotal(1)" v-model="form.pickup_type" name="pickup_type" value="shipping_xps_purchase" :required="setRequired('tab2')" :checked="form.pickup_type == 'shipping_xps_purchase'"> Shipping XPS Purchase
                      </div>
                      <!-- <div class="col">
                        <breeze-label for="only_pickup" value="Only Pickup" />
                        <input type="radio" v-model="form.only_pickup" name="only_pickup" value="1" :required="setRequired('tab2')"> Yes
                        <input type="radio" v-model="form.only_pickup" name="only_pickup" value="0" :required="setRequired('tab2')"> No
                      </div>
                      <div class="col">
                        <breeze-label for="shipping_xps" value="Shipping XPS Purchase" />
                        <input type="radio" v-model="form.shipping_xps" name="shipping_xps" value="1" :required="setRequired('tab2')"> Yes
                        <input type="radio" v-model="form.shipping_xps" name="shipping_xps" value="0" :required="setRequired('tab2')"> No
                      </div>-->
                      <div class="col">
                        <breeze-label for="pickup_date" value="Pickup Date"/>
                        <input type="date" v-model="form.pickup_date" name="pickup_date" class="form-control" placeholder="Pickup Data" :required="setRequired('tab2')">
                      </div>
                    </div>
                  </div>
                  <template v-if="$page.props.auth.user.type == 'admin'">
                    <div class="row">
                      <div class="col form-group">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">Additional Charges</h2>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <breeze-label for="package_weight" value="Shipping Charges"/>
                          <input v-model="form.shipping_from_shop" name="shipping_from_shop" @keyup="getGrandTotal" id="shipping_from_shop" type="number" step="any" min="0" class="form-control" placeholder="Shipping Charges" required/>
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <breeze-label for="package_length" value="Sales Tax"/>
                          <input v-model="form.sale_tax" name="sales_tax" id="sales_tax" type="number" class="form-control" placeholder="Sales Tax" required readonly/>
                        </div>

                      </div>
                    </div>
                  </template>

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

                      <div class="row mt-3">
                        <div class="col">
                          <breeze-label for="name" value="Name"/>
                        </div>
                        <div class="col">
                          <breeze-label for="description" value="Description"/>
                        </div>
                        <div class="col" v-show="form.form_type == 'shopping'">
                          <breeze-label for="url" value="Url"/>
                        </div>
                        <div class="col-md-1" v-show="form.pickup_type != 'pickup_only'">
                          <breeze-label for="price" value="Price"/>
                        </div>
                        <div class="col-md-2" v-show="form.pickup_type != 'pickup_only'">
                          <breeze-label for="price_with_tax" value="Price after Tax"/>
                        </div>
                        <div class="col">
                          <breeze-label for="qty" value="Qty"/>
                        </div>
                        <div class="col-md-2" v-show="form.pickup_type != 'pickup_only'">
                          <breeze-label for="total" value="Total"/>
                        </div>
                        <div class="col-md-1">
                        </div>
                      </div>

                      <div v-for="(item,index) in form.items" :key="item.id" class="row" :id="'order-'+index" :data-id="index">

                        <div class="col">
                          <div class="form-group">
                            <input v-model="item.name" name="name" id="name" type="text" class="form-control name" placeholder="Name" required/>
                          </div>
                        </div>
                        <div class="col">
                          <div class="form-group">
                            <input v-model="item.description" name="description" id="description" type="text" class="form-control option" placeholder="Option"/>
                          </div>
                        </div>

                        <div class="col" v-show="form.form_type == 'shopping'">
                          <div class="form-group">
                            <input v-model="item.url" name="url" id="url" type="url" class="form-control url" placeholder="URL" :required="form.form_type === 'shopping'"/>
                          </div>
                        </div>
                        <div class="col-md-1 p-0" v-show="form.pickup_type!='pickup_only'">
                          <div class="form-group">
                            <input v-model="item.price" v-on:change="addTax($event)" v-on:keyup="addTax($event)" v-on:click="addTax($event)" :min="0" ref="price" name="price" step="0.01" type="number" class="form-control price" placeholder="Price" required/>
                          </div>
                        </div>
                        <div class="col-md-2" v-show="form.pickup_type!='pickup_only'">
                          <div class="form-group">
                            <input v-model="item.price_with_tax" name="price_with_tax" id="price_with_tax" type="number" class="form-control price_with_tax" placeholder="Price After Tax" required readonly/>
                          </div>
                        </div>
                        <div class="col">
                          <div class="form-group">
                            <input v-model="item.qty" name="qty" v-on:change="addTax($event)" id="qty" type="number" class="form-control qty" placeholder="Qty" :min="1" required/>
                          </div>
                        </div>
                        <div class="col-md-2 p-0" v-show="form.pickup_type!='pickup_only'">
                          <div class="form-group">
                            <input v-model="item.sub_total" name="sub_total" id="sub_total" type="number" class="form-control sub_total" placeholder="T.Price" required readonly/>
                          </div>
                        </div>
                        <div class="col-md-1">
                          <div class="form-group" v-show="index!=0">
                            <a v-on:click="removeItem(index)" class="btn btn-primary">
                              <span>Remove</span>
                            </a>
                          </div>
                        </div>

                      </div>

                      <!-- sub_total -->
                      <div class="row mb-2" v-show="form.pickup_type != 'pickup_only'">
                        <div class="col-2 offset-md-8">
                          <breeze-label class="float-right" for="form.subtotal" value="Sub Total"/>
                        </div>
                        <div class="col-1 p-0">
                          <input v-model="form.sub_total" name="sub_total" id="form.subtotal" type="number" class="form-control sub_total" placeholder="T.Price" required readonly/>
                        </div>
                      </div>
                      <!-- Store Charges -->
                      <div class="row mb-2" v-if="form.pickup_type === 'shipping_xps_purchase'">
                        <div class="col-2 offset-md-8">
                          <breeze-label class="float-right" for="store_charges" value="Tax"/>
                        </div>
                        <div class="col-1 p-0">
                          <input v-model="form.store_charges" name="store_charges" id="store_charges" type="text" class="form-control store_charges" placeholder="Storage Charges"  readonly/>
                        </div>
                      </div>
                      <!-- discount -->
                      <div class="row mb-2" v-show="form.shipping_from_shop != null && form.shipping_from_shop != 0">
                        <div class="col-1 offset-md-9">
                          <breeze-label class="float-right" for="shipping_from_shop" value="Shipping From Shop"/>
                        </div>
                        <div class="col-1 p-0">
                          <input v-model="form.shipping_from_shop" name="shipping_from_shop" type="number" class="form-control discount" placeholder="shipping charges" readonly/>
                        </div>
                      </div>
                      <!-- pickup_charges -->
                      <div class="row mb-2" v-show="form.form_type != 'shopping'">
                        <div class="col-2 offset-md-8">
                          <breeze-label class="float-right" for="pickup_charges" value="Pickup Charges"/>
                        </div>
                        <div class="col-1 p-0">
                          <input v-model="form.pickup_charges" name="pickup_charges" id="pickup_charges" type="number" class="form-control pickup_charges" placeholder="pickup charges" required readonly/>
                        </div>
                      </div>
                      <!-- service_charges -->
                      <div class="row mb-2" v-show="form.pickup_type != 'pickup_only'">
                        <div class="col-2 offset-md-8">
                          <breeze-label class="float-right" for="service_charges" value="Services Charges"/>
                          <br><small class="float-right">Minimum USD 5 Or 5% of Subtotal</small>
                        </div>
                        <div class="col-1 p-0">
                          <input v-model="form.service_charges" name="service_charges" id="service_charges" step="0.01" type="number" class="form-control service_charges" @keyup="adminServicesCharges()" placeholder="charges" :min="0" required :readonly="$page.props.auth.user.type == 'customer'"/>

                        </div>
                      </div>
                      <!-- Box Price -->
                      <div class="row mb-2" v-show="this.form.form_type !== 'shopping'">
                        <div class="col-2 offset-md-8">
                          <breeze-label class="float-right" for="additional_pickup_charges" value="Box Price"/>
                        </div>
                        <div class="col-1 p-0">
                          <input v-model="additional_pickup_charges" name="additional_pickup_charges" id="additional_pickup_charges" type="text" class="form-control additional_pickup_charges" placeholder="T.Price" required readonly/>
                        </div>
                      </div>

                      <!-- grand_total -->
                      <div class="row mb-2">
                        <div class="col-2 offset-md-8">
                          <breeze-label class="float-right" for="grand_total" value="Grand Total"/>
                        </div>
                        <div class="col-1 p-0">
                          <input v-model="form.grand_total" name="grand_total" id="grand_total" type="text" class="form-control grand_total" placeholder="T.Price" required readonly/>
                        </div>
                      </div>

                    </div>
                  </div>

                  <fieldset class="border p-2 mb-2" v-if="$page.props.auth.user.type == 'admin' && form.changes_approved == '1'">
                    <div class="row">
                      <div class="col-md-4 form-group">
                        <label for="receipt_url">Image </label><small>(receipts,invoice,doc, etc.)</small>
                        <input type="file" class="form-control" name="receipt_url" id="receipt_url" accept=".png,.jpg,.jpeg,.pdf,.docx,.xls,.xlsx" @input="form.receipt_url = $event.target.files[0]">
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                          {{ form.progress.percentage }}%
                        </progress>
                        <img :src="imgURL(form.receipt_url)" alt="">

                      </div>
                    </div>
                  </fieldset>
                  <div class="row" v-if="$page.props.auth.user.type == 'customer'">
                    <div class="col-md-4 text-center form-group">
                      <img :src="imgURL(form.receipt_url)" alt="">
                    </div>
                  </div>
                  <div class="order-button">
                    <input v-if="order.payment_status != 'Paid' && ($page.props.auth.user.type == 'admin'  || $page.props.auth.user.type == 'manager')" type="submit" value="Update Shopping" class="btn btn-danger"/>
                    <input v-if="order.payment_status != 'Paid'  && $page.props.auth.user.type == 'customer'" type="button"  v-on:click="updateChanges()"  value="Update Shopping" class="btn btn-danger"/>
                    <template v-if="order.payment_status != 'Paid' && $page.props.auth.user.type == 'customer' && form.updated_by_admin == '1'">
                      <a class="btn btn-primary ml-2" v-on:click="approveChanges()">Approve & Checkout</a>
                    </template>
                    <template v-if="order.payment_status != 'Paid' && $page.props.auth.user.type == 'customer' && order.changes_approved == '1'">
                      <a class="btn btn-primary ml-2" v-on:click="approveChanges()">Approve & Checkout</a>
                    </template>

                    <template v-if="form.changes_approved == '1' && ($page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager')">
                      <template v-if="($page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager') && form.status == 'pending'">
                        <a v-on:click="changeToCompleteShopping()" class="btn btn-primary float-right">
                          <span>Complete Shopping</span>
                        </a>
                      </template>
                    </template>
                    <template v-else-if="($page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager')">
                      <a class="btn btn-primary float-right disabled" style="cursor: not-allowed">
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
import BreezeValidationErrors from '@/Components/ValidationErrors'

export default {
  components: {
    BreezeAuthenticatedLayout,
    MainLayout,
    BreezeLabel,
    BreezeValidationErrors
  },
  data() {
    console.log(this.order.updated_by_admin)
    return {
      form: this.$inertia.form({
        form_type: (this.order.order_type == 'shopping' ) ? 'shopping' : 'pickup',
        id: this.order.id,
        warehouse_id: this.order.warehouse_id,
        store_id: this.order.store_id,
        store_charges: this.order.store_charges,
        store_tax: this.order.store_tax,
        site_name: this.order.site_name,
        shop_url: this.order.site_url,
        status: this.order.status,
        notes: this.order.notes,
        order_origin: this.order.order_origin,
        items: this.order.items,
        pickup_type: this.order.pickup_type,
        pickup_charges: this.order.pickup_charges == null ? 0 : this.order.pickup_charges,
        only_pickup: (this.order.order_type == 'shopping') ? '' : this.order.only_pickup,
        shipping_xps: (this.order.order_type == 'shopping') ? '' : this.order.shipping_xps,
        pickup_date: (this.order.order_type == 'shopping') ? '' : this.order.pickup_date,
        is_complete_shopping: 0,
        is_changed: this.order.is_changed,
        updated_by_admin: this.order.updated_by_admin,
        changes_approved: this.order.changes_approved,
        shipping_from_shop: this.order.shipping_from_shop != null ? this.order.shipping_from_shop : 0,
        sales_tax: this.order.sales_tax,
        discount: (this.order.discount != null) ? this.order.discount : 0,
        service_charges: this.order.service_charges,
        shipping_charges: this.order.shipping_charges,
        grand_total: this.order.grand_total,
        sub_total: this.order.sub_total,
        sale_tax: 0,
        receipt_url: this.order.receipt_url,
        is_service_charges: this.order.is_service_charges,
      }),
      tabs: {
        tab1: (this.order.order_type == 'shopping') ? true : false,
        tab2: (this.order.order_type == 'pickup') ? true : false
      },
      stores: []
    };
  },
  props: {
    errors: Object,
    auth: Object,
    warehouses: Object,
    order: Object,
    salePrice: Object,
    additional_pickup_charges:Number,
  },
  mounted() {
    this.filterStores();

    if (this.$page.props.auth.user.type === 'admin') {
      this.getSaleTax();
    }
  },

  created() {
    if (this.form.form_type === 'shopping') {
      this.setActiveTabAB('tab1');
    } else {
      this.setActiveTabAB('tab2');
    }
    // this.getGrandTotal();
  },
  methods: {
    submit() {
      this.form.post(this.route('shop-for-me.update'));
    },
    adminServicesCharges() {
      if (this.$page.props.auth.user.type === 'admin') {
        console.log("adminServicesCharges() triggered...")
        console.log(this.form.is_service_charges)
        this.form.is_service_charges = 1;
        console.log(this.form.is_service_charges)
        this.getGrandTotal(0);
      }
    },
    updateChanges() {
      this.form.changes_approved = 0;
      this.submit();
    },
    changeToCompleteShopping() {
      this.form.is_complete_shopping = 1;
      this.submit();
    },
    approveChanges() {
      this.form.changes_approved = 1;
      this.submit();
    },
    addItem() {
      this.form.items.push({
        name: "",
        description: "",
        url: "",
        price: 0,
        price_with_tax: 0,
        qty: 1,
        sub_total: 0
      })

    },
    removeItem(index) {
      this.form.items.splice(index, 1);
    },
    setActiveTabAB(tab) {
      console.log(tab)
      for (var key in this.tabs) {
        if (key === tab) {
          this.tabs[key] = true;
        } else {
          this.tabs[key] = false;
        }
        // to handle different form submit behaviour for the same form
        if (tab === 'tab1') {
          this.form.form_type = 'shopping';
        } else {
          this.form.form_type = 'pickup';
        }
      }
    },
    getTabClass(tab) {

      if (this.tabs[tab] === true) {
        return 'nav-link active';
      } else {
        return 'nav-link';
      }

    },
    setRequired(tab) {
      return this.tabs[tab];
    },
    getTabPaneClass(tab) {

      if (this.tabs[tab] === true) {
        return 'tab-pane show active';
      } else {
        return 'tab-pane fade d-none';
      }
    },
    filterStores() {
      const params = {
        warehouse_id: this.form.warehouse_id
      };
      console.log(this.$refs);
      this.$refs.price[0].click();

      axios.get("/shop-for-me/filter-stores/" + this.form.warehouse_id)
          .then(({data}) => {
                this.stores = data.stores;
              }
          );
    },
    wareHouseChangeOnline() {
      console.log(this.$refs);
      this.$refs.price[0].click();
    },
    addTax(event) {
      var mainParent = event.target.parentNode.parentNode.parentNode;
      var row = document.getElementById(mainParent.id);
      var index = row.dataset.id;
      var quantity = this.form.items[index].qty;
      var price = this.form.items[index].price;
      var sale_tax = 0;
      for (var i = 0; i < this.warehouses.length; i++) {
        if (this.warehouses[i]['id'] == this.form.warehouse_id) {
          sale_tax = this.warehouses[i]['sale_tax'];
        }
      }
      var gross_total = (price * (sale_tax / 100));
      var net_total = this.form.items[index].price_with_tax = (parseFloat(gross_total) + parseFloat(price)).toFixed(2);
      this.form.items[index].sub_total = parseFloat(net_total * quantity).toFixed(2);
      this.getGrandTotal(1);
    },
    getGrandTotal(e) {
      var pickup_charges = this.form.pickup_charges;
      var additionalCharges = parseFloat(this.additional_pickup_charges).toFixed(2);

      if (this.form.pickup_type == 'pickup_only') {
        this.form.grand_total = parseFloat(pickup_charges).toFixed(2);
        console.log('inHere');
        console.log(pickup_charges);
        console.log(this.form.grand_total);
      } else {
        var sum = 0;
        this.form.items.forEach(function (n) {
          sum += parseFloat(n['sub_total']);
        });
        let storeCharges = 0;
        this.form.store_charges = 0.00
        if(this.form.pickup_type === 'shipping_xps_purchase'){
          storeCharges = sum * (this.form.store_tax / 100);
          this.form.store_charges = storeCharges;
        }
        this.form.sub_total = parseFloat(sum).toFixed(2);
        if (e == 1 && this.form.is_service_charges != 1) {
          var serviceCharges = sum * 0.05
          if(serviceCharges <= 5 && serviceCharges > 0.01){
            serviceCharges = this.form.service_charges = 5.00;
          }else{
            serviceCharges = this.form.service_charges = parseFloat(serviceCharges).toFixed(2);
          }
        } else {
          serviceCharges = this.form.service_charges
        }
        var charges = parseFloat(this.form.shipping_from_shop);
        this.form.shipping_charges = parseFloat(charges).toFixed(2)
        pickup_charges = this.form.form_type === 'shopping' ? 0 : pickup_charges;
        additionalCharges = this.form.form_type === 'shopping' ? 0 : additionalCharges;
        var total = parseFloat(sum) + parseFloat(charges) + parseFloat(pickup_charges) + parseFloat(serviceCharges) + parseFloat(additionalCharges) + parseFloat(storeCharges);
        this.form.grand_total = parseFloat(total).toFixed(2);
      }
    },
    setPickupCharges(event) {
      var store_id = event.target.value;
      var pickup_charges = 0;
      var tax = 0;
      for (var i = 0; i < this.stores.length; i++) {
        if (this.stores[i]['id'] == store_id) {
          pickup_charges = this.stores[i]['pickup_charges'];
          tax = this.stores[i]['tax'];
        }
      }
      this.form.pickup_charges = pickup_charges;
      this.form.store_tax = tax;

      this.getGrandTotal(1);
    },
    getSaleTax() {
      var sale_tax = 0;
      for (var i = 0; i < this.warehouses.length; i++) {
        if (this.warehouses[i]['id'] == this.form.warehouse_id) {
          sale_tax = this.warehouses[i]['sale_tax'];
        }
      }
      this.form.sale_tax = sale_tax;
    },
    imgURL(url) {
      return "/public/uploads/" + url;
    },
  }
}
</script>
