<template>
    <div class="col-md-12" v-if="$page.props.auth.user.type == 'customer' && packag.status == 'open'">
        <div class="card">
        <div class="card-header">
            <h3 class="text-uppercase">Shipping Address</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <form @submit.prevent="submitShippingAddressForm">
                <div class="form-group col-md-4">
                    <select name="address_book_id" class="form-select text-uppercase" 
                    v-model="address_form.address_book_id" required @change="submitShippingAddressForm">
                    <option :value="0">--Select Address--</option>
                    <template v-for="(address) in shipping_address" :key="address.id">
                        <option  :value="address.id">
                        {{ address.fullname}}, {{ address.address}}
                        </option>
                    </template>                      
                    </select>
                </div>
                </form>
            </div>
            <div class="row">
                <div>
                <template v-if="packag.address_type == 'international'">
                    <inertia-link class="btn btn-primary btn-sm m-1" :href="route('packages.custom', packag.id)">
                    <i class="fa fa-copy mr-1"></i>Customs Declaration Form
                    </inertia-link>
                </template>
                <!-- <template v-if="(packag.status !='open')">
                    <a target="_blank" class="btn btn-info btn-sm m-1" :href="route('packages.pdf', packag.id)">
                    <i class="fa fa-print mr-1"></i>Print Commercial Invoice
                    </a>
                </template> -->
                </div>
            </div>
        </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Shipping Address Component",
        props: {
            packag:Object,
            shipping_address: Object,
        },
        data() {
            return {
                address_form: this.$inertia.form({
                    package_id: this.packag.id,
                    address_book_id: this.packag.address_book_id
                }),
            }
        },
        methods:{
            submitShippingAddressForm() {
                this.address_form.post(this.route('packages.address.update'));
            },
        }
    }
</script>

