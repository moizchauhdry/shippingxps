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
                    <template v-if="hasPackage == 0">
                      <h3 v-show="form.shipping_address_id == null">Select Shipping Address to proceed</h3>
                      <select v-on:change="selectAddress($event)" name="shipping_address_id" class="form-select"  v-model="shipping_address_id" required>
                        <option :value="null" disabled :selected="true" >Select Shipping Address</option>
                        <template v-for="(address) in shippingAddress" :key="address.id">
                        <option  :value="address.id" >{{ address.label}}</option>
                      </template>
                    </select>
                      <hr class="mb-3 mt-3">
                    </template>
                    <div v-if="hasPackage == 1 || form.shipping_address_id != null" class="row">
                      <div class="col-12" v-if="status != undefined">
                        <p style="color:red;">{{ status.message[0].text }}</p>
                      </div>
                      <div class="form-group col-12">
                        <breeze-label value="Card No"/>
                        <input v-model="form.card_no" class="form-control"  type="number" :maxlength="card_max" pattern="[0-9]*"  name="card_no" placeholder="####-####-####-####" required>
                      </div>
                      <div class="form-group col-4">
                        <breeze-label for="month" value="Month"/>
                        <select v-model="form.month" class="form-control" name="month" required>
                          <option value="">Selcect Month</option>
                          <option v-for="n in 12" :value="n" >{{n}}</option>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <breeze-label  value="Year"/>
                        <select v-model="form.year" class="form-control" name="year" required>
                          <option value="">Selcect Year</option>
                          <option :value="(new Date().getFullYear())">{{new Date().getFullYear()}}</option>
                          <option v-for="n in 5" :value="n + (new Date().getFullYear())">{{n + (new Date().getFullYear())}}</option>
                        </select>
                      </div>
                      <div class="form-group col-4">
                        <breeze-label value="CVV"/>
                        <input v-model="form.cvv" class="form-control" type="number" :maxlength="3" name="cvv" placeholder="123" required  pattern="[0-9]*">
                      </div>

                      <h2 style="font:small-caps bold 1.2em cursive">Billing Address</h2>

                      <div class="form-group col-sm-12 col-md-6 ">
                        <breeze-label value="First Name*"/>
                        <input v-model="form.first_name" class="form-control" type="text" maxlength="55" name="first_name" placeholder="Enter First Name" required>
                      </div>
                      <div class="form-group col-sm-12 col-md-6 ">
                        <breeze-label value="Last Name*"/>
                        <input v-model="form.last_name" class="form-control" type="text" maxlength="55" name="last_name" placeholder="Enter Last Name" required>
                      </div>

                      <div class="form-group col-sm-12 col-md-6 ">
                        <breeze-label value="Country *"/>
                        <input v-model="form.country" class="form-control" type="text" maxlength="55" name="country" placeholder="Enter Country" required>
                      </div>
                      <div class="form-group col-sm-12 col-md-6 ">
                        <breeze-label value="City*"/>
                        <input v-model="form.city" class="form-control" type="text" maxlength="55" name="city" placeholder="Enter City" required>
                      </div>
                      <div class="form-group col-sm-12 col-md-6 ">
                        <breeze-label value="State*"/>
                        <input v-model="form.state" class="form-control" type="text" maxlength="55" name="state" placeholder="Enter State" required>
                      </div>
                      <div class="form-group col-sm-12 col-md-6 ">
                        <breeze-label value="Zip Code*"/>
                        <input v-model="form.zip" class="form-control" type="text" maxlength="55" name="zip" placeholder="Enter Zip Code" required>
                      </div>
                      <div class="form-group col-12">
                        <breeze-label value="Address*"/>
                        <input v-model="form.address" class="form-control" type="text" maxlength="55" name="address" placeholder="Enter Address" required>
                      </div>
                      <div class="form-group col-sm-12 col-md-6 ">
                        <breeze-label  value="Phone No*"/>
                        <input v-model="form.phone_no" class="form-control" type="number" maxlength="14" name="phone_no" placeholder="Enter Phone No" required>
                      </div>
                      <div class="form-group col-sm-12 col-md-6 ">
                        <breeze-label value="Email*"/>
                        <input v-model="form.email" class="form-control" type="email"  name="email" placeholder="Enter Email" required>
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
              <hr class="mb-3 mt-3">
              <div v-show="hasPackage == 1 || form.shipping_address_id != null" class="row">
                {{ status }}
                <div class="col-md-6 offset-md-3">
                  <a href="javascript:void(0)" id="paymentSuccess" @click="paymentSuccess" class="hidden">pay Success</a>
<!--                  <a href="https://www.paypal.com/signin" class="btn btn-info w-100" target="_blank">Pay With PayPal</a>-->
<!--                  <form method="post" action="https://www.paypal.com/cgi-bin/webscr">
                    <input type="hidden" name="cmd" value="_xclick">
                    &lt;!&ndash; Modify the below items to suit your needs &ndash;&gt;
                    <input type="hidden" name="business" value="info@shippingxps.com">
                    <input type="hidden" name="amount" :value="amount">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="return" :value="this.route('payments.PaymentSuccess')">
                    <button class="btn btn-info w-100" type="submit">Pay With Pay Pal</button>
                  </form>-->
                  <div id="smart-button-container">
                    <div style="text-align: center;">
                      <div id="paypal-button-container"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
<!--          <div class="card-footer">
            <button class="btn btn-primary" @click="checkout()">Pay</button>
          </div>-->
      </div>
    </div>
    <input type="text" ref="transaction_id" id="transaction_id" class="hidden" @change="getValues()"/>
    <textarea name="payment_detail" id="payment_detail" cols="30" rows="10" class="hidden"></textarea>
    <div v-show="overlay === true" class="overlay">
      <div class="overlay__inner">
        <div class="overlay__content"><span class="spinner"></span></div>
      </div>
    </div>




  </MainLayout>
</template>
<style scoped>
.overlay {
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  position: fixed;
  background: rgba(0,0,0,0.5);
}

.overlay__inner {
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  position: absolute;
}

.overlay__content {
  left: 50%;
  position: absolute;
  top: 50%;
  transform: translate(-50%, -50%);
}

.spinner {
  width: 75px;
  height: 75px;
  display: inline-block;
  border-width: 2px;
  border-color: rgba(255, 255, 255, 0.05);
  border-top-color: #fff;
  animation: spin 1s infinite linear;
  border-radius: 100%;
  border-style: solid;
}

@keyframes spin {
  100% {
    transform: rotate(360deg);
  }
}
</style>

<script>
import MainLayout from '@/Layouts/Main'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import BreezeLabel from '@/Components/Label'
import BreezeValidationErrors from '@/Components/ValidationErrors'
import $ from 'jquery'

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
      overlay: false,
      response:'',
      response_message : null,
      coupon_status : 2,
      coupon_message : '',
      card_max:16,
      card_csv_max:3,
      shipping_address_id:null,
      hasPackage : this.hasPackage,
      form: this.$inertia.form({
        amount:this.amount,
        first_name : '',
        last_name : '',
        country : '',
        city : '',
        state : '',
        zip : '',
        address : '',
        phone_no : '',
        email : '',
        card_no : '',
        month : '',
        year : '',
        cvv : '',
        coupon_code : '',
        coupon_code_id : '',
        discount:0.00,
        shipping_address_id:null,
      })
    };
  },
  props: {
    amount:Object,
    status:Object,
    hasPackage:Object,
    shippingAddress:Object,
  },
  watch: {

  },
  mounted() {
    var formdata = this.form;
    this.coupon_message = '';
    this.coupon_status = 2;
    this.initPayPalButton(this.amount,this.route('payment.payPalSuccess'),this.form,this.axios);


  },
  methods : {
    submit(){
      // this.form.post(this.route('payment.pay'))
      if(this.hasPackage == 0 && this.form.shipping_address_id == null){
        alert('Please Select Shipping Address first.')
      }
      this.response_message = null;
      this.overlay = true;
      axios.post(this.route('payment.pay'), this.form).then(response => (this.response = response)).finally(() => this.responseFromSubmit());
    },
    responseFromSubmit(){
      let data = this.response.data;
      if(data.status === 0){
        this.response_message = data.message
        this.overlay = false;
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
        let amount = this.amount * this.discount / 100;
        this.initPayPalButton(amount,this.route('payment.payPalSuccess'),this.form,this.axios);

      } else {
        console.log(info);
        console.log(this);
        this.coupon_status = 0;
        this.coupon_message = info.data.message;
        this.form.coupon_code_id = info.data.coupon_id

      }
    },
    initPayPalButton(amount,route,formData,axios) {
      paypal.Buttons({
        style: {
          shape: 'rect',
          color: 'gold',
          layout: 'vertical',
          label: 'paypal',

        },

        createOrder: function(data, actions) {
          return actions.order.create({
            purchase_units: [{"amount":{"currency_code":"USD","value":amount}}]
          });
        },

        onApprove: function(data, actions) {
          return actions.order.capture().then(function(orderData) {
            // Full available details
            console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
            document.getElementById('transaction_id').value = orderData.id;
            document.getElementById('payment_detail').value = JSON.stringify(orderData, null, 2);
            document.getElementById("paymentSuccess").click();
            // Show a success message within this page, e.g.
            const element = document.getElementById('paypal-button-container');
            element.innerHTML = '';
            element.innerHTML = '<h3>Thank you for your payment!</h3>';

            // Or go to another URL:  actions.redirect('thank_you.html');

          });
        },

        onError: function(err) {
          console.log(err);
        },

        onSuccess: function(order){

          axios.post(this.route('payment.payPalSuccess'), formData).then(response => (this.response = response)).finally(() => this.responseFromSubmit());
        }
      }).render('#paypal-button-container');
    },
    paymentSuccess(){
      this.overlay = true;
      this.form.transaction_id = $('#transaction_id').val();
      this.form.payment_detail = $('#payment_detail').val();
      axios.post(this.route('payment.payPalSuccess'), this.form).then(response => (this.response = response)).finally(() => this.responseFromSubmit());
    },
    getValues(){
      console.log(document.getElementById('transaction_id'));
    },
    selectAddress(event){
      var address = this.shippingAddress[event.target.value];
      this.current_address = address.full_address;
      console.log('target value ' + event.target.value)
      console.log('old value ' + this.form.shipping_address_id)
      this.form.shipping_address_id = event.target.value
      this.shipping_address_id = event.target.value
      console.log('new value ' + this.form.shipping_address_id)
    }
  },
}
</script>

<style scoped>

</style>