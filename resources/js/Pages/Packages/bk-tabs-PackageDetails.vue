<template>
    <MainLayout>
        
    <div class="row" style="margin-top:20px;">
        <div class="col-md-12">
            <div class="row">

            </div>
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                        Package # {{ packag.package_no }}
                    </h2>
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
                                    aria-selected="true">Package Details </button>
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
                                    aria-selected="false">Service Requests</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button 
                                    v-on:click="setActiveTabAB('tab3')" 
                                    :class="getTabClass('tab3')" 
                                    id="pills-profile-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#pills-profile" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="pills-profile" 
                                    aria-selected="false">Images</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button 
                                    v-on:click="setActiveTabAB('tab4')" 
                                    :class="getTabClass('tab4')" 
                                    id="pills-profile-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#pills-profile" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="pills-profile" 
                                    aria-selected="false">Charges/Totals</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">                                
                                <div :class="getTabPaneClass('tab1')" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" style="background-color:white;">                                    
                                    
                                     <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Order Id</th>
                                                <th>Tracking # </th>
                                                <th>From </th>
                                                <th>Receive Date </th>
                                                <th scope="col">Details</th>
                                                <th scope="col">Image</th>  
                                                <th></th>                                              
                                            </tr>
                                        </thead>
                                        <tbody> 
                                            <template v-for="order in packag.orders" :key="order.id">
                                                <tr>
                                                    <td>{{ order.id }} </td>
                                                    <td>{{ order.tracking_number_in }} </td>
                                                    <td>{{ order.received_from }} </td>
                                                    <td>{{ order.created_at }} </td>
                                                    <td>
                                                        Dimentions : {{ order.package_length }} {{ order.dim_unit }} x {{ order.package_width }} {{ order.dim_unit }} x {{ order.package_height }} {{ order.dim_unit }} <br>
                                                        Weight : {{ order.package_weight }} {{ order.weight_unit }} 
                                                    </td>                                                                    
                                                    <td>
                                                        <a :href="route('orders.show', order.id)" class="link-primary">Details</a>
                                                    </td>                                                                    
                                                </tr>
                                            </template>
                                        </tbody>                                                    
                                    </table>

                                    <div class="row" style="margin-top:10px;margin-bottom-10px;">
                                        <div class="col-md-12">
                                            <template v-if="(packag.status == 'open' || packag.status == 'filled') && ($page.props.auth.user.type == 'customer')">
                                                <inertia-link class="btn btn-success" :href="route('packages.custom', packag.id)">
                                                    <span v-if="packag.status == 'open'">Fill Customs form </span>
                                                    <span v-else>Edit Custom Form </span>                                                    
                                                </inertia-link>
                                                <p style="color :red;">Customs Declaration form already filled, It will not be editable once package is labeled for shipment.</p>
                                            </template>
                                            <template v-if="(packag.status =='filled') && ($page.props.auth.user.type == 'admin')">
                                                <p style="color :green;">Customer has filled custom declaration form.</p>                                                
                                                <inertia-link class="btn btn-primary" :href="route('packages.custom', packag.id)">
                                                    <span>Edit Custom Form </span>                                                    
                                                </inertia-link>
                                            </template>
                                        </div>
                                    </div>
                                    <template v-if="($page.props.auth.user.type == 'admin')">
                                        <div class="row" style="margin-top:20px;">
                                            <div class="col-md-12"> 
                                                <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                                                    Label Order For Shipment.
                                                </h2>
                                                <p>Enter new package dimentions and ship order</p>
                                                <p>These are consolidated package dimentions.</p>
                                            </div>

                                            <div class="col-md-12">
                                                <form @submit.prevent="submitShipForm">    
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <breeze-label for="warehouse_id" value="Weight Unit" />
                                                                <select name="weight_unit" class="form-select" v-model="form_ship.weight_unit" required>
                                                                    <option value="lb">Lb</option>
                                                                    <option value="kg">Kg</option> 
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <breeze-label for="package_weight" value="Package Weight" />
                                                                <input v-model="form_ship.package_weight" name="package_weight" id="package_weight" type="number" class="form-control" placeholder="Package Weight" required />
                                                            </div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <breeze-label for="dim_unit" value="Dimention Unit" />
                                                                <select name="dim_unit" class="form-select" v-model="form_ship.dim_unit" required>
                                                                <option value="in">Inch</option>
                                                                <option value="cm">Cm</option> 
                                                                </select>
                                                            </div>  
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <breeze-label for="package_length" value="Package Length" />
                                                                <input v-model="form_ship.package_length" name="package_length" id="package_length" type="number" class="form-control" placeholder="Package Length" required />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <breeze-label for="package_height" value="Package Height" />
                                                                <input v-model="form_ship.package_height" name="package_height" id="package_height" type="number" class="form-control" placeholder="Package Height" required />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <breeze-label for="package_weight" value="Package Width" />
                                                                <input v-model="form_ship.package_width" name="package_width" id="package_width" type="number" class="form-control" placeholder="Package Width" required />
                                                            </div>  
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12"> 
                                                            <input type="submit" value="Update Package" class="btn btn-success float-left" />
                                                            <!-- <a v-on:click="submitShipForm()" class="btn btn-success float-left">
                                                                <span>Ship Order</span>
                                                            </a> -->
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>   

                                        </div>
                                    </template>

                                </div>
                                <div :class="getTabPaneClass('tab2')" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" style="background-color:white;">
                                    <div class="row">
                                        <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                                            Service Requests 
                                        </h2>
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Service </th>
                                                    <template v-if="$page.props.auth.user.type == 'customer'">
                                                        <th scope="col">
                                                            Your Message
                                                        </th>
                                                    </template>
                                                    <template v-if="$page.props.auth.user.type == 'admin'">
                                                        <th scope="col">
                                                            Customer Message
                                                        </th>
                                                    </template>
                                                    <th scope="col">Status</th> 
                                                    <th scope="col">Admin Response</th>
                                                    <th scope="col">Charges</th>
                                                    <template v-if="$page.props.auth.user.type == 'admin'">
                                                        <th scope="col">                                                        
                                                        </th>
                                                    </template>                                         
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="request in service_requests" :key="request.id">
                                                    <td>
                                                        {{ request.service_title }}                                                    
                                                    </td>
                                                    <td>
                                                        {{ request.customer_message }}                                                    
                                                    </td>
                                                    <td>
                                                        <span v-bind:class="getLabelClass(request.status)" style="padding :5px;"> {{ request.status }} </span>                                                   
                                                    </td>
                                                    <td>
                                                        {{ request.admin_message }}                                                    
                                                    </td>
                                                    <td>
                                                        $ {{ request.price }}                                                    
                                                    </td>
                                                    <template v-if="$page.props.auth.user.type == 'admin'">
                                                    <td>
                                                         <a v-on:click="setServiceResponse(request)" class="link-primary">Respond</a>                                                   
                                                    </td>
                                                    </template>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="row" v-if="$page.props.auth.user.type == 'admin'">
                                        <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                                            Handle Service Request 
                                        </h2>                                             

                                        <div  class="col-md-8">
                                            <template v-if="form_respond.request !=null">                                        
                                                <form @submit.prevent="submitRespondForm">                                                    
                                                    <div class="form-group">
                                                        <p>Message for Customer</p>
                                                        <textarea v-model="form_respond.admin_message" 
                                                        name="admin_message" 
                                                        id="admin_message" 
                                                        class="form-control" 
                                                        placeholder="Message for Customer" 
                                                        rows="4"
                                                        style="resize:none;"
                                                        required >
                                                        </textarea>
                                                    </div>                                                    
                                                    <p style="color:re">Service : <strong>{{ form_respond.request.service_title }}</strong></p>
                                                    <p style="color:re">Charges : <strong>${{ form_respond.request.price }}</strong></p>
                                                    <div class="order-button">
                                                        <input type="submit" value="Update Request" class="btn btn-danger" />

                                                        <a v-on:click="requestComplete()" class="btn btn-success float-right">
                                                            <span>Complete Request</span>
                                                        </a>

                                                        <a v-on:click="requestReject()" class="btn btn-danger float-right" style="margin-right:10px;">
                                                            <span>Reject Request</span>
                                                        </a>

                                                    </div>
                                                    </form>
                                                </template>
                                        </div>   
                                    </div>

                                    <div class="row" v-if="$page.props.auth.user.type == 'customer'">
                                        <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                                            Available Services 
                                        </h2>                                             
                                        <div  class="col-md-8">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Service </th>
                                                        <th scope="col">Details</th>
                                                        <th scope="col">Fees</th> 
                                                        <th scope="col"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="service in services" :key="service.id">
                                                        <td>
                                                            {{ service.title }}                                                    
                                                        </td>
                                                        <td>
                                                            {{ service.description }}                                                    
                                                        </td>
                                                        <td>
                                                            $ {{ service.price }}                                                    
                                                        </td>
                                                        <td style="width:110px;">
                                                            <a v-on:click="setActiveService(service)" class="link-primary"> Make Request</a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div  class="col-md-4">
                                                    <template v-if="form.service_id !=null">                                        
                                                    <form @submit.prevent="submit">
                                                    
                                                    <div class="form-group">
                                                        <breeze-label for="notes" value="Message for Admin" />
                                                        <textarea v-model="form.customer_message" 
                                                        name="notes" 
                                                        id="notes" 
                                                        class="form-control" 
                                                        placeholder="Message for Admin" 
                                                        rows="4"
                                                        style="resize:none;"
                                                        required >
                                                        </textarea>
                                                    </div>                                                    

                                                    <p style="color:red;">Are you sure you want to use service? Add your message for admin and continue</p>
                                                    <p style="color:red;">Every service request is charged separately, so if you have already requested any service wait for system response.</p>
                                                    <p style="color:re">Service : <strong>{{ form.service.title }}</strong></p>
                                                    <p style="color:re">Charges : <strong>${{ form.service.price }}</strong></p>
                                                    <div class="order-button">
                                                        <input type="submit" value="Make Request" class="btn btn-danger" />
                                                    </div>
                                                    </form>
                                                </template>
                                        </div>   
                                    </div>
                                </div>
                                <div :class="getTabPaneClass('tab3')" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" style="background-color:white;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                                                Order/Item Images 
                                            </h2>
                                        </div>
                                                       
                                        <div v-for="image in images" :key="'image'+image.order_id" class="col-md-3">
                                            <div class="text-center">
                                                <img style="width:100px;height:auto;" class="img-thumbnail" :src="imgURL(image.img_url)" />
                                            </div>
                                        </div>     
                                    </div>
                                </div>
                                <div :class="getTabPaneClass('tab4')" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" style="background-color:white;">
                                    <div class="row">
                                        <div class="col-md-12">

                                            <table class="table table-striped">                                                    
                                                <thead>
                                                    <tr>
                                                        <th colspan="4" style="text-align:center;">
                                                            <strong>Package Service Charges</strong>
                                                        </th>
                                                    </tr>

                                                    <tr>
                                                        <th>Service Name </th>
                                                        <th scope="col">Price </th>
                                                    </tr>                                                    
                                                </thead>

                                                <tbody>
                                                    <template  v-for="order in order_charges" :key="order.id">
                                                        <tr>
                                                            <td>Order Pickup charges </td>
                                                            <td>${{ order.service_charges }}</td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>  
                                                    </template>
                                                    <template v-for="service in service_requests " :key="service.id">
                                                        <tr>
                                                            <td>{{ service.service_title }}</td>
                                                            <td>${{ service.price }}</td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>  
                                                    </template>
                                                    <tr>
                                                        <td colspan="2" style="text-align:center;">
                                                            <strong>Sub Total</strong>
                                                        </td>
                                                        <td>${{ getServiceSubTotal() }}</td>
                                                        <td></td>
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

    import BreezeLabel from '@/Components/Label'
    export default {
        components: {
            BreezeAuthenticatedLayout,
            MainLayout,

            BreezeLabel
        },
        data(){
            return{
                form: this.$inertia.form({
                    package_id:this.packag.id,
                    service_id: null,
                    customer_message : '',
                    service:null                                      
                }),
                form_respond: this.$inertia.form({
                    admin_message : '',
                    status : 'pending',
                    request:null                                      
                }),
                form_ship: this.$inertia.form({
                    package_id:this.packag.id,
                    status : 'shipped',
                    weight_unit: this.packag.weight_unit,
                    dim_unit: this.packag.dim_unit,
                    package_weight:this.packag.package_weight,
                    package_length:this.packag.package_length,
                    package_width:this.packag.package_width,
                    package_height:this.packag.package_height,
                }),
                tabs : {
                    tab1:true,
                    tab2:false,
                    tab3:false,
                    tab4:false,
                },
            }            
        },
        props: {
            auth: Object,
            packag:Object,
            services: Object,
            service_requests: Object,
            images:Object,
            order_charges:Object,
        },
        computed:{
            siuteNum(){
                return 4000 + this.$page.props.auth.user.id;
            },
        },
        mounted() {
            console.log(this.packag);
        },
        methods:{
          	submit() {
              this.form.post(this.route('packages.service-request'));
              this.form.reset();
              location.reload();
              //Inertia.reload({ only: ['service_requests'] })
            },
            setActiveService(service){
                this.form.service_id = service.id;                
                this.form.service = service;
            },            
            submitRespondForm() {
              this.form_respond.post(this.route('packages.service-handle'));
              this.form_respond.reset();
              //location.reload();
            },
            setServiceResponse(request){
                this.form_respond.request_id = request.id;                
                this.form_respond.request = request;
            },
            requestComplete() {
                this.form_respond.status = 'served';
                this.submitRespondForm();
            },
            requestReject() {
                this.form_respond.status = 'rejected';
                this.submitRespondForm();
            },
            submitShipForm(){
              this.form_ship.post(this.route('packages.ship-package'));
              //this.form_ship.reset();
              //location.reload();
            },
            getServiceSubTotal(){
                 let request_total =  this.service_requests.reduce((acc, item) => {
                    return acc + (parseInt(item.price));
                    }, 
                0);

                let pickup_total =  this.order_charges.reduce((acc, item) => {
                    return acc + (parseInt(item.service_charges));
                    }, 
                0);
                return request_total+pickup_total;

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
                return "/uploads/"+url;
            },
            makePackageUrl(order_id){
                return route('package.create')+'?order_id='+order_id;
            },
            getLabelClass(status){
                switch(status) {
                case 'pending':
                    return 'label bg-warning';
                    break;
                case 'served':
                    return 'label bg-success';
                    break;
                case 'rejected':
                    return 'label bg-danger';
                    break;
                }
            }
        }        
    }
</script>
