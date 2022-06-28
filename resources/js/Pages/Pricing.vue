<template>
  <header-component></header-component>
  <div role="main" class="main">

    <loading v-model:active="isLoading"
             :can-cancel="true"
             :on-cancel="onCancel"
             :is-full-page="fullPage"/>


    <section class="section top-section-padd section-with-shape-divider page-header page-header-modern page-header-lg border-0 my-0" style="background-size: cover; background-position: center;">
      <div class="container pb-5 my-3">
        <div class="row mb-4">
          <div class="col-md-12 align-self-center p-static order-2 text-center">
            <h1 class="font-weight-bold text-color-dark text-10">Shipping Calculator</h1>
            <p class="text-4 font-weight-medium mt-3 mb-0">Shop from the USA and get your packages delivered to your doorstep with ease and without worry. </p>
          </div>
        </div>
      </div>
      <div class="shape-divider shape-divider-bottom shape-divider-reverse-x divider-index" style="height: 123px;">
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1920 123" preserveAspectRatio="xMinYMin">
          <polygon fill="#F3F3F3" points="0,90 221,60 563,88 931,35 1408,93 1920,41 1920,-1 0,-1 "/>
          <polygon fill="#FFFFFF" points="0,75 219,44 563,72 930,19 1408,77 1920,25 1920,-1 0,-1 "/>
        </svg>
      </div>
    </section>
    <section class="section price-section-padd section-height-3 bg-light border-0 pt-4 m-0" style="background-size: 100%; background-repeat: no-repeat;">
      <div class="container">
        <div class="row mx-3 mx-xl-0">
          <div class="col-md-12 px-0">
            <div class="bg-dark h-100">
              <div class="text-center text-md-start p-5 h-100">
                <form @submit.prevent="submit" class="contact-form form-style-4 form-placeholders-light form-errors-light mb-5 mb-lg-0">

                  <div class="row">

                    <div class="col-md-5">
                      <div class="form-group">
                        <div class="input-title text-white mb-2 text-6 font-weight-medium text-center">Where is your merchandise?</div>
                        <select required v-model="form.ship_from" class="form-select text-4" aria-label="Default select example">
                          <option v-for="warehouse in warehouses" :value="warehouse.id">{{ warehouse.name }}</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group md-mrgn">
                        <div class="input-title text-white mb-2 text-6 font-weight-medium text-center">Where are you shipping to?</div>
                        <select required v-model="form.ship_to" class="form-select text-4" aria-label="Default select example">
                          <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="form-group sizes-input mt-5" style="margin-top:0px !important;">
                        <label class="text-6 text-center text-white font-weight-medium d-block" for="weight">Zip</label>
                        <input v-model="form.zipcode" type="text" class="form-control text-dark text-4 mt-2" name="zip" placeholder="">
                      </div>
                    </div>
                    <div class="form-heading text-center mt-5">
                      <span class="text-6 text-light font-weight-bold"> <input type="checkbox" name="is_residential" @change="is_residential($event)" style="line-height: 1;vertical-align: unset;">&nbsp;&nbsp;Residential</span>
                    </div>
                    <div class="form-heading text-center mt-5">
                      <h6 class="text-6 text-primary font-weight-bold">Tell us about the package size and weight...</h6>
                    </div>
                  </div>
                  <div class="row">
                    <!-- <div class="col-md-4">
                      <div class="form-group sizes-input mt-5">
                        <label class="text-6 text-center text-white font-weight-medium d-block" for="weight">Declared Value of Package</label>
                        <input v-model="form.declared_value"  type="text" class="form-control text-dark text-4 mt-2" name="name" placeholder="" required="">
                      </div>
                    </div> -->
                    <div class="col-md-3">
                      <div class="form-group sizes-input mt-5">
                        <label class="text-6 text-center text-white font-weight-medium d-block" for="weight">Weight</label>
                        <input v-model="form.weight" type="text" class="form-control text-dark text-4 mt-2" name="name" placeholder="" required="">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group sizes-input mt-5">
                        <label class="text-6 text-center text-white font-weight-medium d-block" for="weight">Unit</label>
                        <select v-model="form.weight_unit" class="form-select text-4 mt-2" aria-label="Default select example">
                          <option value="lb_in" selected>Lb / Inch</option>
                          <option value="kg_cm">Kg / Cm</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group sizes-input mt-5">
                        <label class="text-6 text-center text-white font-weight-medium d-block" for="weight">Length</label>
                        <input v-model="form.length" type="text" class="form-control text-dark text-4 mt-2" name="name" placeholder="" required="">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group sizes-input mt-5">
                        <label class="text-6 text-center text-white font-weight-medium d-block" for="weight">Width</label>
                        <input v-model="form.width" type="text" class="form-control text-dark text-4 mt-2" name="name" placeholder="" required="">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group sizes-input mt-5">
                        <label class="text-6 text-center text-white font-weight-medium d-block" for="height">Height</label>
                        <input v-model="form.height" type="text" class="form-control text-dark text-4 mt-2" name="name" placeholder="" required="">
                      </div>
                    </div>
                  </div>
                  <div class="dim-warning col text-center">
                    <a class="" href="#"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                      Why dimensions matter? Learn more about "Volumetric" weight
                    </a>
                  </div>

                  <div class="row" v-show="serverError!=''">
                    <div class="form-group col text-center mt-4">
                      <p style="color:red;font-weight:bold;"> {{ serverError }}</p>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col text-center mt-4">
                      <button type="submit"
                              class="btn btn-primary custom-btn-style-1 font-weight-normal btn-px-4 btn-py-2 text-5-5"
                              data-loading-text="Loading..."
                              data-cursor-effect-hover="plus"
                              data-cursor-effect-hover-color="light"
                              ref="buttonRates"
                      >
                        <span>Get Shipping Rates</span>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SPECIAL SERVICES -->
    <section v-show="showEstimatedPrice" class="special-services mt-5">
      <div class="container">
        <div class="col">
          <h3 class="text-5 text-center text-lg-5 text-xl-5 line-height-3 text-transform-none font-weight-semibold mb-4 mb-lg-5">Current Rates</h3>
          <template v-for="service in services" :key="service.service_id">
            <div v-if="service.isReady === true" class="free-services-meta mt-4 py-4">
              <div class="row align-items-center">
                <div class="col-md-2">
                  <div class="services-img handle-img">
                    <img class="img-fluid" :src="imgURL(service.logo)" alt="Account Fee">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="services-title">
                    <h6 class="text-5-5 font-weight-medium mb-0">{{ service.serviceLabel }}</h6>
                    <strong class="text-4-5 font-weight-medium text-primary text-uppercase d-block mt-2"></strong>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="services-para">
                    <div class="text-4-5 font-weight-normal">{{ service.totalAmount }} {{ service.currency }}</div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </section>
    <!-- END SPECIAL SERVICES -->
  </div>
  <PricingContentComponent/>
  <FooterComponent/>


</template>

<style scoped>

</style>

<script>
import HeaderComponent from '../Components/Header.vue'
import HomeTopSection from '../Components/HomeTopSection.vue'
import FooterComponent from '../Components/Footer.vue'
import PricingContentComponent from '../Components/PricingContent.vue'

import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

export default {
  components: {
    'header-component': HeaderComponent,
    'home-top-section': HomeTopSection,
    FooterComponent,
    PricingContentComponent,
    Loading
  },
  data() {
    return {
      showEstimatedPrice: false,
      isLoading: false,
      fullPage: true,
      serverError: '',
      services: {},
      form: this.$inertia.form({
        ship_from: '',
        ship_to: '',
        zipcode: '',
        weight: '',
        unit: 'lb_',
        length: '',
        width: '',
        height: '',
        is_residential:'0'
      })
    };
  },
  props: {
    auth: Object,
    canLogin: Boolean,
    canRegister: Boolean,
    errors: Object,
    laravelVersion: String,
    phpVersion: String,
    warehouses: Object,
    countries: Object,
    // services: Object
  },

  methods: {
    is_residential(event){
      if(event.target.checked){
        this.form.is_residential = '1';
      }else{
        this.form.is_residential = '0';
      }
    },
    submit() {
      //this.$refs.buttonRates.innerText = "Loading ..."
      this.showEstimatedPrice = false;
      this.isLoading = true;
      let quote_params = {
        ship_from: this.form.ship_from,
        ship_to: this.form.ship_to,
        weight: this.form.weight,
        unit: this.form.unit,
        length: this.form.length,
        //declared_value: this.form.declared_value,
        width: this.form.width,
        height: this.form.height,
        zipcode: this.form.zipcode,
        is_residential: this.form.is_residential,
      };
      axios.get(this.route('getServicesList')).then(response => {
        console.log(response.data.services)
        this.services = response.data.services;
        response.data.services.forEach((ele, index) => {
          console.log(ele);
          quote_params.service = ele;
          this.getRates(quote_params);
        })
      }).catch(error => {
        console.log(error)
      })


      /*quote_params.service = this.services.fedex_international_economy;
          axios.get("/getQuote",{
        params : quote_params,
      })
          .then(({ data }) => {
          this.isLoading = false;
          if(data.status){
            this.showEstimatedPrice=true;
            //this.services.push(data.service)
            this.services[data.service.serviceCode] = data.service;

          }else{
            this.serverError = data.message;
          }
              }
          );

      //get first service rate.
      quote_params.service = this.services.usps_international_first_class;
          axios.get("/getQuote",{
        params : quote_params,
      })
          .then(({ data }) => {
          this.isLoading = false;
          if(data.status){
            this.showEstimatedPrice=true;
            //this.services.push(data.service)
            this.services[data.service.serviceCode] = data.service;

          }else{
            this.serverError = data.message;
          }
              }
          );

      //get first service rate.
      quote_params.service = this.services.usps_international_priority;
          axios.get("/getQuote",{
        params : quote_params,
      })
          .then(({ data }) => {
          this.isLoading = false;
          if(data.status){
            this.showEstimatedPrice=true;
            //this.services.push(data.service)
            this.services[data.service.serviceCode] = data.service;
          }else{
            this.serverError = data.message;
          }
              }
          );

      //get first service rate.
      quote_params.service = this.services.usps_international_express;
          axios.get("/getQuote",{
        params : quote_params,
      })
          .then(({ data }) => {
          this.isLoading = false;
          if(data.status){
            this.showEstimatedPrice=true;
            //this.services.push(data.service)
            this.services[data.service.serviceCode] = data.service;
          }else{
            this.serverError = data.message;
          }
              }
          );*/


    },
    getRates(quote_params) {
      axios.get("/getQuote", {
        params: quote_params,
      }).then((response) => {
            console.log(response.data.service)
            this.isLoading = false;
            if (response.data.status) {

              this.showEstimatedPrice = true;
              //this.services.push(data.data.service)
              this.services[response.data.service.service_id] = response.data.service;

            } else {
              this.serverError = response.data.message;
            }
          }
      );
    },
    imgURL(url) {
      return "/public" + url;
    },
  },

  mounted() {
    var services1 = this.services;

  },
  created() {
    //this.$Progress.start();
    //this.$Progress.finish();
  }
};
</script>
