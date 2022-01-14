<template>
  <MainLayout>
    <div class="card mt-4 pr-2">
      <div class="card-header">Payments
        <div class="float-right"><a v-if="$page.props.auth.user.type == 'admin'" :href="route('generateReportList')" class="btn btn-primary" target="_blank">Download Reports</a></div>
      </div>
      <div class="card-body ">
        <div class="container">
          <div class="row">
            <div class="form-group col-sm-12 col-md-4">
              <label for="">Filter By</label>
              <select class="form-control" @change="getResults(route('payments.getPayments'))" v-model="filter.date_selection" id="">
                <option value="1">Today</option>
                <option value="2">Yesterday</option>
                <option value="3">Last 7 Days</option>
                <option value="4">Last 30 Days</option>
                <option value="5">Custom Range</option>
              </select>
            </div>
            <div class="form-group col-sm-12 col-md-4">
              <label for="" v-show="filter.date_selection === '5'">Date Range</label>
              <Datepicker  v-show="filter.date_selection === '5'" v-model="date" range :format="format" :enableTimePicker="false"></Datepicker>
            </div>
            <div class="form-group col-sm-12 col-md-4">
              <label for="">Search</label>
              <input type="text" class="form-control" v-model="filter.search" @keyup="getResults(route('payments.getPayments'))" >
            </div>
          </div>
        </div>
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
              <th>Destination Country</th>
              <th>Service Type</th>
              <th>Service Shipping Charges (USD)</th>
              <th>Service Charges (USD)</th>
              <th>Charged Amount (USD)</th>
              <th>Charged At</th>
              <th>Tracking No.</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item,index) in data.data" :key="item.id">
              <td>{{ item.id }}</td>
              <td>{{ item.customer_id != null ? item.customer.name +' - '+ item.customer.suite_no : '- -'}}</td>
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
              <td>{{ item.package != NULL ? getAddress(item.package.address): '- -'  }}</td>
              <td>{{ item.package != NULL ? item.package.service_label : '- -' }}</td>
              <td>{{ item.package != NULL ? item.package.shipping_total : '- -' }}</td>
              <td>{{ item.package != NULL ? item.package.service_charges : '- -' }}</td>
              <td>{{ item.charged_amount }}</td>
              <td>{{ item.charged_at }}</td>
              <td>{{ item.package != NULL ? item.package.tracking_number_out : '- -' }}</td>
              <td>
                <!-- Action Here -->
                <a :href="route('generateReport',item.id)" target="_blank">Download Report</a>
              </td>
            </tr>
            <tr v-show="data.data.length === 0">
              <td colspan="15">
                <div class="container text-center p-5">
                    No Record Found
                </div>
              </td>
            </tr>
            </tbody>
          </table>

        </div>
        <div id="parent">
          <nav class="float-right">
          <ul class="pagination">
          <template v-for="link in data.links">
            <li v-if="link.active" class="page-item active" aria-current="page"><span class="page-link" v-html="link.label"></span></li>
            <li v-else class="page-item"><a href="javascript:void(0)" class="page-link" @click="getResults(link.url)" v-html="link.label"></a></li>
          </template>
          </ul>
          </nav>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/Main'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import BreezeLabel from '@/Components/Label'
import Paginate from "@/Components/Paginate"
import Datepicker from 'vue3-date-time-picker';
import 'vue3-date-time-picker/dist/main.css'


export default {
  data(){
    return {
      data : this.payments,
      filter : this.$inertia.form({
        search:null,
        date_selection:null,
        date_range:null,
        per_page:null,
      }),
      date: null,
    }
  },
  components: {
    BreezeAuthenticatedLayout,
    MainLayout,
    BreezeLabel,
    Paginate,
    Datepicker
  },
  props: {
    auth: Object,
    payments: Object
  },
  mounted() {
  },
  methods:{
    getResults(url){
      if(url != null){
        axios.post(url,this.filter)
            .then((response) => {
                  this.data = response.data.payments;
                }
            );
      }
    },
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

    },
    format(){
      var start = new Date(this.date[0]);
      var end = new Date(this.date[1]);
      console.log(this.date[0]);
      console.log(this.date[1]);
      var startDay = start.getDate();
      var startMonth = start.getMonth() + 1;
      var startYear = start.getFullYear();
      var endDay = end.getDate();
      var endMonth = end.getMonth() + 1;
      var endYear = end.getFullYear();

      this.filter.date_range = `${startYear}/${startMonth}/${startDay} - ${endYear}/${endMonth}/${endDay}`;
      this.getResults(route('payments.getPayments'));
      return `${startDay}/${startMonth}/${startYear} - ${endDay}/${endMonth}/${endYear}`;
    }
  },
  created() {

    console.log(this.data);
  }


}
</script>

<style scoped>

</style>