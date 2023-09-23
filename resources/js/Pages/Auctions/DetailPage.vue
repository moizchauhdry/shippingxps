<template>
  <MainLayout>
    <div>
      <section>
        <div class=container>
          <div class=card>
            <div class=card-header><strong>Product Detail -{{ auction.name }}</strong></div>
            <div class=card-body>
              <table class="table table-borderd">
                <tr>
                  <th>Name</th>
                  <td>{{ auction.name }}</td>
                </tr>
                <tr>
                  <th>Category</th>
                  <td>{{ auction.category_name }}</td>
                </tr>
                <tr>
                  <th>Weight</th>
                  <td>{{ auction.weight + ' ' + auction.weight_unit }}</td>
                </tr>
                <tr>
                  <th>Dimensions</th>
                  <td>{{ auction.length + ' X ' + auction.width + ' X ' + auction.height + ' ' + auction.dimension_unit
                    }}
                  </td>
                </tr>
                <tr>
                  <th>Starting Price</th>
                  <td>{{ auction.starting_price }}</td>
                </tr>
                <tr>
                  <th>Ending At</th>
                  <td>{{ formatDate(auction.ending_at)}}</td>
                </tr>
                <tr>
                  <th>Feature Image</th>
                  <td><img style=width:100px;height:auto class=img-thumbnail :src="'/'+auction.thumbnail"
                           @click=viewImage($event)></td>
                </tr>
                <tr>
                  <th>Images</th>
                  <td>
                    <div class=row>
                      <div v-for="(image, index) in auction_images" :key=image.id class=col-md-2>
                        <div>
                          <img style=width:100px;height:auto class=img-thumbnail :src=imgURL(image.image)
                               @click=viewImage($event)>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </table>

              <template v-if="auction.bids.length > 0">
                <h3>Bids</h3>
                <table class="table table-borderd">
                  <tr>
                    <th>Sr #</th>
                    <th>Customer Name</th>
                    <th>Amount</th>
                    <th>At</th>
                  </tr>
                  <tr v-for="(bid,index) in auction.bids" v-bind:key="bid.id">
                    <td>{{ index+1 }}</td>
                    <td>{{ bid.customer_name }}</td>
                    <td>{{ bid.amount }}</td>
                    <td>{{ formatDateTime(bid.created_at) }}</td>
                  </tr>
                </table>
              </template>
            </div>
          </div>
        </div>
      </section>
    </div>
  </MainLayout>
</template>
<script>import MainLayout from "@/Layouts/Main";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import BreezeLabel from "@/Components/Label";
import BreezeValidationErrors from "@/Components/ValidationErrors";

export default {
  components: {
    BreezeAuthenticatedLayout: BreezeAuthenticatedLayout,
    MainLayout: MainLayout,
    BreezeLabel: BreezeLabel,
    BreezeValidationErrors: BreezeValidationErrors
  }, data() {
  }, props: {auth: Object, auction: Object, auction_images: Object},
  methods: {
    imgURL: e => "/" + e, viewImage(e) {
      console.log(e.target.src);
      var t = document.getElementById("imageViewer");
      document.querySelector("#imageViewer img").src = e.target.src, t.classList.add("show"), $("#imageViewer").show()
    },
    formatDate: e => new Date(e).toLocaleString("en-GB", {dateStyle: "short"}),
    formatDateTime: e => new Date(e).toLocaleString("en-GB")
  }
}</script>
<style scoped></style>