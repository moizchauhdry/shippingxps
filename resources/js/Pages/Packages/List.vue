<template>
    <MainLayout>
        
    <div class="row" style="margin-top:20px;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                        Packages
                    </h2>
                </div>

                <div class="row">

                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-pills nav-justified mb-3" id="pills-tab " role="tablist">
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
                                    aria-selected="true">Orders In account ({{arrived_count}})</button>
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
                                    aria-selected="false">Ready for Mail Out ({{ labeled_count }})</button>
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
                                    aria-selected="false">Sent/Shipped ({{ shipped_count }})</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button 
                                    v-on:click="setActiveTabAB('tab4')" 
                                    :class="getTabClass('tab4')" 
                                    id="pills-contact-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#pills-contact" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="pills-contact" 
                                    aria-selected="false">Thrashed/Rejected ({{ rejected_count }})</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div :class="getTabPaneClass('tab1')" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Image</th>
                                                <th scope="col">Package</th>
                                                <th scope="col"></th>                         
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="order in arrived" :key="order.id">
                                                <td>{{ order.id }}</td>
                                                <td>
                                                    <div class="text-center">
                                                        <img style="width:100px;height:auto;" class="img-thumbnail" :src="imgURL(order)" />
                                                    </div>
                                                </td>
                                                <td>
                                                    Order Id : <b>{{ order.order_id }}</b> <br>
                                                    Tracking IN :<b> {{ order.tracking_number }}</b> <br>
                                                    Status : {{ order.status }} <br>
                                                    Tracking OUT :<b> ---- </b> <br>
                                                    
                                                    Dim : {{ order.package_length }} in X {{ order.package_width }} in X {{ order.package_height }} in 
                                                    Weight : {{ order.package_weight }} 

                                                </td>
                                                <td>
                                                    Customs Declaration : <b>OK</b> <br>
                                                    Mail Out :<b> Fedex International </b> <br>

                                                </td>
                                                <td>
                                                    <inertia-link class="link-primary" :href="route('package.edit',order.id)">
                                                        <span>Custom Declaration form</span>
                                                    </inertia-link>
                                                </td>
                                            </tr>
                                            <tr v-if="arrived && arrived.length > 1">
                                            <td colspan="5">                     
                                                </td> 
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div :class="getTabPaneClass('tab2')" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Order Id</th>
                                                <th scope="col">Tracking #</th>                         
                                                <th scope="col">Receiver</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          <tr v-for="order in labeled" :key="order.id">
                                                <td>{{ order.id }}</td>
                                                <td>
                                                    <div class="text-center">
                                                        <img style="width:100px;height:auto;" class="img-thumbnail" :src="imgURL(order)" />
                                                    </div>
                                                </td>
                                                <td>
                                                    Order Id : <b>{{ order.order_id }}</b> <br>
                                                    Tracking IN :<b> {{ order.tracking_number }}</b> <br>
                                                    Status : {{ order.status }} <br>
                                                    Tracking OUT :<b> ---- </b> <br>
                                                    
                                                    Dim : {{ order.package_length }} in X {{ order.package_width }} in X {{ order.package_height }} in 
                                                    Weight : {{ order.package_weight }} 

                                                </td>
                                                <td>
                                                    Customs Declaration : <b>OK</b> <br>
                                                    Mail Out :<b> Fedex International </b> <br>

                                                </td>
                                                <td>
                                                    <inertia-link class="link-primary" :href="route('package.edit',order.id)">
                                                        <span>Custom Declaration form</span>
                                                    </inertia-link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div :class="getTabPaneClass('tab3')" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Order Id</th>
                                                <th scope="col">Tracking #</th>                         
                                                <th scope="col">Receiver</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="order in shipped" :key="order.id">
                                                <td>{{ order.id }}</td>
                                                <td>{{ order.order_id }}</td>
                                                <td>{{ order.tracking_number }}</td>
                                                <td>{{ order.receiver_fullname }}</td>
                                                <td>
                                                    <inertia-link class="link-primary" :href="route('orders.show', order.id)">
                                                        <span>View</span>
                                                    </inertia-link>
                                                    &nbsp;|&nbsp;
                                                    <inertia-link class="link-primary" :href="route('order.edit', order.id)">
                                                        <span>Edit</span>
                                                    </inertia-link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div :class="getTabPaneClass('tab4')" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Order Id</th>
                                                <th scope="col">Tracking #</th>                         
                                                <th scope="col">Receiver</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="order in rejected" :key="order.id">
                                                <td>{{ order.id }}</td>
                                                <td>{{ order.order_id }}</td>
                                                <td>{{ order.tracking_number }}</td>
                                                <td>{{ order.receiver_fullname }}</td>
                                                <td>
                                                    <inertia-link class="link-primary" :href="route('orders.show', order.id)">
                                                        <span>View</span>
                                                    </inertia-link>
                                                    &nbsp;|&nbsp;
                                                    <inertia-link class="link-primary" :href="route('order.edit', order.id)">
                                                        <span>Edit</span>
                                                    </inertia-link>
                                                </td>
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
    height:130px;
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
                address1 : '3578 w savanna st Suite #:AD - 400'+ this.$page.props.auth.user.id + ',Anaheim CA ,92804',
                address2 : '1217 old cooch bridge rd Suite #:AD - 400'+ this.$page.props.auth.user.id +' ,Newark, Delaware ',
                copied1:false,
                copied2:false,
                tabs : {
                    tab1:true,
                    tab2:false,
                    tab3:false,
                    tab4:false
                }
            }            
        },
        props: {
            auth: Object,
            errors: Object,
            arrived:Object,
            labeled:Object,
            shipped:Object,
            rejected:Object,
            arrived_count : Number,
            labeled_count : Number,
            shipped_count : Number,
            rejected_count : Number,
        },
        computed:{
            siuteNum(){
                return 4000 + this.$page.props.auth.user.id;
            },
        },
        methods:{
            copyToClipBoard(address){
                if(address.includes("92804")){
                    this.copied1 = true;
                }else{
                    this.copied2 = true;
                }
                var text = address;
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
            imgURL(url) {
                //console.log(order);
                return "/uploads/1628359763_banner3.jpg";
            },
            makePackageUrl(order_id){
                return route('package.create')+'?order_id='+order_id;
            }
        }        
    }
</script>
