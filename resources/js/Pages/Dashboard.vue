<template>
    <MainLayout>
<!--      <div class="row" v-if="$page.props.notifications.length">-->
<!--        <div class="col">-->
<!--          <span class="alert alert-success d-block" v-for="notification in $page.props.notifications" :key="notification.id">-->
<!--            <strong v-if="notification.read_at == null"> {{ notification.data.message }}</strong>-->
<!--          </span>-->
<!--          <inertia-link :href="route('notifications')" class="float-right">View All Notifications</inertia-link>-->
<!--        </div>-->
<!--      </div>-->
    <div class="row">
        <div class="col-md-6">
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
                    <br/><br/>
                    <a href="javascript:void(0)" class="btn btn-primary" @click="copyToClipBoard(address,index)">
                      <span v-if="!address.clicked">Click Here To Copy</span>
                      <span v-if="address.clicked">Copied</span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <div class="col-md-6" v-show="false">
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
                    <div class="col-md-12">
                      <ul class="nav nav-pills nav-justified mb-3 mt-3" id="pills-tab " role="tablist">
                        <li class="nav-item" role="presentation">
                          <button
                              v-on:click="setActiveTabAB('tab1')"
                              :class="getTabClass('tab1')"
                              id="pills-home-tab"
                              data-bs-toggle="pill"
                              data-bs-target="#pills-home"
                              type="button"
                              role="tab"
                              aria-controls="pills-home"
                              aria-selected="true">Arrived</button>
                        </li>
                        <li class="nav-item" role="presentation">
                          <button
                              v-on:click="setActiveTabAB('tab2')"
                              :class="getTabClass('tab2')"
                              id="pills-profile-tab"
                              data-bs-toggle="pill"
                              data-bs-target="#pills-profile"
                              type="button"
                              role="tab"
                              aria-controls="pills-profile"
                              aria-selected="false">Labeled</button>
                        </li>
                        <li class="nav-item" role="presentation">
                          <button
                              v-on:click="setActiveTabAB('tab3')"
                              :class="getTabClass('tab3')"
                              id="pills-contact-tab"
                              data-bs-toggle="pill"
                              data-bs-target="#pills-contact"
                              type="button"
                              role="tab"
                              aria-controls="pills-contact"
                              aria-selected="false">Shipped</button>
                        </li>
                      </ul>
                      <div class="tab-content" id="pills-tabContent">
                        <div :class="getTabPaneClass('tab1')" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                          <table class="table table-striped">
                            <thead>
                            <tr>
                              <th scope="col">Order Id</th>
                              <th v-if="$page.props.auth.user.type == 'admin'" scope="col">Customer Name</th>
                              <th scope="col">Tracking #</th>
                              <th scope="col">Warehouse</th>
                              <th scope="col">Received Date</th>
                              <template v-if="$page.props.auth.user.type == 'admin'">
                                <th scope="col"></th>
                              </template>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="order in arrived" :key="order.id">
                              <td>{{ order.id }}</td>
                              <td v-if="$page.props.auth.user.type == 'admin'">{{ order.customer.name }}</td>
                              <td>{{ order.tracking_number_in }}</td>
                              <td>{{ order.warehouse.name }}</td>
                              <td>{{ order.created_at }}</td>
                              <template v-if="$page.props.auth.user.type == 'admin'">
                                <td>
                                  <inertia-link class="link-primary" :href="route('orders.show', order.id)">
                                    <span>View</span>
                                  </inertia-link>
                                  &nbsp;|&nbsp;
                                  <inertia-link class="link-primary" :href="route('order.edit', order.id)">
                                    <span>Edit</span>
                                  </inertia-link>
                                </td>
                              </template>
                            </tr>

                            </tbody>
                          </table>
                        </div>
                        <div :class="getTabPaneClass('tab2')" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                          <table class="table table-striped">
                            <thead>
                            <tr>
                              <th scope="col">Order Id</th>
                              <th v-if="$page.props.auth.user.type == 'admin'" scope="col">Customer Name</th>
                              <th scope="col">Tracking #</th>
                              <th scope="col">Warehouse</th>
                              <th scope="col">Received Date</th>
                              <template v-if="$page.props.auth.user.type == 'admin'">
                                <th scope="col"></th>
                              </template>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="labeler in labeled" :key="labeler.id">
                              <td>{{ labeler.id }}</td>
                              <td v-if="$page.props.auth.user.type == 'admin'">{{ labeler.customer.name }}</td>
                              <td>{{ labeler.tracking_number_in }}</td>
                              <td>{{ labeler.warehouse.name }}</td>
                              <td>{{ labeler.created_at }}</td>
                              <template v-if="$page.props.auth.user.type == 'admin'">
                                <td>
                                  <inertia-link class="link-primary" :href="route('orders.show', labeler.id)">
                                    <span>View</span>
                                  </inertia-link>
                                  &nbsp;|&nbsp;
                                  <inertia-link class="link-primary" :href="route('order.edit', labeler.id)">
                                    <span>Edit</span>
                                  </inertia-link>
                                </td>
                              </template>
                            </tr>
                            </tbody>
                          </table>
                        </div>
                        <div :class="getTabPaneClass('tab3')" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                          <table class="table table-striped">
                            <thead>
                            <tr>
                              <th scope="col">Order Id</th>
                              <th v-if="$page.props.auth.user.type == 'admin'" scope="col">Customer Name</th>
                              <th scope="col">Tracking #</th>
                              <th scope="col">Warehouse</th>
                              <th scope="col">Received Date</th>
                              <template v-if="$page.props.auth.user.type == 'admin'">
                                <th scope="col"></th>
                              </template>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="order in shipped" :key="order.id">
                              <td>{{ order.id }}</td>
                              <td v-if="$page.props.auth.user.type == 'admin'">{{ order.customer.name }}</td>
                              <td>{{ order.tracking_number_in }}</td>
                              <td>{{ order.warehouse.name }}</td>
                              <td>{{ order.created_at }}</td>
                              <template v-if="$page.props.auth.user.type == 'admin'">
                                <td>
                                  <inertia-link class="link-primary" :href="route('orders.show', order.id)">
                                    <span>View</span>
                                  </inertia-link>
                                  &nbsp;|&nbsp;
                                  <!--                      <inertia-link class="link-primary" :href="route('order.edit', order.id)">
                                                          <span>Edit</span>
                                                        </inertia-link>-->
                                </td>
                              </template>
                            </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
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

    export default {
        components: {
            BreezeAuthenticatedLayout,
            MainLayout
        },
        data(){
            return{
                count:0,
                addresses:{},
                address1 : '3578 w savanna st Suite #:AD - 400'+ this.$page.props.auth.user.id + ',Anaheim CA ,92804',
                address2 : '1217 old cooch bridge rd Suite #:AD - 400'+ this.$page.props.auth.user.id +' ,Newark, Delaware ',
                copied1:false,
                copied2:false,
                tabs : {
                    tab1:true,
                    tab2:false,
                    tab3:false
                },
                copied_id: '',
            }            
        },
        props: {
            auth: Object,
            errors: Object,
            arrived:Object,
            labeled:Object,
            shipped:Object,
            rejected:Object
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
            setActiveTabAB(tab){
                
                for (var key in this.tabs) {
                    if(key === tab){
                        this.tabs[key] = true;
                    }else{
                        this.tabs[key] = false;
                    }
                }
            },
            getTabClass(tab){

                if(this.tabs[tab] === true){
                      return 'nav-link active';
                }else{
                    return 'nav-link';
                }
                
            },
            getTabPaneClass(tab){

                if(this.tabs[tab] === true){
                     return 'tab-pane show active';
                }else{
                    return 'tab-pane fade';
                }
            },
            getMailingAddress(){
              axios.get(this.route('getMailingAddress')).then((response) => {
                this.addresses = response.data.data;
                console.log(this.addresses);
              })
            }
        },
        created(){
          this.getMailingAddress()
        }
    }
</script>
