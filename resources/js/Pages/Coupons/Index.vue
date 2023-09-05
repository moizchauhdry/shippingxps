<template>
  <MainLayout>
    <div class="card mt-4 pr-2">
      <div class="card-header">
        Manage Coupons
        <div class="float-right">
          <inertia-link :href="route('coupon.create')" v-if="$page.props.auth.user.type == 'admin'"
            class="btn btn-primary float-right">Add Coupon</inertia-link>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-responsive table-bordered table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Code</th>
              <th>Discount Amount</th>
              <th>Expired Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="coupon in coupons" :key="coupon.id">
              <td>{{ coupon.id }}</td>
              <td>{{ coupon.name }}</td>
              <td>{{ coupon.code }}</td>
              <td>${{ coupon.discount }}</td>
              <td>{{ coupon.expires_at }}</td>
              <td>
                <a class="btn btn-danger" v-if="coupon.status == 1"
                  v-on:click="changeStatus(coupon.id, 0, $event)">Deactivate</a>
                <a class="btn btn-success" v-else v-on:click="changeStatus(coupon.id, 1, $event)">Activate</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="card-footer">

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
    coupons: Object
  },
  methods: {
    changeStatus(id, status, event) {
      axios.post(this.route('coupon.changeStatus'), {
        id: id,
        status: status,
      }).then(function (response) {
        console.log(response.data.coupon.status);
        let status = response.data.coupon.status;
        if (status === 1) {
          event.target.classList.remove('btn-success');
          event.target.classList.add('btn-danger');
        } else {
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

<style scoped></style>