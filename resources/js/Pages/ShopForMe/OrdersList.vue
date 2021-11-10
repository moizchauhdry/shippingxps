<template>
  <MainLayout>
    <div class="card mt-4">
      <div class="card-body">
        <FlashMessages />
        <div class="row">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
            Manage Shopping List
          </h2>
        </div>
        <div class="row" style="margin-top:30px;">
          <div class="col-md-6" >
            <form action="/shop-for-me" method="get">
              <input type="text" name="search" class="form-control" placeholder="Search">
            </form>
          </div>
          <div class="col-md-6" >
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              <template v-if="$page.props.auth.user.type == 'customer'">
                <inertia-link :href="route('shop-for-me.create')" class="btn btn-success float-right">Add New Shopping</inertia-link>
              </template>
            </h2>
          </div>
        </div>

        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Warehouse Name</th>
              <th scope="col">Customer Name</th>
              <th scope="col">Order Type</th>
              <th scope="col">Status</th>
              <th scope="col">Site Name</th>
              <th scope="col">Site URL</th>
              <!-- <th scope="col">Shipping From Shop</th> -->
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders.data" :key="order.id">
              <td>{{ order.id }}</td>
              <td>{{ order.warehouse.name }}</td>
              <td v-if="order.customer">{{ order.customer.name }}</td>
              <td class="capitalize">{{ order.order_origin }}</td>
              <td class="capitalize"> <span v-bind:class="getLabelClass(order.status)" >{{ order.status }}</span></td>
              <td>
                  <template v-if="order.site_name !== null">
                    <span v-if="order.site_name.length <30 ">{{ order.site_name }}</span>
                    <span v-else>Welcome, {{ order.site_name.substring(0,30)+ "..." }}</span>
                  </template>
              </td>
              <td>
                <template v-if="order.site_url !== null">
                <a target="_blank" class="link-primary" :href="'//' + order.site_url" >
                  <span v-if="order.site_url.length<30">{{ order.site_url != null ? order.site_url : '- -' }}</span>
                  <span v-else>Welcome, {{ order.site_url.substring(0,30)+ "..." }}</span>
                </a>  
                </template>
              </td>
              <!-- <td>{{ order.shipping_from_shop }}</td> -->
              <td style="min-width:70px;">
                <inertia-link class="link-primary" :href="route('shop-for-me.show', order.id)">
                  <span><i class="fa fa-eye"></i></span>
                </inertia-link>
                <template v-if="order.status == 'pending' || $page.props.auth.user.type =='admin'">
                &nbsp;|&nbsp;
                  <inertia-link class="link-primary" :href="route('shop-for-me.edit', order.id)">
                    <span><i class="fa fa-pencil-alt"></i></span>
                  </inertia-link>
                </template>
                <template v-if="order.status == 'labeled' && order.order_type=='package'">
                  &nbsp;|&nbsp;
                  <a target= '_blank' class="link-success" :href="route('packages.pdf', order.id)" title="Invoice">
                    <i class="fa fa-file"></i>
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
  },
  data() {
    return {
      form: this.$inertia.form({
        search: ''
      }),
      params:{
        search:null,
      },
    }
  },
  watch:{
    params:{
      handler(){
        this.$inertia.get(this.route('shop-for-me'),this.params,{replace:true,preserveState:true});
      }
    }
  },
  methods:{
    getLabelClass(status){
      switch(status) {
        case 'pending':
          return 'label bg-warning';
        break;
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
