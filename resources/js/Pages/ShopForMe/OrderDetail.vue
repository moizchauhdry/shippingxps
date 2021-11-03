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
                  <td>Site Name </td>
                  <td>{{ order.site_name }}</td>
                </tr>
                <tr v-if="order.order_type == 'shopping'">
                  <td>Site URL </td>
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
          <div class="col-md-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Receipt/Image
            </h2>           
            <div class="row">                                
                  <div v-for="image in order.images" :key="image.id" class="col-md-3">
                      <div class="text-center">
                          <img style="width:100px;height:auto;" class="img-thumbnail" :src="imgURL(image.image)" />
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
                  <td>{{ order.sender_fullname}}</td>
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
                  <td>{{ order.receiver_fullname}}</td>
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
                  <th scope="col">Name </th>
                  <th scope="col">Quantity</th>
                  <th scope="col">Price</th>
                  <th scope="col">Price with tax</th>
                  <th scope="col">Description</th>
                  <th scope="col">URL</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in order.items" :key="item.id">
                  <td>{{ item.id}}</td>
                  <td>{{ item.name}}</td>
                  <td>{{ item.quantity}}</td>
                  <td>{{ item.unit_price }}</td>
                  <td>{{ item.price_with_tax }}</td>
                  <td>{{ item.description }}</td>
                  <td>
                    <a :href="item.url">{{ item.url }}</a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/Main'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'

export default {
  components: {
    BreezeAuthenticatedLayout,
    MainLayout
  },

  props: {
    auth: Object,
    order:Object,
    order_details:Object
  },
  methods : {
      imgURL(url) {
          return "/uploads/"+url;
      },
  }
}
</script>
