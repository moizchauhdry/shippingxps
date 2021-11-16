<template>
  <MainLayout>
    <div class="container">
      <div class="card" style="margin-top: 0px">
          <div class="card-header">Payment</div>
          <div class="card-body">
            <div class="container">
              <form @submit.prevent="submit" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-md-6 offset-md-3">
                    <div class="bg-dark border-3 border-warning container mb-3 text-center text-white">
                      <h2 style="color: #fff;font-weight:bold;font-size:25px;" >TOTAL AMOUNT</h2>
                      <h4 class="mb-2" style="color: #fff;font-weight:bold;font-size:20px;" >USD {{ amount }}</h4>
                    </div>
                    <div class="row">
                      <div class="form-group col-12">
                        <breeze-label for="name" value="Name On Card"/>
                        <input v-model="form.name_on_card" class="form-control" type="text" maxlength="55" name="name_on_card" placeholder="Enter Name On Card" required>
                      </div>
                      <div class="form-group col-12">
                        <breeze-label for="name" value="Card No"/>
                        <input v-model="form.card_no" class="form-control" type="number" min="1111111111111111" max="9999999999999999" name="card_no" placeholder="####-####-####-####" required>
                      </div>
                      <div class="form-group col-4">
                        <breeze-label for="month" value="Month"/>
                        <select v-model="form.month" class="form-control" name="month" required>
                          <option value="">Selcect Month</option>
                          <option v-for="n in 12" :value="n">{{n}}</option>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <breeze-label for="name" value="Year"/>
                        <select v-model="form.year" class="form-control" name="year" required>
                          <option value="">Selcect Year</option>
                          <option :value="(new Date().getFullYear())">{{new Date().getFullYear()}}</option>
                          <option v-for="n in 5" :value="n + (new Date().getFullYear())">{{n + (new Date().getFullYear())}}</option>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <breeze-label for="name" value="CVV"/>
                        <input v-model="form.cvv" class="form-control" type="number" min="100" max="999" name="cvv" placeholder="123" required>
                      </div>
                      <div class="form-group col-12">
                        <button type="submit" v-on:submit="submit()" class="btn btn-primary">Pay</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
<!--          <div class="card-footer">
            <button class="btn btn-primary" @click="checkout()">Pay</button>
          </div>-->
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
  data() {
    return {
      form: this.$inertia.form({
        amount:this.amount,
        name_on_card : '',
        card_no : '',
        month : '',
        year : '',
        cvv : '',
      })
    };
  },
  props: {
    amount:Object,
  },
  methods : {
    submit(){
      this.form.post(this.route('payment.pay'))
    },
  },
}
</script>

<style scoped>

</style>