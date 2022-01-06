<template>
  <MainLayout>
    <div class="card mt-4 pr-2">
      <div class="card-header">Payments
        <div class="float-right"><a :href="route('generateReportList')" class="btn btn-primary" target="_blank">Download Reports</a></div>
      </div>
      <div class="card-body ">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
            <tr>
              <th>ID</th>
              <th>Customer</th>
              <th>Order</th>
              <th>Package</th>
              <th>Transaction Id</th>
              <th>Invoice Id</th>
              <th>Invoice</th>
              <th>Charged Amount (USD)</th>
              <th>Charged At</th>
              <th>Destination Country</th>
              <th>Service Type</th>
              <th>Service Shipping Charges (USD)</th>
              <th>Tracking No.</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item,index) in payments" :key="item.id">
              <td>{{ item.id }}</td>
              <td>{{ item.customer_id != null ? item.customer.name : '- -'}}</td>
              <td>
                <template v-if="item.order_id != null">
                  <inertia-link :href="route('orders.show',item.order_id)" class="link-hover-style-1 ms-1">{{ item.order_id }}</inertia-link>
                </template>
                <template v-else>- -</template>
              </td>
              <td>
                <template v-if="item.package_id != null">
                  <inertia-link :href="route('packages.show',item.package.id)" class="link-hover-style-1 ms-1">{{ item.package.id }}</inertia-link>
                </template>
                <template v-else>- -</template>
              </td>
              <td>{{ item.transaction_id }}</td>
              <td>{{ item.invoice_id }}</td>
              <td><a :href="'/public/'+item.invoice_url" target="_blank">View Invoice</a></td>
              <td>{{ item.charged_amount }}</td>
              <td>{{ item.charged_at }}</td>
              <td>{{ item.package != NULL ? getAddress(item.package.address): '- -'  }}</td>
              <td>{{ item.package != NULL ? item.package.service_label : '- -' }}</td>
              <td>{{ item.package != NULL ? item.package.shipping_total : '- -' }}</td>
              <td>{{ item.package != NULL ? item.package.tracking_number_out : '- -' }}</td>
              <td>
                <!-- Action Here -->
                <a :href="route('generateReport',item.id)" target="_blank">Download Report</a>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
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
  props: {
    payments: Object
  },
  methods:{
    changeStatus(id,status,event){
      axios.post(this.route('coupon.changeStatus'), {
        id:id,
        status:status,
      }).then(function (response) {
        console.log(response.data.coupon.status);
        let status = response.data.coupon.status;
        if(status === 1){
          event.target.classList.remove('btn-success');
          event.target.classList.add('btn-danger');
        }else{
          event.target.classList.add('btn-success');
          event.target.classList.remove('btn-danger');
        }

      }).catch(function (error) {
        console.log(error);
      });
    },
    getAddress(address){
      return address.address + ', ' + address.city +', '+ address.country.name;

    }
  }

}
</script>

<style scoped>

</style>