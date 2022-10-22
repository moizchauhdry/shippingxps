<template>
  <MainLayout>
    <div class="row" style="margin-top:20px;">
      <div class="col-md-12">
        <div class="row">
        </div>
        <div class="row">
          <div class="col-md-10">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Package # {{ packag.package_no }} <a v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'" class="btn btn-outline-danger ml-2" v-show="packag.status === 'open'|| packag.status === 'filled' || packag.status === 'labeled' " v-on:click="confirmDeletion()">Delete Package</a>
            </h2>
          </div>
          <div class="col-md-2">
            <span v-bind:class="getLabelClass(packag.status)" style="color:black;padding:5px;">{{ packag.status }}</span>

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
                    <th>Tracking IN</th>
                    <th>From</th>
                    <th>Receive Date</th>
                    <th scope="col">Image</th>
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>
                  <template v-for="(order,index) in packag.orders" :key="order.id">
                    <tr>
                      <td>{{ index + 1 }}</td>
                      <td>{{ order.tracking_number_in }}</td>
                      <td>{{ order.received_from }}</td>
                      <td>{{ order.created_at }}</td>
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
                          {{ packag.package_no }}
                        </td>
                      </tr>
                      <tr>
                        <td>
                          Weight
                        </td>
                        <td>
                          {{ packag.package_weight }} {{ packag.weight_unit}}
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
                    <template v-if="((packag.status == 'filled' && packag.orders.length == 1) || (packag.status == 'consolidated' && hasConsolidationServed && !hasMultiPieceServed))&&((packag.package_length !=='' && packag.package_length !== 0) && (packag.package_width !=='' && packag.package_width !== 0) && (packag.package_height !=='' && packag.package_height !== 0)) ">
                      <a class="btn btn-success" v-on:click="getShippingRates()">Get Shipping Rates</a>
                    </template>
                    <template v-else-if="((packag.status == 'filled' && packag.orders.length == 1) || (hasMultiPieceServed && !hasConsolidationServed ) && (packag.status != 'labeled' && packag.status != 'shipped')) ">
                      <a class="btn btn-success" v-on:click="getShippingRatesByOrders()">Get Shipping Rates</a>
                    </template>
                    <template v-else-if="packag.orders.length > 1 && packag.status == 'filled'" >
                      <strong>Please get your package consolidated/Multi piece sets before getting shipping rates</strong>
                    </template>
                    <template v-else-if="packag.orders.length == 1 && packag.status == 'filled'">
                        <strong>Your package needs an update before getting shipping rates</strong>
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
                        <th>Price</th>
                        <th></th>
                      </tr>
                      </thead>
                      <tbody>
                      <template v-for="service in shipping_services" :key="service.service_id">
                        <tr v-if="service.isReady === true">
                          <td>{{ service.serviceLabel }}</td>
                          <td>{{ service.totalAmount }} {{ service.currency }}</td>
                          <td><a v-on:click="setShippingService(service)" class="btn btn-info">Confirm</a></td>
                        </tr>
                      </template>
                      <tr colspan="3">
                        <p style="color:red">Selected service cannot be changed. So make sure you choose correct service.</p>
                        <p  style="color:red" v-show="displayNoteShipping"><b>Note:</b> Make sure your address is valid to get Shipping Service</p>
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
          <template v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
            <template v-if="(packag.status == 'open' || ( packag.status == 'filled' && packag.orders.length > 1) || packag.status != 'consolidated') && packag.status != 'labeled' && packag.status != 'shipped'">
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
                              <breeze-label for="warehouse_id" value="Weight Unit"/>
                              <select name="weight_unit" class="form-select" v-model="form_consolidate.weight_unit" required @change="changeDimention($event)">
                                <option value="lb">Lb</option>
                                <option value="kg">Kg</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <breeze-label for="package_weight" value="Package Weight"/>
                              <input v-model="form_consolidate.package_weight" min="1" name="package_weight" id="package_weight" type="number" class="form-control" placeholder="Package Weight" :step="0.01" required/>
                            </div>
                          </div>

                          <div class="col-md-2">
                            <div class="form-group">
                              <breeze-label for="dim_unit" value="Dimention Unit"/>
                              <select name="dim_unit" class="form-select" v-model="form_consolidate.dim_unit"   readonly :disabled="1">
                                <option value="in">Inch</option>
                                <option value="cm">Cm</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <breeze-label for="package_length" value="Package Length"/>
                              <input v-model="form_consolidate.package_length" min="1" name="package_length" id="package_length" type="number" class="form-control" placeholder="Package Length" required/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <breeze-label for="package_height" value="Package Height"/>
                              <input v-model="form_consolidate.package_height" min="1" name="package_height" id="package_height" type="number" class="form-control" placeholder="Package Height" required/>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <breeze-label for="package_width" value="Package Width"/>
                              <input v-model="form_consolidate.package_width" min="1" name="package_width" id="package_width" type="number" class="form-control" placeholder="Package Width" required/>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <input type="submit" value="Consolidate Package" class="btn btn-success float-left"/>
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
                            {{ packag.package_no }}
                          </td>
                        </tr>
                        <tr>
                          <td>
                            Weight
                          </td>
                          <td>
                            {{ packag.package_weight }} {{ packag.weight_unit}}
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

        <template v-if="packag.status != 'shipped'">
          <div class="row" v-show="($page.props.auth.user.type == 'customer') || (($page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager') && (service_requests.length > 0))">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header">
                  <h3>Services</h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <template v-if="$page.props.auth.user.type == 'customer' && packag.status != 'consolidated'">
                      <div class="col-md-7">
                        <table class="table table-striped">
                          <thead>
                          <tr>
                            <th scope="col">Service</th>
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
                              <a v-if="service.title == 'Consolidation' && !hasConsolidationRequest && !hasMultiPieceServed && packag.status != 'open'"  v-on:click="setActiveService(service)" class="link-primary"> Make Request</a>
                              <a v-if="service.title == 'Multi Piece' && !hasConsolidationRequest && !hasMultiPieceServed && packag.status != 'open'"  v-on:click="setActiveService(service)" class="link-primary"> Make Request</a>
                              <span v-if="service.title == 'Consolidation' && !hasConsolidationRequest && packag.status == 'open'"><b>Note: Declare your customs to consolidate package</b></span>
                              <span v-if="service.title == 'Multi Piece' && !hasConsolidationServed && !hasMultiPieceServed && packag.status == 'open'"><b>Note: Declare your customs to use this service</b></span>
                              <a v-else-if="service.title != 'Consolidation' && service.title != 'Multi Piece'" v-on:click="setActiveService(service)" class="link-primary"> Make Request</a>
                            </td>
                          </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="col-md-5">
                        <template v-if="form.service_id !=null">
                          <form @submit.prevent="submit">
                            <div class="form-group">
                              <breeze-label for="notes" value="Message for Admin"/>
                              <textarea v-model="form.customer_message"
                                        name="notes"
                                        id="notes"
                                        class="form-control"
                                        placeholder="Message for Admin"
                                        rows="2"
                                        style="resize:none;"
                                        required>
                                                    </textarea>
                            </div>
                            <p style="color:red;">Are you sure you want to use service? Add your message for admin and continue</p>
                            <p style="color:red;">Every service request is charged separately, so if you have already requested any service wait for system response.</p>
                            <p>Service : <strong>{{ form.service.title }}</strong></p>
                            <p>Charges : <strong>${{ form.service.price }}</strong></p>
                            <div class="order-button">
                              <a class="btn btn-danger" v-on:click="cancelServiceForm()"> Cancel</a>
                              <input type="submit" value="Make Request" class="btn btn-success float-right"/>
                            </div>
                          </form>
                        </template>
                      </div>
                    </template>
                    <template v-if="service_requests.length > 0">
                      <div v-bind:class="{ 'col-md-8': $page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager', 'col-md-12': $page.props.auth.user.type == 'customer' }">
                        <table class="table table-striped">
                          <thead>
                          <tr>
                            <th scope="col">Service</th>
                            <template v-if="$page.props.auth.user.type == 'customer'">
                              <th scope="col">
                                Your Message
                              </th>
                            </template>
                            <template v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
                              <th scope="col">
                                Customer Message
                              </th>
                            </template>
                            <th scope="col">Admin Response</th>
                            <th scope="col">Status</th>
                            <th scope="col">Charges</th>
                            <template v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'">
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
                              <template v-if="($page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager' )&& request.status == 'pending'">
                                <a v-on:click="setServiceResponse(request)" class="link-primary">Respond</a>
                              </template>
                            </td>
                          </tr>
                          </tbody>
                        </table>
                      </div>
                    </template>
                    <div v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'" class="col-md-4">
                      <template v-if="form_respond.request !=null">
                        <h3> Handle Service Request </h3>
                        <form @submit.prevent="submitRespondForm">
                          <p style="">Service : <strong>{{ form_respond.request.service_title }}</strong></p>
                          <div class="form-group">
                            <p>Message for Customer</p>
                            <textarea v-model="form_respond.admin_message"
                                      name="admin_message"
                                      id="admin_message"
                                      class="form-control"
                                      placeholder="Message for Customer"
                                      rows="4"
                                      style="resize:none;"
                                      required>
                                                </textarea>
                          </div>
                          <p style="">Charges: <strong>${{ form_respond.request.price }}</strong></p>
                          <div class="form-group">
                            <p>Edit Charges</p>
                            <input type="text" v-model="form_respond.request.price"/>
                          </div>
                          <div class="order-button">
                            <input style="display:none;" type="submit" value="Update Request" class="btn btn-danger"/>

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
        </template>

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
                            {{ packag.address.fullname === null ? "- -" : packag.address.fullname }}
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2">
                            {{ packag.address.address === null ? "- -" : packag.address.address }}
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2">
                            {{ packag.address.city }},{{ packag.address.state }}
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2">
                            {{ packag.address.country_name }}
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2">
                            Phone : {{ packag.address.phone }}
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2">
                            Zip Code : {{ packag.address.zip_code }}
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
                            {{ packag.carrier_code }}
                          </template>
                          <template v-else>Not Set</template>
                        </td>
                      </tr>
                      <tr>
                        <td>Service</td>
                        <td>
                          <template v-if="packag.service_code">
                            {{ packag.service_label }}
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
                            {{ packag.tracking_number_out }} <a href="javascript:void(0)" class="badge badge-dark " v-show="packag.payment_status == 'Paid' && !tracking_edit && ($page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager')" @click="tracking_edit = !tracking_edit">Edit</a>
                          </template>
                          <template v-else>Not Set <a href="javascript:void(0)" class="badge badge-dark " v-show="packag.payment_status == 'Paid' && !tracking_edit && ($page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager')" @click="tracking_edit = !tracking_edit">Edit</a></template>
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
                      <!--                                            <template  v-for="order in order_charges" :key="order.id">
                                                                      <tr>
                                                                          <td>Order Pickup charges </td>
                                                                          <td>${{ order.service_charges }}</td>
                                                                          <td></td>
                                                                          <td></td>
                                                                      </tr>
                                                                  </template>-->
                      <template v-for="service in service_requests " :key="service.id">
                        <tr>
                          <td>{{ service.service_title }}</td>
                          <td v-if="service.service_title == 'Consolidation'">{{ '$1.5 X ' + packag.orders.length + ' + $' + service.price + ' = ' }}</td>
                          <td v-if="service.service_title != 'Consolidation'">${{ service.price }} =</td>
                          <td v-if="service.service_title != 'Consolidation'">${{ service.price }}</td>
                          <td v-if="service.service_title == 'Consolidation'">${{ parseFloat(service.price) + (1.5 * packag.orders.length) }}</td>
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
                        <td></td>
                        <td>${{ mailout_fee }}</td>
                        <td></td>
                      </tr>
                      <tr v-show="storage_fee > 0">
                        <td>Storage Fee</td>
                        <td></td>
                        <td>${{ storage_fee }}</td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>Shipping Total</td>
                        <td></td>
                        <td>${{ packag.shipping_total }}</td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="2" style="text-align:center;">
                          <strong>Total</strong>
                        </td>
                        <td>${{ getGrandTotal() }}</td>
                        <td></td>
                      </tr>
                      <tr v-if="$page.props.auth.user.type == 'customer'">
                        <td v-if="packag.carrier_code != null && packag.payment_status != 'Paid'" colspan="4">
                          <button type="button"  @click="checkout()" class="btn btn-primary">Checkout</button>
                        </td>
                        <td v-else colspan="4">
                          <button v-if="packag.payment_status != 'Paid'" type="button"  class="btn btn-primary disabled">Checkout</button>
                          <p ><strong>Note:</strong> For checking out the package should be consolidated if package contains
                          more than one order. And shipping service should be also selected.</p>

                          <span class="badge badge-success" v-if="packag.payment_status == 'Paid'">Paid</span>
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

        <div class="row" v-show="tracking_edit" v-if="(packag.payment_status == 'Paid') && ($page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager')">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3>
                  Ship out Package
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <form @submit.prevent="submitShipOutForm">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <breeze-label for="package_weight" value="Tracking # Out "/>
                            <input v-model="form_ship.tracking_number_out" name="tracking_number_out" id="tracking_number_out" type="text" class="form-control" placeholder="Tracking # Out" required/>
                          </div>
                        </div>
                        <div class="col-md-6" style="margin-top:20px;">
                          <input type="submit" value="Ship Out" class="btn btn-success float-left"/>
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
                      <!-- <img style="width:150px;height:auto;" class="img-thumbnail" :src="imgURL(image.img_url)"/> -->
                        <img style="width:100px;height:auto;" class="img-thumbnail" @click="viewImage($event)" :src="imgURL(image.img_url)"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-show="overlay === true" class="overlay">
          <div class="overlay__inner">
            <div class="overlay__content"><span class="spinner"></span></div>
          </div>
        </div>
      </div>

      <!-- Modal For Delete Package -->
      <div v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'" class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-backdrop="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Delete Package</h5>
              <button type="button" @click="closeDeletionModal()" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="text-center mt-2 mb-5">
                Are You sure you want to delete this "Package # {{ packag.package_no }}"?
                <br>
                You can't undo this action.
              </div>
              <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Warning!</h4>
                <p>By deleting this package it will delete all the connected orders.</p>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-dark" @click="closeDeletionModal()" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-danger" @click="deletePackage()">Delete Anyway</button>
            </div>
          </div>
        </div>
      </div>
    </div>


  </MainLayout>
    <ImageViewer></ImageViewer>

</template>

<style scoped>
.overlay {
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  position: fixed;
  background: rgba(0,0,0,0.5);
  z-index:9999999999;
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
.card {
  margin-top: 25px;
}

.card-body {
  padding: 0.8rem;
}

.card-body p strong {
  color: #212529;
  margin-right: 12px;
}

.address-card {
  height: 130px;
}

.card-header > h3 {
  font-weight: bold;
}
</style>
<script>
import MainLayout from '@/Layouts/Main'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import BreezeLabel from '@/Components/Label'
import ImageViewer from '@/Components/ImageViewer'
import $ from 'jquery'

export default {
  components: {
    BreezeAuthenticatedLayout,
    MainLayout,
    BreezeLabel,
    ImageViewer
  },
  data() {
    return {
      form: this.$inertia.form({
        package_id: this.packag.id,
        service_id: null,
        customer_message: '',
        service: null
      }),
      form_respond: this.$inertia.form({
        admin_message: '',
        status: 'pending',
        request: null
      }),
      form_consolidate: this.$inertia.form({
        package_id: this.packag.id,
        status: 'consolidated',
        weight_unit: this.packag.weight_unit,
        dim_unit: this.packag.dim_unit,
        package_weight: this.packag.package_weight,
        package_length: this.packag.package_length,
        package_width: this.packag.package_width,
        package_height: this.packag.package_height,
      }),
      form_shipping_service: this.$inertia.form({
        package_id: this.packag.id,
        status: 'labeled',
        service: null,
      }),
      form_ship: this.$inertia.form({
        package_id: this.packag.id,
        status: 'shipped',
        tracking_number_out: '',
        service: null,
      }),
      tabs: {
        tab1: true,
        tab2: false,
        tab3: false,
        tab4: false,
      },
      serverError: '',
      showEstimatedPrice: false,
      form_checkout: this.$inertia.form({
        amount: '',
        package_id: this.packag.id,
      }),
      storage_fee : 0,
      overlay: false,
      displayNoteShipping: true,
      tracking_edit: false,
    }
  },
  props: {
    auth: Object,
    packag: Object,
    services: Object,
    service_requests: Object,
    images: Object,
    order_charges: Object,
    mailout_fee: Number,
    shipping_services: Object,
    hasConsolidationRequest: Object,
    hasConsolidationServed: Object,
    hasMultiPieceServed: Object,
  },
  computed: {
    siuteNum() {
      return 4000 + this.$page.props.auth.user.id;
    },
  },
  mounted() {
    console.log(this.packag);
    this.getStorageFee()
  },
  methods: {
    submit() {
      this.form.post(this.route('packages.service-request'));
      this.form.reset();
      Inertia.reload({only: ['service_requests']});
    },
    setActiveService(service) {
      this.form.service_id = service.id;
      this.form.service = service;
    },
    cancelServiceForm() {
      this.form.service_id = null;
    },
    submitRespondForm() {
      this.form_respond.post(this.route('packages.service-handle'));
      this.form_respond.reset();
      Inertia.reload({only: ['service_requests']});
    },
    setServiceResponse(request) {
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
    submitConsolidateForm() {
      this.form_consolidate.post(this.route('packages.consolidate'));
      Inertia.reload();
    },
    submitShipOutForm() {
      this.form_ship.post(this.route('packages.ship-package'));
      this.tracking_edit = false;
    },
    setShippingService(service) {
      var result = window.confirm('After Confirming the shippment method you wont be able to change. Are you sure to confirm ?')
      if(result){
        this.form_shipping_service.service = service;
        this.form_shipping_service.post(this.route('packages.set-shipping-service'));
      }
      /*window.location.reload()*/
    },
    getShippingRates() {
      //this.$refs.buttonRates.innerText = "Loading ..."

      console.log('here');

      this.showEstimatedPrice = false;
      this.isLoading = true;
      let quote_params = {
        ship_from: this.packag.warehouse_id,
        ship_to: this.packag.address.country_id,
        weight: this.packag.package_weight,
        unit: this.packag.weight_unit,
        weight_unit: this.packag.weight_unit +"_"+ this.packag.dim_unit,
        length: this.packag.package_length,
        width: this.packag.package_width,
        height: this.packag.package_height,
        zipcode: this.packag.address.zip_code,
        city : this.packag.address.city,
        is_residential : this.packag.address.is_residential,
      };
      axios.get(this.route('getServicesList')).then(response => {
        console.log(response.data.services)
        response.data.services.forEach((ele, index) => {
          console.log(ele);
          quote_params.service = ele;
          this.getRates(quote_params);
        })
      }).catch(error => {
        console.log(error)
      })
      /*//get first service rate.
      quote_params.service = this.shipping_services.fedex_international_economy;
      axios.get("/getQuote", {
        params: quote_params,
      })
          .then(({data}) => {
                this.isLoading = false;
                if (data.status) {
                  this.showEstimatedPrice = true;
                  //this.services.push(data.service)
                  this.shipping_services[data.service.serviceCode] = data.service;

                } else {
                  this.serverError = data.message;
                }
              }
          );

      //get first service rate.
      quote_params.service = this.shipping_services.usps_international_first_class;
      axios.get("/getQuote", {
        params: quote_params,
      })
          .then(({data}) => {
                this.isLoading = false;
                if (data.status) {
                  this.showEstimatedPrice = true;
                  //this.services.push(data.service)
                  this.shipping_services[data.service.serviceCode] = data.service;

                } else {
                  this.serverError = data.message;
                }
              }
          );

      //get first service rate.
      quote_params.service = this.shipping_services.usps_international_priority;
      axios.get("/getQuote", {
        params: quote_params,
      })
          .then(({data}) => {
                this.isLoading = false;
                if (data.status) {
                  this.showEstimatedPrice = true;
                  //this.services.push(data.service)
                  this.shipping_services[data.service.serviceCode] = data.service;
                } else {
                  this.serverError = data.message;
                }
              }
          );

      //get first service rate.
      quote_params.service = this.shipping_services.usps_international_express;
      axios.get("/getQuote", {
        params: quote_params,
      })
          .then(({data}) => {
                this.isLoading = false;
                if (data.status) {
                  this.showEstimatedPrice = true;
                  //this.services.push(data.service)
                  this.shipping_services[data.service.serviceCode] = data.service;
                } else {
                  this.serverError = data.message;
                }
              }
          );*/
    },
    getShippingRatesByOrders() {
      //this.$refs.buttonRates.innerText = "Loading ..."

      console.log('here');

      this.showEstimatedPrice = false;
      this.isLoading = true;
      let pieces = [];

      console.log(this.packag.orders.length);
      this.packag.orders.forEach(function (value,index) {
        let piece = {
          "weight": value.package_weight.toString(),
          "length": value.package_length.toString(),
          "width": value.package_width.toString(),
          "height": value.package_height.toString(),
          "insuranceAmount": "0",
          "declaredValue": value.declared_value == 0 ? "1" : value.declared_value.toString()
        };

        pieces.push(piece)
      })


      let quote_params = {
        ship_from: this.packag.warehouse_id,
        ship_to: this.packag.address.country_id,
        weight: this.packag.package_weight,
        unit: this.packag.weight_unit,
        weight_unit: this.packag.weight_unit +"_"+ this.packag.dim_unit,
        pieces:pieces,
        zipcode: this.packag.address.zip_code,
        city : this.packag.address.city,
        is_residential : this.packag.address.is_residential,
      };
      axios.get(this.route('getServicesList')).then(response => {
        console.log(response.data.services)
        response.data.services.forEach((ele, index) => {
          console.log(ele);
          quote_params.service = ele;
          this.getRatesByOrders(quote_params);
        })
      }).catch(error => {
        console.log(error)
      })

    },
    getServiceSubTotal() {
      let request_total = this.service_requests.reduce((acc, item) => {
            return acc + (parseInt(item.price));
          },
          0);

      var consolidation_total = 0;
      console.log(this.hasConsolidationServed);
      if (this.hasConsolidationServed || this.hasConsolidationRequest) {
        consolidation_total = this.packag.orders.length * 1.5;
      } else {
        consolidation_total = 0;
      }

      /*let pickup_total =  this.order_charges.reduce((acc, item) => {
          return acc + (parseInt(item.service_charges));
          },
      0);*/
      // return request_total+pickup_total;
      return request_total + consolidation_total;

    },
    getGrandTotal() {
      var grandTotal = this.getServiceSubTotal() + parseFloat(this.packag.shipping_total) + parseFloat(this.mailout_fee) + parseFloat(this.storage_fee);
      return parseFloat(grandTotal).toFixed(2);
    },
    setActiveTabAB(tab) {

      for (var key in this.tabs) {
        if (key === tab) {
          this.tabs[key] = true;
        } else {
          this.tabs[key] = false;
        }
      }
    },
    getTabClass(tab) {

      if (this.tabs[tab] === true) {
        return 'nav-link active';
      } else {
        return 'nav-link';
      }

    },
    getTabPaneClass(tab) {

      if (this.tabs[tab] === true) {
        return 'tab-pane show active';
      } else {
        return 'tab-pane fade';
      }
    },
    imgURL(url) {
      return "/public/uploads/" + url;
    },
    viewImage(event) {
      console.log(event.target.src);
      var modal = document.getElementById('imageViewer');
      var imageSRC = document.querySelector('#imageViewer img')
      imageSRC.src = event.target.src;
      modal.classList.add('show');
      $('#imageViewer').show();
    },
    makePackageUrl(order_id) {
      return route('package.create') + '?order_id=' + order_id;
    },
    getLabelClass(status) {
      switch (status) {
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
    checkout() {
      this.form_checkout.amount = this.getGrandTotal();
      this.form_checkout.post(this.route('payment.index'))
    },
    getRates(quote_params) {
      axios.get("/getQuote", {
        params: quote_params,
      }).then((response) => {
            console.log(response.data.service)
            this.isLoading = false;
            if (response.data.status) {
              this.showEstimatedPrice = true;
              this.shipping_services[response.data.service.service_id] = response.data.service;

            } else {
              this.serverError = response.data.message;
            }
            if(response.data.service.isReady !== undefined && response.data.service.isReady  === true){
              this.displayNoteShipping = false;
            }
          }
      );
    },
    getRatesByOrders(quote_params) {
      axios.get("/getQuoteByOrders", {
        params: quote_params,
      }).then((response) => {
            console.log(response.data.service)
            this.isLoading = false;
            if (response.data.status) {
              this.showEstimatedPrice = true;
              this.shipping_services[response.data.service.service_id] = response.data.service;

            } else {
              this.serverError = response.data.message;
            }
            if(response.data.service.isReady !== undefined && response.data.service.isReady  === true){
              this.displayNoteShipping = false;
            }
          }
      );
    },
    getStorageFee(){
      axios.get(this.route('getStorageFee'),{
        params: {
          package_id:this.packag.id
        },
      }).then((response) => {this.storage_fee = response.data});
    },
    changeDimention(event){
      console.log(event.target.value);

      this.form_consolidate.dim_unit =  event.target.value == 'kg' ? 'cm' : 'in'
    },
    confirmDeletion(){
      let packageID = this.packag.id;
      var modal = document.getElementById('deleteModal');
      modal.classList.add('show');
      $('#deleteModal').show();
    },
    closeDeletionModal(){
      var modal = document.getElementById('deleteModal');
      modal.style.display = 'none';
    },
    deletePackage(){
      this.overlay = true;
      axios.post(this.route('packages.destroy'),{id:this.packag.id})
          .then(({ data }) => {
                if(data.status == 1){
                  alert(data.message)
                  location.href = data.url;
                }else{
                  alert(data.message)
                  this.overlay = false;
                  var modal = document.getElementById('deleteModal');
                  modal.style.display = 'none';
                }
              }
          );
    }
    /*imgURL(url) {
      return "/public" + url;
    },*/
  }
}
</script>
