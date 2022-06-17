<template>
    <MainLayout>
    <div>
    
        <section>
      <div class="container">

        <h1>Edit Address </h1>

        <div class="stock-subscription-form">

          <form @submit.prevent="submit">          
            <div class="order-form">                
                <breeze-validation-errors class="mb-4" />
                
                <flash-messages class="mb-4" />    
                
                <div class="row">
                  <div class="col-md-6">

                    <div class="form-group">
                      <breeze-label for="fullname" value="Full Name" />
                      <input name="fullname" id="fullname" type="text" class="form-control" placeholder="Full Name" v-model="form.fullname" required />
                    </div>

                    <!-- <div class="form-group">
                      <breeze-label for="country" value="Country" />
                      <input name="country" id="country" type="text" class="form-control" placeholder="Country" v-model="form.country" required />
                    </div> -->

                   <div class="form-group">
                      <breeze-label for="country" value="Country" />
                      <select required  v-model="form.country_id" class="form-control" aria-label="Default select example">
                        <template v-for="country in countries" :key="country">
                          <option  :value="country.id" >{{ country.name}}</option>
                        </template>
                      </select>
                    </div>

                    <div class="form-group">
                      <breeze-label for="state" value="State" />
                      <input name="state" id="state" type="text" class="form-control" placeholder="State" v-model="form.state" required />
                    </div>

                    <div class="form-group">
                      <breeze-label for="city" value="City" />
                      <input name="city" id="city" type="text" class="form-control" placeholder="City" v-model="form.city" required />
                    </div>

                    <div class="form-group">
                      <breeze-label for="Commercial/Residential" value="Commercial/Residential" />
                      <select class="form-control" v-model="form.is_residential">
                          <option :value="false">Commercial</option>
                        <option :value="true">Residential</option>
                      </select>
                    </div>

                     <div class="form-group">
                      <breeze-label for="phone" value="Phone" />
                      <input name="phone" id="phone" type="text" class="form-control" placeholder="Phone" v-model="form.phone" required />
                    </div>

                    <div class="form-group">
                      <breeze-label for="address" value="Address 1" />
                      <input name="address" id="address1" type="text" class="form-control" placeholder="Address" v-model="form.address" required />
                    </div>
                  </div>                
                </div>
                          
              <div class="order-button">
                <input type="submit" value="Update Address" class="btn btn-danger" />
              </div>
            </div>
           
          </form>
        </div><!-- subscription -->
      </div><!-- container -->
    </section>
         
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
        data() {
            return {
                form: this.$inertia.form({
                    id:this.address.id,
                    fullname: this.address.fullname,          
                    country_id: this.address.country_id,
                    state: this.address.state,
                    city: this.address.city,
                    phone: this.address.phone,
                    address: this.address.address,
                    is_residential: Boolean(this.address.is_residential),
                })
            };
        },
        props: {
            auth: Object,
            address:Object,
            countries:Object,
        },
        methods : {
          	submit() {
              this.form.put(this.route('address.update'))
            }
        }
    }
</script>
