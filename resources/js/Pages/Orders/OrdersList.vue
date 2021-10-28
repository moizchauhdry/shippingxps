<template>
  <MainLayout>
    <div class="card mt-4">
      <div class="card-body">
        <FlashMessages />
        <div class="row">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
            Manage Orders
          </h2>
        </div>
        <div class="row" style="margin-top:30px;">
            <div class="col-md-9" >
            <form action="/orders" method="get">
                <div class="row">
                  <div class="col-md-5" >
                    <input type="text" name="search" :value="form.search" class="form-control" placeholder="Tracking #">
                  </div>
                  <div class="col-md-5" >
                    <select name="customer_id" class="form-select" v-model="form.customer_id">                      
                      <option value="">All</option>                
                      <option v-for="customer in customers" :value="customer.id" :key="customer.id" >{{ customer.name}}</option>
                    </select>
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
                            v-if="$page.props.auth.user.type == 'admin'"
                            class="btn btn-success float-right">Add New Order</inertia-link>
            </h2>
          </div>
        </div>

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
import FlashMessages from '@/Components/FlashMessages'
export default {
  components: {
    BreezeAuthenticatedLayout,
    MainLayout,
    FlashMessages
  },
  props: {
    auth: Object,
    orders:Object,
    search:String,
    customers:Object,
    customer_id:String,
  },
  data() {
    return {
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
    }
  }
}
</script>
