<template>
  <MainLayout>
    <div class="container">
      <div class="card" style="margin-top: 0px">
          <div class="card-header">Payment</div>
          <div class="card-body">

          </div>
          <div class="card-footer">
            <button class="btn btn-primary" @click="checkout()">Pay</button>
          </div>
      </div>
    </div>
  </MainLayout>
</template>

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
  methods : {
    checkout(){
      axios.post(this.route('payment.pay'))
          .then(({ data }) => {
                if(data.status){
                  this.$inertia.visit('/dashboard');
                }else{
                  //alert('There was some issue');
                }
              }
          );
    },
  },
}
</script>

<style scoped>

</style>