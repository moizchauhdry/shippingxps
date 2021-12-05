<template>
  <MainLayout>
    <div class="container">
      <div class="card" style="margin-top: 0px">
        <BreezeValidationErrors/>
          <div class="card-header">Payment</div>
          <div class="card-body">
            <div class="container">
              <form @submit.prevent="submit" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-md-6 offset-md-3">
                    <div class="bg-dark border-3 border-warning container mb-3 text-center text-white">
                      <h2 style="color: #fff;font-weight:bold;font-size:25px;" >Checkout</h2>
                    </div>
                    <div v-show="response_message != null" class="alert alert-danger">
                      {{ response_message }}
                    </div>
                    <div v-if="hasPackage == 1" class="d-inline-flex mt-2 mb-2">
                      <breeze-label value="Hava a Coupon?"/><span @click="isHidden = false" style="text-decoration: underline;cursor: pointer" >Click here to enter your code</span>
                    </div>
                    <div v-if="!isHidden" class="col-md-12 mt-2 mb-4 ">
                      <breeze-label value="Enter Coupon Code"/>
                      <input v-model="form.coupon_code" class="form-control" type="text" id="coupon_code" name="coupon_code" placeholder="#####">
                      <span :class="coupon_status == 0 ? 'text-red-600' : 'text-green-600'" >{{ coupon_message }}</span><br>
                      <label class="btn btn-primary mt-1 mb-2 " @click="checkCouponCode">Apply Coupon</label>
                    </div>
                    <div class="row">
                      <div class="col-12" v-if="status != undefined">
                        <p style="color:red;">{{ status.message[0].text }}</p>
                      </div>
                      <div class="form-group col-12">
                        <breeze-label for="name" value="Name On Card"/>
                        <input v-model="form.name_on_card" class="form-control" type="text" maxlength="55" name="name_on_card" placeholder="Enter Name On Card" required>
                      </div>
                      <div class="form-group col-12">
                        <breeze-label for="name" value="Card No"/>
                        <input v-model="form.card_no" class="form-control"  type="number" :maxlength="card_max"  name="card_no" placeholder="####-####-####-####" required>
                      </div>
                      <div class="form-group col-4">
                        <breeze-label for="month" value="Month"/>
                        <select v-model="form.month" class="form-control" name="month" required>
                          <option value="">Selcect Month</option>
                          <option v-for="n in 12" :value="n" >{{n}}</option>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <breeze-label  for="name" value="Year"/>
                        <select v-model="form.year" class="form-control" name="year" required>
                          <option value="">Selcect Year</option>
                          <option :value="(new Date().getFullYear())">{{new Date().getFullYear()}}</option>
                          <option v-for="n in 5" :value="n + (new Date().getFullYear())">{{n + (new Date().getFullYear())}}</option>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <breeze-label for="name" value="CVV"/>
                        <input v-model="form.cvv" class="form-control" type="number" maxlength="3" name="cvv" placeholder="123" required>
                      </div>

                      <table class="table" cols="4">
                        <tbody >
                        <tr  v-if="form.discount != null">
                          <th class="w-75 text-end">Sub Total</th>
                          <td>${{ amount }}</td>
                        </tr>
                        <tr  v-if="form.discount != null && form.discount > 0.00">
                          <th class="w-75 text-end">Coupon Discount</th>
                          <td>${{ parseFloat(amount * (form.discount/100)).toFixed(2) }}</td>
                        </tr>
                        <tr>
                          <th class="w-75 text-end">Total</th>
                          <td>${{ form.discount != null ? parseFloat(amount - (amount * (form.discount/100))).toFixed(2) : amount }}</td>
                        </tr>
                        </tbody>
                      </table>
                      <div class="form-group col-12">
                        <input type="checkbox" id="terms" required> <label for="terms">By clicking here, I state that I have read and understood the</label> <inertia-link :href="route('page-show','terms_and_conditions')" class="link-hover-style-1 ms-1">Terms and Conditions</inertia-link>
                      </div>
                      <div class="form-group col-12">
                        <button type="submit" v-on:submit="submit()" class="btn btn-primary w-100">Pay</button>
                      </div>
                    </div>

                  </div>
                </div>
              </form>
              <hr>
              <div class="row">
                {{ status }}
                <div class="col-md-6 offset-md-3">
                  <a href="https://www.paypal.com/signin" class="btn btn-info w-100" target="_blank">Pay With PayPal</a>
<!--                  <form method="post" action="https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" name="cmd" value="_xclick">
                    &lt;!&ndash; Modify the below items to suit your needs &ndash;&gt;
                    <input type="hidden" name="business" value="info@shippingxps.com">
                    <input type="hidden" name="amount" :value="amount">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="return" :value="this.route('payments.PaymentSuccess')">
                    <button class="btn btn-info w-100" type="submit">Pay With Pay Pal</button>
                  </form>-->
                </div>
              </div>
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
      isHidden: true,
      info:'',
      response:'',
      response_message : null,
      coupon_status : 2,
      coupon_message : '',
      card_max:16,
      card_csv_max:3,
      hasPackage : this.hasPackage,
      form: this.$inertia.form({
        amount:this.amount,
        name_on_card : '',
        card_no : '',
        month : '',
        year : '',
        cvv : '',
        coupon_code : '',
        coupon_code_id : '',
        discount:0.00,
      })
    };
  },
  props: {
    amount:Object,
    status:Object,
    hasPackage:Object,
  },
  computed:{

  },
  mounted() {
    this.coupon_message = '';
    this.coupon_status = 2;
  },
  methods : {
    submit(){
      // this.form.post(this.route('payment.pay'))
      this.response_message = null;
      axios.post(this.route('payment.pay'), this.form).then(response => (this.response = response)).finally(() => this.responseFromSubmit());
    },
    responseFromSubmit(){
      let data = this.response.data;
      if(data.status === 0){
        this.response_message = data.message
      }else{
        location.href = this.route('payments.PaymentSuccess',data.payment_id)
      }
    },
    checkCouponCode() {
      axios.post(this.route('checkCoupon'), {
        code: this.form.coupon_code,
      }).then(response => (this.info = response)).catch(function (error) {
        this.coupon_status = 0;
        this.coupon_message = "Something went wrong";
        console.log(error);
      }).finally(() => this.couponResponse());
    },
    couponResponse(){
      console.log(this.info);
      var info = this.info;
      if (info.data.status == 1) {
        this.coupon_status = 1;
        this.coupon_message = info.data.message;
        this.form.discount = info.data.discount;
        this.form.coupon_code_id = info.data.coupon_id

      } else {
        console.log(info);
        console.log(this);
        this.coupon_status = 0;
        this.coupon_message = info.data.message;
        this.form.coupon_code_id = info.data.coupon_id

      }
    },
  },
}
</script>

<style scoped>

</style>