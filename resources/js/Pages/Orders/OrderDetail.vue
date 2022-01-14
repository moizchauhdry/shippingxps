<template>
  <MainLayout>
    <div class="card mt-4">
      <div class="card-body">
        <template v-if="$page.props.auth.user.type == 'admin'">
          <div class="row" style="margin-bottom:10px;">
            <div class="col-md-12">
              <a :href="route('order.edit', order.id)" class="btn btn-primary">Edit Order</a>
            </div>
          </div>
        </template>
        <div class="row">
          <div class="col-md-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Order Details
            </h2>
            <table class="table table-striped">
              <tbody>
              <tr>
                <td>Traking No IN</td>
                <td>{{ order.tracking_number_in }}</td>
              </tr>
              <tr>
                <td>Customer</td>
                <td>{{ order.customer.name }}</td>
              </tr>
              <tr>
                <td>Received At</td>
                <td>{{ order.warehouse.name }}</td>
              </tr>
              <tr>
                <td>Status</td>
                <td>{{ order.status }}</td>
              </tr>
              </tbody>
            </table>
          </div>

          <div class="col-md-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Package
            </h2>
            <table class="table table-striped">
              <tbody>
              <tr>
                <td>Weight</td>
                <td>{{ order.package_weight }} {{ order.weight_unit }}</td>
              </tr>
              <tr>
                <td>Length</td>
                <td>{{ order.package_length }} {{ order.dim_unit }}</td>
              </tr>
              <tr>
                <td>Width</td>
                <td>{{ order.package_width }} {{ order.dim_unit }}</td>
              </tr>
              <tr>
                <td>Height</td>
                <td>{{ order.package_height }} {{ order.dim_unit }}</td>
              </tr>
              </tbody>
            </table>
          </div>

          <div class="col-md-4">
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
                <!-- <th scope="col">Price</th>
                <th scope="col">Price with Tax</th> -->
                <th scope="col">Quantity</th>
                <!-- <th scope="col">Total</th> -->
                <!-- <th scope="col">Image</th> -->
              </tr>
              </thead>
              <tbody>
              <tr v-for="item in order.items" :key="item.id">
                <td>{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>{{ item.description }}</td>
                <td>{{ item.quantity }}</td>
                <!--<td>{{ item.unit_price}}</td>
                <td>{{ item.price_with_tax}}</td>
                <td>{{ item.sub_total}}</td> -->
                <!-- <td>
                    <div class="text-center">
                        <img style="width:100px;height:auto;" class="img-thumbnail" :src="imgURL(item.image)" />
                    </div>
                </td> -->
              </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="row">

          <div class="col-md-12">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Images
            </h2>
          </div>
          <div v-for="image in order.images" :key="image.id" class="col-md-3">
            <div class="text-center">
              <img style="width:100px;height:auto;" class="img-thumbnail" @click="viewImage($event)" :src="imgURL(image.image)"/>
            </div>
          </div>
        </div>

      </div>
    </div>
  </MainLayout>
  <ImageViewer></ImageViewer>
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
    ImageViewer
  },

  props: {
    auth: Object,
    order: Object,
    order_details: Object
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
