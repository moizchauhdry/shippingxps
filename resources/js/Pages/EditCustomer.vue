<template>
    <MainLayout>
      <div class="card mt-4">
      <div class="card-body stock-subscription-form">

        <form @submit.prevent="submit">          
            <div class="order-form">
              <h5>Personal Info</h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>First Name</label>
                    <input name="first_name" id="first_name" type="text" class="form-control" placeholder="First Name" v-model="form.first_name" required />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Last Name</label>
                    <input name="last_name" id="last_name" type="text" class="form-control" placeholder="Last Name" v-model="form.last_name" required />
                  </div>
                </div>
              </div>
            </div>
            <div class="billing-form">
              <h5>Billing Address</h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Address 1</label>
                    <input name="address1" id="address1" type="text" class="form-control" placeholder="Address 1" required v-model="form.address1" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Address 2</label>
                    <input name="address2" id="address2" type="text" v-model="form.address2" class="form-control" placeholder="Address 2" />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>City</label>
                    <input name="city" id="city" v-model="form.city" type="text" class="form-control" placeholder="City" required />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>State</label>
                    <input name="city" id="city" v-model="form.state" type="text" class="form-control" placeholder="State" required />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Country</label>
                    <select name="country" id="country" class="form-select" v-model="form.country"  required>
                      <option selected>Country</option>
                      <option value="USA">United States Of America</option>
                      <option value="Canada">Canada</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-control" v-model="form.postal_code" placeholder="Postal Code" required />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Phone No</label>
                    <input type="text" id="phone_no" name="phone_no" v-model="form.phone_no" class="form-control" placeholder="Phone No"  required />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" readonly v-model="form.email" name="email_address" id="email_address" class="form-control" placeholder="Email Address" required  />
                  </div>
                </div>
              </div>


            </div>



            <div class="order-button text-center">
              <input type="submit" value="Update" class="btn btn-danger" />
            </div>


          </form>
        </div>
      </div> 
    </MainLayout>
</template>

<script>
    import MainLayout from '@/Layouts/Main'
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
    import BreezeButton from '@/Components/Button'
    import BreezeGuestLayout from '@/Layouts/Guest'
    import BreezeInput from '@/Components/Input'
    import BreezeLabel from '@/Components/Label'
    import BreezeValidationErrors from '@/Components/ValidationErrors'

    export default {
        components: {
            BreezeAuthenticatedLayout,
            BreezeButton,
            BreezeInput,
            BreezeLabel,
            BreezeValidationErrors,
            MainLayout
        },
         props: {
            errors: Object,
            customer: Object,
        },
    data() {
            return {
                form: this.$inertia.form({
                    first_name: this.customer.first_name,
                    last_name: this.customer.last_name,
                    address1: this.customer.address1,
                    address2: this.customer.address2,
                    city: this.customer.city,
                    state: this.customer.state,
                    city: this.customer.city,
                    country: this.customer.country,
                    postal_code: this.customer.postal_code,
                    phone_no: this.customer.phone_no,
                    email: this.customer.email,
                    id:this.customer.id
                }),
                currentYear: new Date().getFullYear(),
                Years : [],
            }
        },
        computed : {
            years () {
              const year = new Date().getFullYear()
              return Array.from({length: year - 2000}, (value, index) => year + index)
            }
          },
    methods: {
            submit() {
                this.form.put(this.route('edit-customer',this.customer.id))
            },

        }
    }
</script>
