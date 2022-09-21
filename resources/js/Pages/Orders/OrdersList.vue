<template>
  <MainLayout>
    <div class="card mt-4">
      <div class="card-body">

        <div class="row">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
            Manage Orders
          </h2>
        </div>
        <div class="row" style="margin-top:30px;">
            <div class="col-md-9" >
            <form method="get">
                <div class="row">
                  <div class="col-md-5" >
                    <input type="text" name="search" :value="form.search" class="form-control" placeholder="Search #">
                  </div>
                  <div class="col-md-2" >
                    <input type="submit" value="Search" class="btn btn-primary">
                  </div>
                </div>
            </form>
            </div>
          <div class="col-md-3" >
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              <inertia-link :href="route('orders.create')"
                            v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'"
                            class="btn btn-success float-right">Add New Order</inertia-link>
            </h2>
          </div>
        </div>

        <template v-if="$page.props.auth.user.type == 'customer'">
          <table class="table table-striped">
            <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Tracking #</th>
              <th scope="col">Customer</th>
              <th scope="col">Received At</th>
              <th scope="col">Status</th>
              <th scope="col">Order Type</th>
              <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="order in orders.data" :key="order.id">
              <td>{{ order.id }}</td>
              <td>{{ order.tracking_number_in }}</td>
              <td v-if="order.customer">{{ order.customer.name }}</td>
              <td >{{ order.warehouse.name }}</td>
              <td>
                <span v-bind:class="getLabelClass(order.status)">{{ order.status }}</span>
              </td>
              <td class="capitalize">{{ order.order_origin }}</td>
              <td>
                <inertia-link class="link-primary" :href="route('orders.show', order.id)">
                  <span>View</span>
                </inertia-link>
                &nbsp;|&nbsp;
                <inertia-link v-if="$page.props.auth.user.type == 'admin'" class="link-primary" :href="route('order.edit', order.id)">
                  <span>Edit</span>
                </inertia-link>

                <template v-if="order.status == 'arrived' || order.status =='pending'">
                  &nbsp;|&nbsp;
                  <inertia-link class="link-primary" :href="route('orders.destroy', order.id)" method="delete">
                    <span>Delete</span>
                  </inertia-link>
                </template>

                <template v-if="order.status == 'labeled' && order.order_type=='package'">
                  &nbsp;|&nbsp;
                  <a target= '_blank' class="link-success" :href="route('packages.pdf', order.id)">
                    <span>Invoice</span>
                  </a>
                </template>
              </td>
            </tr>
            </tbody>
          </table>
        </template>

        <template v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
          <ul class="nav nav-pills nav-justified mb-3 mt-3" id="pills-tab " role="tablist">
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
                  aria-selected="true">Arrived</button>
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
                  aria-selected="false">Labeled</button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                  v-on:click="setActiveTabAB('tab3')"
                  :class="getTabClass('tab3')"
                  id="pills-contact-tab"
                  data-bs-toggle="pill"
                  data-bs-target="#pills-contact"
                  type="button"
                  role="tab"
                  aria-controls="pills-contact"
                  aria-selected="false">Shipped</button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                  v-on:click="setActiveTabAB('tab4')"
                  :class="getTabClass('tab4')"
                  id="pills-contact-tab"
                  data-bs-toggle="pill"
                  data-bs-target="#pills-contact"
                  type="button"
                  role="tab"
                  aria-controls="pills-contact"
                  aria-selected="false">Trashed/Rejected</button>
            </li>
          </ul>
          <div class="tab-content" id="pills-tabContent">
            <div :class="getTabPaneClass('tab1')" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
              <table class="table table-striped">
                <thead>
                <tr>
                  <th scope="col">Order Id</th>
                  <th v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'" scope="col">
                  Customer Name</th>
                  <th scope="col">Order From</th>
                  <th scope="col">Tracking #</th>
                  <th scope="col">Warehouse</th>
                  <th scope="col">Received Date</th>
                  <th scope="col">Status</th>
                  <template v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
                    <th scope="col"></th>
                  </template>
                </tr>
                </thead>
                <tbody>
                <tr v-for="order in arrived" :key="order.id">
                  <td>{{ order.id }}</td>
                  <td v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
                    <inertia-link :href="route('detail-customer', order.customer.id)" class="btn btn-link">
                    # {{ siuteNum(order.customer.id) }} - {{ order.customer.name }} 
                    </inertia-link>
                  </td>
                  <td>{{ order.received_from }}</td>
                  <td>{{ order.tracking_number_in }}</td>
                  <td>{{ order.warehouse.name }}</td>
                  <td>{{ order.created_at }}</td>
                  <td><span v-bind:class="getLabelClass(order.status)">{{ order.status }}</span></td>
                  <template v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
                    <td>
                      <inertia-link class="link-primary" :href="route('orders.show', order.id)">
                        <span>View</span>
                      </inertia-link>
                      &nbsp;|&nbsp;
                      <inertia-link class="link-primary" :href="route('order.edit', order.id)">
                        <span>Edit</span>
                      </inertia-link>
                    </td>
                  </template>
                </tr>

                </tbody>
              </table>
            </div>
            <div :class="getTabPaneClass('tab2')" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
              <table class="table table-striped">
                <thead>
                <tr>
                  <th scope="col">Order Id</th>
                  <th v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'" scope="col">Customer Name</th>
                  <th scope="col">Order From</th>
                  <th scope="col">Tracking #</th>
                  <th scope="col">Warehouse</th>
                  <th scope="col">Received Date</th>
                  <th scope="col">Status</th>
                  <template v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
                    <th scope="col"></th>
                  </template>
                </tr>
                </thead>
                <tbody>
                <tr v-for="labeler in labeled" :key="labeler.id">
                  <td>{{ labeler.id }}</td>
                  <td v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
                      <inertia-link :href="route('detail-customer', labeler.customer.id)" class="btn btn-link">
                      # {{ siuteNum(labeler.customer.id) }} - {{ labeler.customer.name }} 
                      </inertia-link>
                  </td>
                  <td>{{ labeler.received_from }}</td>
                  <td>{{ labeler.tracking_number_in }}</td>
                  <td>{{ labeler.warehouse.name }}</td>
                  <td>{{ labeler.created_at }}</td>
                  <td><span v-bind:class="getLabelClass(labeler.status)">{{ labeler.status }}</span></td>
                  <template v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
                    <td>
                      <inertia-link class="link-primary" :href="route('orders.show', labeler.id)">
                        <span>View</span>
                      </inertia-link>
                      &nbsp;|&nbsp;
                      <inertia-link class="link-primary" :href="route('order.edit', labeler.id)">
                        <span>Edit</span>
                      </inertia-link>
                    </td>
                  </template>
                </tr>
                </tbody>
              </table>
            </div>
            <div :class="getTabPaneClass('tab3')" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
              <table class="table table-striped">
                <thead>
                <tr>
                  <th scope="col">Order Id</th>
                  <th v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'" scope="col">Customer Name</th>
                  <th scope="col">Order From</th>
                  <th scope="col">Tracking #</th>
                  <th scope="col">Warehouse</th>
                  <th scope="col">Received Date</th>
                  <th scope="col">Status</th>
                  <template v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
                    <th scope="col"></th>
                  </template>
                </tr>
                </thead>
                <tbody>
                <tr v-for="order in shipped" :key="order.id">
                  <td>{{ order.id }}</td>
                  <td v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
                    <inertia-link :href="route('detail-customer', order.customer.id)" class="btn btn-link">
                      # {{ siuteNum(order.customer.id) }} - {{ order.customer.name }} 
                      </inertia-link>
                  </td>
                  <td>{{ order.received_from }}</td>
                  <td>{{ order.tracking_number_in }}</td>
                  <td>{{ order.warehouse.name }}</td>
                  <td>{{ order.created_at }}</td>
                  <td><span v-bind:class="getLabelClass(order.status)">{{ order.status }}</span></td>
                  <template v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
                    <td>
                      <inertia-link class="link-primary" :href="route('orders.show', order.id)">
                        <span>View</span>
                      </inertia-link>
                      &nbsp;|&nbsp;
<!--                      <inertia-link class="link-primary" :href="route('order.edit', order.id)">
                        <span>Edit</span>
                      </inertia-link>-->
                    </td>
                  </template>
                </tr>
                </tbody>
              </table>
            </div>
            <div :class="getTabPaneClass('tab4')" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
              <table class="table table-striped">
                <thead>
                <tr>
                  <th scope="col">Order Id</th>
                  <th v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'" scope="col">Customer Name</th>
                  <th scope="col">Order From</th>
                  <th scope="col">Tracking #</th>
                  <th scope="col">Warehouse</th>
                  <th scope="col">Received Date</th>
                  <th scope="col">Status</th>
                  <template v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
                    <th scope="col"></th>
                  </template>
                </tr>
                </thead>
                <tbody>
                <tr v-for="order in rejected" :key="order.id">
                  <td>{{ order.id }}</td>
                  <td v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
                      <inertia-link :href="route('detail-customer', order.customer.id)" class="btn btn-link">
                      # {{ siuteNum(order.customer.id) }} - {{ order.customer.name }} 
                      </inertia-link>
                  </td>
                  <td>{{ order.received_from }}</td>
                  <td>{{ order.tracking_number_in }}</td>
                  <td>{{ order.warehouse.name }}</td>
                  <td>{{ order.created_at }}</td>
                  <td><span v-bind:class="getLabelClass(order.status)">{{ order.status }}</span></td>
                  <template v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
                    <td>
                      <inertia-link class="link-primary" :href="route('orders.show', order.id)">
                        <span>View</span>
                      </inertia-link>
                      &nbsp;|&nbsp;
                      <inertia-link class="link-primary" :href="route('order.edit', order.id)">
                        <span>Edit</span>
                      </inertia-link>
                    </td>
                  </template>
                </tr>

                </tbody>
              </table>
            </div>
          </div>
        </template>

      </div>
    </div>
  </MainLayout>
</template>
<style scoped>
.label{
  padding:5px;
}
</style>
<script>
import MainLayout from '@/Layouts/Main'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'

export default {
  components: {
    BreezeAuthenticatedLayout,
    MainLayout,
  },
  props: {
    auth: Object,
    orders:Object,
    search:String,
    customers:Object,
    customer_id:String,
    arrived:Object,
    labeled:Object,
    shipped:Object,
    rejected:Object,
    // rejected:Object
  },
  data() {
    return {
      tabs : {
        tab1:true,
        tab2:false,
        tab3:false,
        tab4:false
      },
      form: this.$inertia.form({
        search: this.search,
        customer_id:this.customer_id
      }),
      params:{
        search:null,
        customer_id:'',
      },
    }
  },
  watch:{
    params:{
      handler(){
        this.$inertia.get(this.route('orders'),this.params,{replace:true,preserveState:true});
      }
    }
  },
  methods:{
    getLabelClass(status){
      switch(status) {
        case 'arrived':
          return 'label bg-success';
          break;
        case 'labeled':
          return 'label bg-info';
          break;
        case 'shipped':
          return 'label bg-warning';
          break;
        case 'delivered':
          return 'label bg-success';
          break
        case 'rejected':
          return 'label bg-danger';
          break;
        default:
          return 'label bg-primary';
      }
    },
    setActiveTabAB(tab){

      for (var key in this.tabs) {
        if(key === tab){
          this.tabs[key] = true;
        }else{
          this.tabs[key] = false;
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
    getTabPaneClass(tab){

      if(this.tabs[tab] === true){
        return 'tab-pane show active';
      }else{
        return 'tab-pane fade';
      }
    },
    siuteNum(user_id){
          return 4000 + user_id;
      },
  }
}
</script>
