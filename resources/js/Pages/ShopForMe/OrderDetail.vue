<template>
  <MainLayout>
    <div class="card mt-4">
      <div class="card-body">
        <template v-if="$page.props.auth.user.type == 'admin'">
          <div class="row" style="margin-bottom:10px;">
            <div class="col-md-12">
              <a :href="route('shop-for-me.edit', order.id)" class="btn btn-primary float-right">Edit Order</a>
            </div>
          </div>
        </template>
        <div class="row">
          <div class="col-md-12">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Order Details
            </h2>
            <table class="table table-striped">
              <tbody>
              <tr>
                <td>Customer</td>
                <td>{{ order.customer.name }}</td>
              </tr>
              <tr>
                <td>Warehouse</td>
                <td>{{ order.warehouse.name }}</td>
              </tr>

              <tr v-if="order.order_type == 'pickup'">
                <td>Mall name</td>
                <td>{{ order.store.name }}</td>
              </tr>

              <tr v-if="order.order_type == 'pickup'">
                <td>Store name</td>
                <td>{{ order.store_name }}</td>
              </tr>

              <tr v-if="order.order_type == 'pickup'">
                <td>Pickup Date</td>
                <td>{{ order.pickup_date }}</td>
              </tr>

              <tr v-if="order.order_type == 'shopping'">
                <td>Site Name</td>
                <td>{{ order.site_name }}</td>
              </tr>
              <tr v-if="order.order_type == 'shopping'">
                <td>Site URL</td>
                <td>
                  <a :href="order.site_url">{{ order.site_url }}</a>
                </td>
              </tr>
              <!-- <tr>
                <td>Shipping From Shop </td>
                <td>
                  {{ order.shipping_from_shop }}
                </td>
              </tr>
              <tr>
                <td>Sales Tax </td>
                <td>
                  {{ order.sales_tax }}
                </td>
              </tr> -->
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6" v-if="order.status != 'pending'">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Package
            </h2>
            <table class="table table-striped">
              <tbody>
              <tr>
                <td>Weight</td>
                <td>{{ order.package_weight }}</td>
              </tr>
              <tr>
                <td>Length</td>
                <td>{{ order.package_length }}</td>
              </tr>
              <tr>
                <td>Width</td>
                <td>{{ order.package_width }}</td>
              </tr>
              <tr>
                <td>Height</td>
                <td>{{ order.package_height }}</td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="col-md-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Notes
            </h2>
            <table class="table table-striped">
              <tbody>
              <tr>
                <td>Notes</td>
                <td>{{ order.notes }}</td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="col-md-6" v-if="order.order_type == 'pickup'">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Receipt/Image
            </h2>
            <div class="row">
              <div v-for="image in order.images" :key="image.id" class="col-md-3">
                <div class="text-center">
                  <img style="width:100px;height:auto;" class="img-thumbnail" @click="viewImage($event)" :src="imgURL(image.image)"/>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row" style="display:none;">
          <div class="col-md-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Sender Details
            </h2>
            <table class="table table-striped">
              <tbody>
              <tr>
                <td>Name</td>
                <td>{{ order.sender_fullname }}</td>
              </tr>
              <tr>
                <td>Address</td>
                <td>{{ order.sender_address }}</td>
              </tr>
              <tr>
                <td>Email</td>
                <td>{{ order.sender_email }}</td>
              </tr>
              <tr>
                <td>Phone</td>
                <td>{{ order.sender_phone }}</td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="col-md-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Receiver Details
            </h2>
            <table class="table table-striped">
              <tbody>
              <tr>
                <td>Name</td>
                <td>{{ order.receiver_fullname }}</td>
              </tr>
              <tr>
                <td>Address</td>
                <td>{{ order.receiver_address }}</td>
              </tr>
              <tr>
                <td>Email</td>
                <td>{{ order.receiver_email }}</td>
              </tr>
              <tr>
                <td>Phone</td>
                <td>{{ order.receiver_phone }}</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="row">

          <div class="col-md-12">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Items
            </h2>
            <table class="table table-striped">
              <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Description</th>
                <th scope="col" v-if="order.pickup_type != 'pickup_only'">Price</th>
                <th scope="col" v-if="order.pickup_type != 'pickup_only'">Price with Tax</th>
                <th scope="col">Quantity</th>
                <th scope="col" v-if="order.pickup_type != 'pickup_only'">Total</th>
                <th scope="col" v-if="order.order_type == 'shopping'">URL</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="item in order.items" :key="item.id">
                <td>{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>{{ item.description }}</td>
                <td v-if="order.pickup_type != 'pickup_only'">{{ item.unit_price }}</td>
                <td v-if="order.pickup_type != 'pickup_only'">{{ item.price_with_tax }}</td>
                <td>{{ item.quantity }}</td>
                <td v-if="order.pickup_type != 'pickup_only'">{{ item.sub_total }}</td>
                <td v-if="order.order_type == 'shopping'">
                  <a :href="item.url">{{ item.url }}</a>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
          <hr>
          <div class="col-md-4 offset-md-8">
            <table class="table table-responsive table-borderless">
              <tbody>
              <tr v-if="order.order_type == 'shopping' || order.pickup_type == 'shipping_xps_purchase'">
                <th style="text-align: end">Sub Total</th>
                <td>{{ order.sub_total }}</td>
              </tr>
              <tr v-if="order.order_type == 'pickup'">
                <th style="text-align: end">Pickup Charges</th>
                <td>{{ order.pickup_charges }}</td>
              </tr>
              <tr v-if="order.order_type == 'shopping' || order.pickup_type == 'shipping_xps_purchase'">
                <th style="text-align: end">
                  Service Charges <br>
                  <small>5% of subtotal</small>
                </th>
                <td>{{ order.service_charges }}</td>
              </tr>
              <tr v-if="order.order_type == 'pickup' && order.shipping_charges != null">
                <th style="text-align: end">
                  Shipping Charges
                </th>
                <td>{{ order.shipping_charges }}</td>
              </tr>
              <tr v-if="order.order_type == 'pickup' && additional_pickup_charges != null">
                <th style="text-align: end">Box Price</th>
                <td>{{ additional_pickup_charges }}</td>
              </tr>
              <tr>
                <th style="text-align: end">Grand Total</th>
                <td>{{ order.grand_total }}</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
  <ImageViewer>

  </ImageViewer>
</template>

<script>
import MainLayout from '@/Layouts/Main'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import ImageViewer from '@/Components/ImageViewer'
import $ from 'jquery'
export default {
  components: {
    BreezeAuthenticatedLayout,
    MainLayout,
    ImageViewer,
  },

  props: {
    auth: Object,
    order: Object,
    order_details: Object,
    additional_pickup_charges:Number,
  },
  methods: {
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

  }
}
</script>
