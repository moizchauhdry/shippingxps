<template>
  <Header></Header>

  <div class="container">
    <div class="main">
      <section class="py-5">
        <div class="container">
          <div class="row gx-5">
            <aside class="col-lg-6">
              <div class="border rounded-4 mb-3 d-flex justify-content-center">
                <a data-fslightbox="mygalley" class="rounded-4" target="_blank" data-type="image" href="javascript:void(0)">
                  <img style="max-width: 100%; max-height: 100vh; margin: auto;" class="rounded-4 fit" :src="'/'+auction.thumbnail" />
                </a>
              </div>
              <div class="d-flex justify-content-center mb-3">
                <template v-for="(image, index) in auction_images" :key="image.id">
                  <a data-fslightbox="mygalley" class="border mx-1 rounded-2 item-thumb" target="_blank" data-type="image" href="javascript:void(0)" >
                    <img width="60" height="60" class="rounded-2" :src="'/'+image.image" />
                  </a>
                </template>


              </div>
              <!-- thumbs-wrap.// -->
              <!-- gallery-wrap .end// -->
            </aside>
            <main class="col-lg-6">
              <div class="ps-lg-3">
                <h4 class="title text-dark">
                  {{ auction.name }}
                </h4>
<!--                <div class="d-flex flex-row my-3">
                  <div class="text-warning mb-1 me-2">
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                    <span class="ms-1">
                    4.5
                  </span>
                  </div>
                  <span class="text-muted"><i class="fas fa-shopping-basket fa-sm mx-1"></i>154 orders</span>
                  <span class="text-success ms-2">In stock</span>
                </div>-->

<!--                <div class="mb-3">
                  <span class="h5">$75.00</span>
                  <span class="text-muted">/per box</span>
                </div>-->

                <p v-html="auction.description"></p>

                <div class="row">
                  <dt class="col-3">Weight:</dt>
                  <dd class="col-9">{{ `${auction.weight} ${auction.weight_unit}`  }}</dd>

                  <dt class="col-3">Dimensition</dt>
                  <dd class="col-9">{{ `${auction.length}l X ${auction.width}w X ${auction.height}h  ${auction.dimension_unit}`  }}</dd>

                  <dt class="col-3">Current Bid</dt>
                  <dd class="col-9">$ {{ auction.latest_bid != undefined ? auction.latest_bid.amount : auction.starting_price }}</dd>

                  <dt class="col-3">Ending On</dt>
                  <dd class="col-9">{{ formatDate(auction.ending_at) }}</dd>

                  <dt class="col-3">Brand</dt>
                  <dd class="col-9">{{ timeLeft }}</dd>

                  <dt class="col-3">Brand</dt>
                  <dd class="col-9">Reebook</dd>
                </div>

                <hr />


                <template v-if="!$page.props.auth.user">
                  <a :href="route('login')" class="btn btn-warning shadow-0 mt-3"> Login To Bid  </a>
                </template>
                <template v-else>
                  <a v-if="$page.props.auth.user.type == 'customer'" href="javascript:void(0)" class="btn btn-warning shadow-0 mt-3" v-on:click="bidModal = true">Bid</a>

                </template>

              </div>
            </main>
          </div>
        </div>
      </section>
    </div>

    <div class="modal fade show" id="bidModal" tabindex="-1" role="dialog" aria-labelledby="bidModalTitle"  :style="bidModal ? 'display: block' : 'display: none'">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="bidModalLongTitle">BID - {{ auction.name }}</h5>
            <button type="button" class="btn btn-secondary close" v-on:click="bidModal = false" data-dismiss="modal" >&times;</button>
          </div>
          <div class="modal-body">
            <label for="bid-amount">Bid Amount</label><br>
            <input id="bid-amount" class="form-control" v-model="amount" type="number" name="amount" step="0.01" :min="auction.amount != undefined ? auction.latest_bid.amount : auction.starting_price">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" v-on:click="bidModal = false" data-dismiss="modal" >Close</button>
            <button type="button" class="btn btn-primary" v-on:click="updateBid">Confirm Bid</button>
          </div>
        </div>
      </div>
    </div>
  </div>



  <FooterSection></FooterSection>
</template>

<script>


import Header from '@/Components/Frontend/Header'
import FooterSection from '@/Components/Frontend/Footer'
import Pagination from "@/Components/Pagination";
import {Inertia} from "@inertiajs/inertia";

export default {
  name: "AuctionDetail",
  props: {
    errors: Object,
    auction: Object,
    auction_images: Object,
  },
  components: {
    Header,FooterSection, Pagination
  },
  data() {
    return {
      bidModal:false,
      amount: parseFloat((this.auction.latest_bid != null ? this.auction.latest_bid.amount : this.auction.starting_price) + 0.10).toFixed(2),
      timeLeft: '',
    }
  },
  computed: {
  },
  methods: {
    getRemainingTime(timestamp) {
      // Calculate the difference between the timestamp and the current time.

      let endDate = new Date(timestamp)

      const now = new Date();
      const delta = endDate - now;

      // Convert the difference to seconds.
      const seconds = delta / 1000;

      // Divide the number of seconds by the number of seconds in a day, hour, minute, and second to get the number of days, hours, minutes, and seconds remaining.
      const days = Math.floor(seconds / 86400);
      const hours = Math.floor((seconds % 86400) / 3600);
      const minutes = Math.floor(((seconds % 86400) % 3600) / 60);
      const secondsRemaining = ((seconds % 86400) % 3600) % 60;

      console.log(`${days}D ${hours}h ${minutes}m ${parseInt(secondsRemaining)}s`)
      console.log(days <= 0 && hours <= 0 && minutes <= 0 && secondsRemaining <= 0 )

      if(days <= 0 && hours <= 0 && minutes <= 0 && secondsRemaining <= 0 ){
        const url = `${route("auctions.index")}`;
        Inertia.visit(url, { preserveState: true });
      }

      let nextInterval = 1000;
      if(days > 0){
        this.timeLeft =  `${days}D ${hours}h ${minutes}m`;
        nextInterval = 60000

      }else{
        this.timeLeft =  `${hours}h ${minutes}m ${parseInt(secondsRemaining)}s`;

      }

      const ev = this;
      setTimeout(function(){
        ev.getRemainingTime(timestamp)
      },nextInterval)


    },
    formatDate(dateTime) {
      const today = new Date(dateTime);
      const formattedDate = today.toLocaleString("en-GB");
      return formattedDate;
    },
    updateBid(){
      let auctionID = this.auction.id
      if(this.amount <= (this.auction.latest_bid != null ? this.auction.latest_bid.amount : this.auction.starting_price)){
        alert('Price should be greater than current bid.')
        return;
      }else{
        axios.post(this.route('auctions.bid'), {
          id:this.auction.id,
          amount:this.amount,
        }).then(function (response) {
          if(response.data.status == 1){
            alert(response.data.message);
            const url = `${route("auctions.detail",auctionID)}`;
            Inertia.visit(url, { });
          }else{
            alert(response.data.message);
          }
        }).catch(function (error) {
          console.log(error);
        });
      }
    }

  },
  mounted() {
    const ending_at = this.auction.ending_at
    this.getRemainingTime(ending_at);
  }
}
</script>

<style scoped>

</style>