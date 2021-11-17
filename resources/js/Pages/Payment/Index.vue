<template>
  <MainLayout>
    <div class="card mt-4 pr-2">
      <div class="card-header">Payments</div>
      <div class="card-body">
        <table class="table table-responsive table-hover">
          <thead>
          <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Order</th>
            <th>Package</th>
            <th>Transaction Id</th>
            <th>Invoice Id</th>
            <th>Charged Amount (USD)</th>
            <th>Charged At</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="item in payments" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ item.customer_id != null ? item.customer.name : '- -'}}</td>
            <td>{{ item.order_id  != null ?item.order_id : '- -'}}</td>
            <td>{{ item.package_id != null ? item.package.id : '- -'}}</td>
            <td>{{ item.transaction_id }}</td>
            <td>{{ item.invoice_id }}</td>
            <td>{{ item.charged_amount }}</td>
            <td>{{ item.charged_at }}</td>
          </tr>
          </tbody>
        </table>
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
    }
  }

}
</script>

<style scoped>

</style>