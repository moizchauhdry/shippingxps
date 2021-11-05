<template>
    <MainLayout>
      <div class="card mt-4">
      <div class="card-body stock-subscription-form">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Manage Customers
        </h2>
          <div class="col-md-4" style="float: right;margin-bottom: 30px ">
            <form action="/customers" method="get">
              <input type="text" name="search" :value="form.search"  class="form-control" placeholder="Search By Suite #">
            </form>
          </div>
          <flash-messages ></flash-messages>
          <table class="table">

					  <thead>
					    <tr>
					      <th scope="col">#</th>
					      <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">City</th>
                <!-- <th scope="col">State</th> -->
                <th scope="col">Country</th>
                <!-- <th scope="col">Postal Code</th> -->
                <th scope="col">Suite #</th>
                <th scope="col">Phone No</th>
                <!--<th></th>-->
                <th scope="col">Actions</th>
					    </tr>
					  </thead>
					  <tbody>
					  <tr v-for="customer in customers.data" :key="customer.id">
					  	<td>{{ customer.id }}</td>
					  	<td>{{ customer.first_name }}</td>
              <td>{{ customer.email }}</td>
              <td>{{ customer.city }}</td>
              <!-- <td>{{ customer.state }}</td> -->
              <td>{{ customer.country }}</td>
              <!-- <td>{{ customer.postal_code }}</td> -->
              <td>{{ siuteNum(customer.id) }}</td>
              <td>{{ customer.phone_no }}</td>
              <!-- <td>
                  <inertia-link class="link-primary" :href="createOrderLink(customer.id)">
                      <span>Create Order </span>
                  </inertia-link>
              </td>-->
					  	<td>
					  		<inertia-link  v-if="$page.props.auth.user.type == 'admin'" :href="route('edit-customer',{ id: customer.id })" class="btn btn-info">Edit</inertia-link> |
					  		<inertia-link  v-if="$page.props.auth.user.type == 'admin'" href="" class="btn btn-danger" @click="destroy(customer.id)">Delete</inertia-link>
					  	</td>
					  </tr>
					  </tbody>
					</table>
        <pagination class="mt-6" :links="customers.links" />
        </div>
        </div>
    </MainLayout>
</template>

<script>
    import MainLayout from '@/Layouts/Main'
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
    import Pagination from '@/Components/Pagination'
import FlashMessages from '@/Components/FlashMessages.vue'

    export default {
        data() {
        return {
            units: {},
            term: '',
            form: this.$inertia.form({
                    search: this.search
            }),
            params:{
              search:null,
            },
        }
    },
  components: {
            BreezeAuthenticatedLayout,
            MainLayout,
            Pagination,
            FlashMessages,
        },
    props: {
         customers: Object,
         search: String,
    },
        
    watch:{
      params:{
        handler(){
          this.$inertia.get(this.route('customers'),this.params,{replace:true,preserveState:true});
        }
      }
    },
    methods:{
      siuteNum(user_id){
                return 4000 + user_id;
            },
      destroy(id) {
            if (confirm('Are you sure you want to delete this Customer?')) {
                this.$inertia.delete(this.route('delete-customer', id))
            }
        },
        createOrderLink(id){
            return this.route('orders.create')+'?customer_id='+id;
        }
      }
    }
</script>
