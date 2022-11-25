<template>
    <MainLayout>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
              <div class="col-md-6" v-for="(address,index) in addresses" v-bind:key="address.id">
                <div class="card">
                  <div class="card-header">
                    {{ address.name }} Mailing Address
                  </div>
                  <div class="card-body">
                    <div class="address-card mb-4">
                      <p><strong>Full Name:</strong><span class="copy-item" :id="'copy-item-'+count" v-on:click="copyContent($event)">{{ siuteNum }} – {{ $page.props.auth.user.name }}</span> <span v-if="copied_id=='copy-item-'+count++" style="color:green">Copied!</span></p>
                      <p><strong>Street Address:</strong><span class="copy-item" :id="'copy-item-'+count" v-on:click="copyContent($event)">{{ address.address }}</span><span v-if="copied_id=='copy-item-'+count++" style="color:green">Copied!</span></p>
                      <p><strong>City:</strong><span class="copy-item" :id="'copy-item-'+count" v-on:click="copyContent($event)">{{ address.city }}</span><span v-if="copied_id=='copy-item-'+count++" style="color:green">Copied!</span></p>
                      <p><strong>Suite #:</strong><span class="copy-item" :id="'copy-item-'+count" v-on:click="copyContent($event)"> {{ siuteNum }} </span><span v-if="copied_id=='copy-item-'+count++" style="color:green">Copied!</span></p>
                      <p><strong>State:</strong><span class="copy-item" :id="'copy-item-'+count" v-on:click="copyContent($event)">{{ address.state }} </span><span v-if="copied_id=='copy-item-'+count++" style="color:green">Copied!</span></p>
                      <p><strong>Zip code:</strong><span class="copy-item" :id="'copy-item-'+count" v-on:click="copyContent($event)">{{ address.zip }} </span><span v-if="copied_id=='copy-item-'+count++" style="color:green">Copied!</span></p>
                      <p><strong>Phone Number:</strong><span class="copy-item" :id="'copy-item-'+count" v-on:click="copyContent($event)">{{ address.phone }}</span><span v-if="copied_id=='copy-item-'+count++" style="color:green">Copied!</span></p>
                    </div>
                    <a href="javascript:void(0)" class="btn btn-primary" @click="copyToClipBoard(address,index)">
                      <span v-if="!address.clicked"><i class="fa fa-copy mr-1"></i>Click Here To Copy</span>
                      <span v-if="address.clicked">Copied</span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <div class="col-md-12" v-show="false">
            <div class="card">
                <div class="card-header">
                    <h5>Accounts Balance</h5>
                </div>
                <div class="card-body">
                    <div class="card-address">
                        <p><strong>Total :</strong> $500 </p>
                        <p><strong>Used:</strong>$300 </p>
                        <p><strong>Available:</strong>$200 </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top:20px;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Orders History</h5>
                </div>
                <div class="card-body">
                    <div class="row my-4">
                        <div class="col-md-3">
                            <button type="button" class="btn btn-light w-100"  v-on:click="searchOrder('arrived')">Arrived</button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-light w-100"  v-on:click="searchOrder('labeled')">Labeled</button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-light w-100"  v-on:click="searchOrder('shipped')">Shipped</button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-light w-100"  v-on:click="searchOrder('rejected')">Rejected</button>
                        </div>
                    </div>
                    <order-list v-bind="$props"></order-list>
                </div>
            </div>
        </div>
    </div>

    </MainLayout>
</template>
<style scoped>
.card{
    margin-top: 25px;
}
.card-body{ padding:0.8rem;}
.card-body p strong{
    color: #212529; margin-right: 12px; 
}
.address-card{
    height:190px;
}
.copy-item:hover{
    cursor: pointer;
    border:1px dotted green;
}
.row {
  margin-right: 0px !important;
}
</style>
<script>
    import MainLayout from '@/Layouts/Main'
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
    import OrderList from './Orders/Partials/OrderList.vue'
    import { Inertia } from "@inertiajs/inertia";

    export default {
        components: {
            BreezeAuthenticatedLayout,
            MainLayout,
            OrderList
        },
        data(){
            return{
                count:0,
                addresses:{},
                copied_id: '',
                address1 : '3578 w savanna st Suite #:AD - 400'+ this.$page.props.auth.user.id + ',Anaheim CA ,92804',
                address2 : '1217 old cooch bridge rd Suite #:AD - 400'+ this.$page.props.auth.user.id +' ,Newark, Delaware ',
                copied1:false,
                copied2:false,
                order_form: {
                    order_status: this.filter.order_status,
                    processing: false,
                },
            }            
        },
        props: {
            auth: Object,
            errors: Object,
            orders: Object,
            filter: Object,
        },
        computed:{
            siuteNum(){
                return 4000 + this.$page.props.auth.user.id;
            },
        },
        methods:{
            copyToClipBoard(address){
              var text = address.address +', '+ address.city +', ' + address.state +', ' + address.zip;
                navigator.clipboard.writeText(text).then(function() {
                }, function(err) {
                    console.error('Async: Could not copy text: ', err);
                });
            },
            copyContent(event){
                var text = event.target.innerText;
                this.copied_id = event.target.id;
                navigator.clipboard.writeText(text).then(function() {
                }, function(err) {
                    console.error('Async: Could not copy text: ', err);
                });
            },
            getMailingAddress(){
              axios.get(this.route('getMailingAddress')).then((response) => {
                this.addresses = response.data.data;
                console.log(this.addresses);
              })
            },
            searchOrder(status){
                this.order_form.order_status = status;
                this.order_form.processing = true;
                Inertia.post(route("dashboard"),this.order_form, {
                    preserveScroll: true,
                    onStart: () => {
                        this.order_form.processing = true;
                    },
                    onFinish: () => {
                        this.order_form.processing = false;
                    },
                });
            },
        },
        created(){
          this.getMailingAddress()
        }
    }
</script>
