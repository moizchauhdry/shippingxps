<template>
    <MainLayout>        
    <div class="row" style="margin-top:20px;">
        <div class="col-md-12">
            <div class="row">
                <FlashMessages />
            </div>
            <div class="row">
                <div class="col-md-10">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                    Package # {{ packag.package_no }}
                </h2>
                </div>
                <div class="col-md-2">
                    <span v-bind:class="getLabelClass(packag.status)" style="color:black;padding:5px;">{{packag.status}}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>Packages/Orders Included</h3>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Serial #</th>
                                            <th>Tracking IN </th>
                                            <th>From </th>
                                            <th>Receive Date </th>
                                            <th scope="col">Image</th>  
                                            <th></th>                                              
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        <template v-for="(order,index) in packag.orders" :key="order.id">
                                            <tr>
                                                <td>{{index+1}}</td>
                                                <td>{{ order.tracking_number_in }} </td>
                                                <td>{{ order.received_from }} </td>
                                                <td>{{ order.created_at }} </td>
                                                <td>
                                                    <a :href="route('orders.show', order.id)" class="link-primary">Details</a>
                                                </td>                                                                    
                                            </tr>
                                        </template>
                                    </tbody>                                                    
                            </table>
                            <template v-if="($page.props.auth.user.type == 'customer')">
                                <a class="link-primary" :href="route('packages.custom', packag.id)">                                    
                                    <span>Customs Declaration Form </span>                                                    
                                </a>&nbsp;&nbsp;
                            </template>
                            <template v-if="(packag.status !='open')">
                                <a target="_blank" class="link-success" :href="route('packages.pdf', packag.id)">
                                    <span>Print Commercial Invoice Form </span>                                                    
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" v-if="(packag.status !='open') && $page.props.auth.user.type == 'customer'">
                 <div class="col-md-12"> 
                    <div class="card">
                        <div class="card-header">
                            <h3>Consolidated Package</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">                               
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th colspan="2">Consolidated Package Details</th>                                             
                                            </tr>
                                        </thead>
                                        <tbody> 
                                            <tr>
                                                <td>
                                                    Package
                                                </td>
                                                <td>
                                                    {{packag.package_no}}
                                                </td>                                                                    
                                            </tr>
                                            <tr>
                                                <td>
                                                    Weight
                                                </td>
                                                <td>
                                                    {{packag.package_weight}}
                                                </td>   
                                            </tr>
                                            <tr>                                                                 
                                                <td>
                                                    Dimensions
                                                </td>
                                                <td>
                                                    {{ packag.package_length }} {{ packag.dim_unit }} x {{ packag.package_width }} {{ packag.dim_unit }} x {{ packag.package_height }} {{ packag.dim_unit }}
                                                </td>                                                                    
                                            </tr>
                                            <template v-if="packag.tracking_number_out !=''">
                                            <tr>                                                                 
                                                <td>
                                                    Tracking Out
                                                </td>
                                                <td>
                                                    {{ packag.tracking_number_out }}
                                                </td>                                                                    
                                            </tr>
                                            </template>
                                        </tbody>                                                    
                                    </table>
                                    <template v-if="(packag.status == 'filled')&&(packag.package_length !=='' && packag.package_width !=='' && packag.package_height !=='')">
                                        <a class="btn btn-success" v-on:click="getShippingRates()">Get Shipping Rates</a>                            
                                    </template>
                                </div>
                                <div class="col-md-7">
                                    <table class="table" v-if="showEstimatedPrice">
                                        <thead>
                                            <tr>
                                                <th colspan="3">Available Shipping Services</th>                                             
                                            </tr>
                                            <tr>
                                                <th>Service</th>
                                                <th>Price </th>
                                                <th> </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <template v-for="service in shipping_services" :key="service" >
                                            <tr v-if="service.isReady === true">
                                                <td>{{ service.serviceLabel}}</td>
                                                <td>{{ service.totalAmount }} {{ service.currency }}</td>
                                                <td><a v-on:click="setShippingService(service)" class="btn btn-info">Confirm</a></td>
                                            </tr>
                                        </template>
                                        <tr colspan="3">
                                            <p style="color:red">Selected service cannot be changed. So make sure you choose correct service.</p>
                                        </tr>
                                        </tbody>    
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>

            <template v-if="this.hasConsolidationRequest">
              <template v-if="$page.props.auth.user.type == 'admin'">
                <template v-if="packag.status == 'open' || packag.status == 'consolidated'">
                  <div class="row" style="margin-top:20px;">
                    <div class="col-md-12">

                      <div class="card">
                        <div class="card-header">
                          <h3>Consolidated Package Dimensions</h3>
                        </div>
                        <div class="card-body">
                          <p style="color:red;">Customer has made consolidation request.</p>
                          <p>Enter new dimensions of package after consolidation.</p>

                          <form @submit.prevent="submitConsolidateForm">
                            <div class="row">
                              <div class="col-md-2">
                                <div class="form-group">
                                  <breeze-label for="warehouse_id" value="Weight Unit" />
                                  <select name="weight_unit" class="form-select" v-model="form_consolidate.weight_unit" required>
                                    <option value="lb">Lb</option>
                                    <option value="kg">Kg</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                  <breeze-label for="package_weight" value="Package Weight" />
                                  <input v-model="form_consolidate.package_weight" name="package_weight" id="package_weight" type="number" class="form-control" placeholder="Package Weight" required />
                                </div>
                              </div>

                              <div class="col-md-2">
                                <div class="form-group">
                                  <breeze-label for="dim_unit" value="Dimention Unit" />
                                  <select name="dim_unit" class="form-select" v-model="form_consolidate.dim_unit" required>
                                    <option value="in">Inch</option>
                                    <option value="cm">Cm</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                  <breeze-label for="package_length" value="Package Length" />
                                  <input v-model="form_consolidate.package_length" name="package_length" id="package_length" type="number" class="form-control" placeholder="Package Length" required />
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                  <breeze-label for="package_height" value="Package Height" />
                                  <input v-model="form_consolidate.package_height" name="package_height" id="package_height" type="number" class="form-control" placeholder="Package Height" required />
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                  <breeze-label for="package_weight" value="Package Width" />
                                  <input v-model="form_consolidate.package_width" name="package_width" id="package_width" type="number" class="form-control" placeholder="Package Width" required />
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-12">
                                <input type="submit" value="Consolidate Package" class="btn btn-success float-left" />
                              </div>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>
                <template v-else>
                  <div class="row" style="margin-top:20px;">
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-header">
                          <h3>Consolidated Package Dimentions</h3>
                        </div>
                        <div class="card-body">
                          <table class="table">
                            <tbody>
                            <tr>
                              <td>
                                Package
                              </td>
                              <td>
                                {{packag.package_no}}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                Weight
                              </td>
                              <td>
                                {{packag.package_weight}}
                              </td>
                            </tr>
                            <tr>
                              <td>
                                Dimensions
                              </td>
                              <td>
                                {{ packag.package_length }} {{ packag.dim_unit }} x {{ packag.package_width }} {{ packag.dim_unit }} x {{ packag.package_height }} {{ packag.dim_unit }}
                              </td>
                            </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>
              </template>
            </template>


            <div class="row" v-show="($page.props.auth.user.type == 'customer') || (($page.props.auth.user.type == 'admin') && (service_requests.length > 0))">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>Services</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <template v-if="$page.props.auth.user.type == 'customer'">
                                    <div  class="col-md-7">
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
                                    <div  class="col-md-5">                                    
                                        <template v-if="form.service_id !=null">                                        
                                            <form @submit.prevent="submit">                                        
                                                <div class="form-group">
                                                    <breeze-label for="notes" value="Message for Admin" />
                                                    <textarea v-model="form.customer_message" 
                                                    name="notes" 
                                                    id="notes" 
                                                    class="form-control" 
                                                    placeholder="Message for Admin" 
                                                    rows="2"
                                                    style="resize:none;"
                                                    required >
                                                    </textarea>
                                                </div>                                                    
                                                <p style="color:red;">Are you sure you want to use service? Add your message for admin and continue</p>
                                                <p style="color:red;">Every service request is charged separately, so if you have already requested any service wait for system response.</p>
                                                <p style="color:re">Service : <strong>{{ form.service.title }}</strong></p>
                                                <p style="color:re">Charges : <strong>${{ form.service.price }}</strong></p>
                                                <div class="order-button">
                                                    <a class="btn btn-danger" v-on:click="cancelServiceForm()"> Cancel</a>
                                                    <input type="submit" value="Make Request" class="btn btn-success float-right" />
                                                </div>
                                            </form>
                                        </template>
                                    </div>
                                </template>
                                <template v-if="service_requests.length > 0">
                                    <div v-bind:class="{ 'col-md-8': $page.props.auth.user.type == 'admin', 'col-md-12': $page.props.auth.user.type == 'customer' }">
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
                                                    <th scope="col">Admin Response</th>
                                                    <th scope="col">Status</th>
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
                                                        {{ request.admin_message }}                                                    
                                                    </td>
                                                    <td>
                                                        <span v-bind:class="getLabelClass(request.status)" style="padding :5px;"> {{ request.status }} </span>                                                   
                                                    </td>
                                                    <td>
                                                        $ {{ request.price }}                                                    
                                                    </td>
                                                    <td>
                                                        <template v-if="$page.props.auth.user.type == 'admin' && request.status == 'pending'">
                                                            <a v-on:click="setServiceResponse(request)" class="link-primary">Respond</a>
                                                        </template>                                            
                                                    </td>
                                            </tr>
                                            </tbody>
                                        </table>                                    
                                    </div>
                                </template>
                                <div v-if="$page.props.auth.user.type == 'admin'"  class="col-md-4">
                                    <template v-if="form_respond.request !=null">                  
                                        <h3> Handle Service Request </h3>                       
                                        <form @submit.prevent="submitRespondForm">                                                    
                                            <p style="color:re">Service : <strong>{{ form_respond.request.service_title }}</strong></p>
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
                                            <p style="color:re">Charges: <strong>${{ form_respond.request.price }}</strong></p>
                                            <div class="form-group">
                                                <p>Edit Charges</p>
                                                <input type="text" v-model="form_respond.request.price" />
                                            </div>
                                            <div class="order-button">
                                                <input style="display:none;" type="submit" value="Update Request" class="btn btn-danger" />

                                                <a v-on:click="requestComplete()" class="btn btn-success float-left">
                                                    <span>Complete Request</span>
                                                </a>

                                                <a v-on:click="requestReject()" class="btn btn-danger float-right" style="margin-right:5px;">
                                                    <span>Reject Request</span>
                                                </a>

                                            </div>
                                            </form>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>
                                Mailout Details 
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th colspan="2">Shipping Address</th>                                             
                                            </tr>
                                        </thead>
                                        <template v-if="packag.address_book_id==0">
                                            <tbody>                                     
                                                <tr>
                                                    <td colspan="2">
                                                        To be Filled
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </template>
                                        <template v-else>
                                            <tbody>
                                                <tr>
                                                    <td colspan="2">
                                                        {{packag.address.fullname}}
                                                    </td>                                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        {{packag.address.address}}
                                                    </td>                                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        {{packag.address.city}},{{packag.address.state}}
                                                    </td>                                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        {{packag.address.country}}
                                                    </td>                                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        Phone : {{packag.address.phone}}
                                                    </td>                                                                    
                                                </tr>
                                            </tbody>   
                                        </template>                                                 
                                    </table>
                                </div>
                                <div class="col-md-4">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th colspan="2">Shipping Service Details</th>                                             
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Carrier</td>
                                                <td>
                                                    <template v-if="packag.carrier_code">
                                                        {{packag.carrier_code}}
                                                    </template>
                                                    <template v-else>Not Set</template>
                                                </td>                                                                    
                                            </tr>
                                            <tr>
                                                <td>Service</td>
                                                <td>
                                                    <template v-if="packag.service_code">
                                                        {{packag.service_label}}
                                                    </template>
                                                    <template v-else>Not Set</template>
                                                </td>                                                                    
                                            </tr>                                        
                                            <tr>
                                                <td>Package Type</td>
                                                <td>
                                                    <template v-if="packag.package_type_code">
                                                    {{ packag.package_type_code.split('_').join(' ') }}

                                                    </template>
                                                    <template v-else>Not Set</template>
                                                </td>                                                                    
                                            </tr>
                                            <tr>
                                                <td>Tracking Number Out</td>
                                                <td>
                                                    <template v-if="packag.tracking_number_out">
                                                    {{packag.tracking_number_out}}
                                                    </template>
                                                    <template v-else>Not Set</template>
                                                </td>                                                                    
                                            </tr>

                                        </tbody>                                               
                                    </table>
                                </div>
                                <div class="col-md-4">
                                    <table class="table">                                                    
                                        <thead>
                                            <tr>
                                                <th colspan="4">Services/Charges</th>
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
                                            <template v-if="service_requests.length > 0">
                                                <tr>
                                                    <td colspan="2" style="text-align:center;">
                                                        <strong>Sub Total</strong>
                                                    </td>
                                                    <td>${{ getServiceSubTotal() }}</td>
                                                    <td></td>
                                                </tr>
                                            </template>
                                            <tr>
                                                <td>Mail Out Fee</td>
                                                <td>${{mailout_fee}}</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Shipping Total</td>
                                                <td>${{ packag.shipping_total }}</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="text-align:center;">
                                                    <strong>Total</strong>
                                                </td>
                                                <td>${{ getGrandTotal() }}</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                              <td colspan="4">
                                                <button type="button" @click="checkout()" class="btn btn-primary">Checkout</button>
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

            <div class="row" v-if="packag.status =='labeled' && $page.props.auth.user.type == 'admin'">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>
                                Shipp out Package
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <form @submit.prevent="submitShipOutForm">  
                                        <div class="row">   
                                            <div class="col-md-6">                                
                                                <div class="form-group">
                                                    <breeze-label for="package_weight" value="Tracking # Out " />
                                                    <input v-model="form_ship.tracking_number_out" name="tracking_number_out" id="tracking_number_out" type="text" class="form-control" placeholder="Tracking # Out" required />
                                                </div>
                                            </div>
                                            <div class="col-md-6" style="margin-top:20px;"> 
                                                <input type="submit" value="Ship Out" class="btn btn-success float-left" />
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>
                                Order/Item Images 
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div v-for="image in images" :key="'image'+image.order_id" class="col-md-2">
                                    <div class="text-center">
                                        <img style="width:150px;height:auto;" class="img-thumbnail" :src="imgURL(image.img_url)" />
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
.card-header > h3{
    font-weight: bold;
}
</style>
<script>
    import MainLayout from '@/Layouts/Main'
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
    import FlashMessages from '@/Components/FlashMessages'
    import BreezeLabel from '@/Components/Label'
    export default {
        components: {
            BreezeAuthenticatedLayout,
            MainLayout,
            FlashMessages,
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
                form_consolidate: this.$inertia.form({
                    package_id:this.packag.id,
                    status : 'consolidated',
                    weight_unit: this.packag.weight_unit,
                    dim_unit: this.packag.dim_unit,
                    package_weight:this.packag.package_weight,
                    package_length:this.packag.package_length,
                    package_width:this.packag.package_width,
                    package_height:this.packag.package_height,
                }),
                form_shipping_service: this.$inertia.form({
                    package_id:this.packag.id,
                    status : 'labeled',
                    service : null,
                }),
                form_ship: this.$inertia.form({
                    package_id:this.packag.id,
                    status : 'shipped',
                    tracking_number_out : '',
                    service : null,
                }),
                tabs : {
                    tab1:true,
                    tab2:false,
                    tab3:false,
                    tab4:false,
                },
                serverError:'',
                showEstimatedPrice:false,
                form_checkout: this.$inertia.form({
                  amount:'',
                })
            }            
        },
        props: {
            auth: Object,
            packag:Object,
            services: Object,
            service_requests: Object,
            images:Object,
            order_charges:Object,
            mailout_fee: Number,
            shipping_services:Object,
            hasConsolidationRequest:Object,
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
              Inertia.reload({ only: ['service_requests'] });
            },
            setActiveService(service){
                this.form.service_id = service.id;                
                this.form.service = service;
            },   
            cancelServiceForm(){
                this.form.service_id = null;    
            },      
            submitRespondForm() {
              this.form_respond.post(this.route('packages.service-handle'));
              this.form_respond.reset();
              Inertia.reload({ only: ['service_requests'] });
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
            submitConsolidateForm(){
              this.form_consolidate.post(this.route('packages.consolidate'));
              Inertia.reload();
            },
            submitShipOutForm(){
              this.form_ship.post(this.route('packages.ship-package'));
            },
            setShippingService(service){
              this.form_shipping_service.service = service;
              this.form_shipping_service.post(this.route('packages.set-shipping-service'));
              location.reload();
            },
            getShippingRates(){
                //this.$refs.buttonRates.innerText = "Loading ..."
                this.showEstimatedPrice=false;
                this.isLoading = true;
                let quote_params = {
                        ship_from : this.packag.warehouse_id,
                        ship_to : this.packag.address.country_id,
                        weight : this.packag.package_weight,
                        unit : this.form.unit,	
                        length : this.packag.package_length,
                        width : this.packag.package_width,
                        height : this.packag.package_height,	
                        zipcode : 'null',				
                };
                //get first service rate. 
                quote_params.service = this.shipping_services.fedex_international_economy;		
                axios.get("/getQuote",{	
                    params : quote_params, 
                })
                .then(({ data }) => {
                        this.isLoading = false;
                        if(data.status){
                            this.showEstimatedPrice=true;				
                            //this.services.push(data.service)   
                            this.shipping_services[data.service.serviceCode] = data.service;

                        }else{
                            this.serverError = data.message;
                        }
                    }
                );
                
                //get first service rate. 
                quote_params.service = this.shipping_services.usps_international_first_class;		
                axios.get("/getQuote",{	
                    params : quote_params, 
                })
                .then(({ data }) => {
                        this.isLoading = false;
                        if(data.status){
                            this.showEstimatedPrice=true;				
                            //this.services.push(data.service)   
                            this.shipping_services[data.service.serviceCode] = data.service;

                        }else{
                            this.serverError = data.message;
                        }
                    }
                );

                //get first service rate. 
                quote_params.service = this.shipping_services.usps_international_priority;		
                axios.get("/getQuote",{	
                    params : quote_params, 
                })
                .then(({ data }) => {
                        this.isLoading = false;
                        if(data.status){
                            this.showEstimatedPrice=true;				
                            //this.services.push(data.service)   
                            this.shipping_services[data.service.serviceCode] = data.service;
                        }else{
                            this.serverError = data.message;
                        }
                    }
                );

                //get first service rate. 
                quote_params.service = this.shipping_services.usps_international_express;		
                axios.get("/getQuote",{	
                    params : quote_params, 
                })
                .then(({ data }) => {
                        this.isLoading = false;
                        if(data.status){
                            this.showEstimatedPrice=true;				
                            //this.services.push(data.service)   
                            this.shipping_services[data.service.serviceCode] = data.service;
                        }else{
                            this.serverError = data.message;
                        }
                    }
                );                
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
            getGrandTotal(){
                return this.getServiceSubTotal()+parseFloat(this.packag.shipping_total)+parseFloat(this.mailout_fee);
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
                case 'filled':
                    return 'label bg-info';
                    break;
                case 'rejected':
                    return 'label bg-danger';
                    break;
                case 'consolidated':
                    return 'label bg-success';
                    break;
                case 'labeled':
                    return 'label bg-success';
                    break;
                case 'open':
                    return 'label bg-info';
                    break;
                case 'shipped':
                    return 'label bg-primary';
                    break;
                }
            },
          checkout(){
              this.form_checkout.amount = this.getGrandTotal();
              this.form_checkout.post(this.route('payment.index'))
          }
        }        
    }
</script>
