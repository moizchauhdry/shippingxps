<template>
  <MainLayout>
    <div class="container">
      <div class="card mt-0">
        <div class="card-header">Customer Detail</div>
        <div class="card-body">
          <table class="table table-responsive table-bordered">
            <thead>
            <tr>
              <th colspan="2" class="text-center">Customer Detail</th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <th>Suite #</th>
              <td>{{ suiteNumber(customer.id) }}</td>
            </tr>
            <tr>
              <th>Name</th>
              <td>{{ customer.name }}</td>
            </tr>
            <tr>
              <th>Email</th>
              <td>{{ customer.email }}</td>
            </tr>
            <tr>
              <th>Phone</th>
              <td>{{ customer.phone_no }}</td>
            </tr>
            <tr>
              <th>Country</th>
              <td>{{ customer.country }}</td>
            </tr>
            <tr>
              <th>State</th>
              <td>{{ customer.state }}</td>
            </tr>
            <tr>
              <th>City</th>
              <td>{{ customer.city }}</td>
            </tr>
            <tr>
              <th>Address 1</th>
              <td>{{ customer.address1 }}</td>
            </tr>
            <tr>
              <th>Address 2</th>
              <td>{{ customer.address2 }}</td>
            </tr>
            <tr>
              <th>Zip Code</th>
              <td>{{ customer.postal_code }}</td>
            </tr>
            </tbody>
          </table>

          <!-- <table class="table table-responsive table-bordered table-hover">
            <thead>
            <tr>
              <th colspan="7" class="bg-dark text-white">Orders</th>
            </tr>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Warehouse Name</th>
              <th scope="col">Order Type</th>
              <th scope="col">Status</th>
              <th scope="col">Site Name</th>
              <th scope="col">Site URL</th>
              <th scope="col"></th>
            </tr>
            </thead>
            <template v-if="customer.orders.length > 0">
              <tbody>
              <tr v-for="order in customer.orders" :key="order.id">
                <td>{{ order.id }}</td>
                <td>{{ order.warehouse_id }}</td>
                <td>{{ order.order_type }}</td>
                <td>{{ order.status }}</td>
                <td>{{ order.site_name }}</td>
                <td>{{ order.site_url }}</td>
                <td>
                  <inertia-link class="link-primary" :href="route('shop-for-me.show', order.id)">
                    <span><i class="fa fa-eye"></i></span>
                  </inertia-link>
                </td>
              </tr>

              </tbody>
            </template>
            <template v-else>
              <tbody>
              <tr>
                <th>Customer Haven't Order Anything</th>
              </tr>
              </tbody>
            </template>
          </table> -->

        </div>
        <div class="card-footer">

        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/Main'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'

export default {
  components: {
    BreezeAuthenticatedLayout,
    MainLayout
  },

  props: {
    auth: Object,
    customer: Object,
  },
  methods: {
    suiteNumber(user_id){
      return 4000 + user_id;
    },
  }
}
</script>

<style scoped>

</style>