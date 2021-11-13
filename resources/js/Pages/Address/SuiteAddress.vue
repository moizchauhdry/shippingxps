<template>
  <MainLayout>
    <div class="row mt-4">
      <div class="col-md-7 offset-md-1">
        <div class="card mt-4">
          <div class="card-header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Using your Addresses
            </h2>
          </div>
          <div class="card-body">
            <p>
              How to use your addresses when purchasing from online merchants.
            <ul>
              <li>1: Purchase form merchants</li>
              <li>2: Use one of these addresses</li>
              <li>3: We will deliver items to your address outside US. (Listed in address book)</li>
            </ul>
            </p>
          </div>
        </div>
      </div>
      <div class="col-md-4 offset-md-1">
        <div class="card">
          <div class="card-header">
            California Mailing Address
          </div>
          <div class="card-body">
            <!--                        <p><strong>Full Name:</strong>{{ siuteNum }} – {{ $page.props.auth.user.name }} </p>
                                    <p><strong>Street Address:</strong>3578 W SAVANNA ST </p>
                                    <p><strong>City:</strong>ANAHEIM </p>
                                    <p><strong>Suite #:</strong> {{ siuteNum }} </p>
                                    <p><strong>State:</strong>CA </p>
                                    <p><strong>Zip code:</strong>92804 </p>
                                    <p><strong>Phone Number:</strong>657-201-7881 </p>        -->
            <div class="address-card mb-4">
              <p><strong>Full Name:</strong><span class="copy-item" id="copy-item-1" v-on:click="copyContent($event)">{{ siuteNum }} – {{ $page.props.auth.user.name }}</span> <span v-if="copied_id=='copy-item-1'" style="color:green">Copied!</span></p>
              <p><strong>Street Address:</strong><span class="copy-item" id="copy-item-2" v-on:click="copyContent($event)">3578 W SAVANNA ST </span><span v-if="copied_id=='copy-item-2'" style="color:green">Copied!</span></p>
              <p><strong>City:</strong><span class="copy-item" id="copy-item-3" v-on:click="copyContent($event)">ANAHEIM </span><span v-if="copied_id=='copy-item-3'" style="color:green">Copied!</span></p>
              <p><strong>Suite #:</strong><span class="copy-item" id="copy-item-4" v-on:click="copyContent($event)"> {{ siuteNum }} </span><span v-if="copied_id=='copy-item-4'" style="color:green">Copied!</span></p>
              <p><strong>State:</strong><span class="copy-item" id="copy-item-5" v-on:click="copyContent($event)">CA </span><span v-if="copied_id=='copy-item-5'" style="color:green">Copied!</span></p>
              <p><strong>Zip code:</strong><span class="copy-item" id="copy-item-6" v-on:click="copyContent($event)">92804 </span><span v-if="copied_id=='copy-item-5'" style="color:green">Copied!</span></p>
              <p><strong>Phone Number:</strong><span class="copy-item" id="copy-item-7" v-on:click="copyContent($event)">657-201-7881 </span><span v-if="copied_id=='copy-item-7'" style="color:green">Copied!</span></p>
            </div>
            <br/><br/>
            <a href="javascript:void(0)" class="btn btn-primary" @click="copyToClipBoard(address1)">
              <span v-if="!copied1">Click Here To Copy</span>
              <span v-if="copied1">Copied</span>
            </a>
          </div>
        </div>
      </div>
      <div class="col-md-4 offset-md-1">
        <div class="card">
          <div class="card-header">
            Delaware Mailing Address
          </div>
          <div class="card-body">
            <!--                        <p><strong>Full Name:</strong>{{ siuteNum }} – {{ $page.props.auth.user.name }} </p>
                                    <p><strong>Street Address:</strong>1217 OLD COOCH BRIDGE RD </p>
                                    <p><strong>City:</strong>Newark </p>
                                    <p><strong>Suite #:</strong>{{ siuteNum }} </p>
                                    <p><strong>State:</strong>DE </p>
                                    <p><strong>Phone Number:</strong>657-201-7881 </p>        -->
            <div class="address-card">
              <p><strong>Full Name:</strong><span class="copy-item" id="copy-item-8" v-on:click="copyContent($event)">{{ siuteNum }} – {{ $page.props.auth.user.name }}</span> <span v-if="copied_id=='copy-item-8'" style="color:green">Copied!</span></p>
              <p><strong>Street Address:</strong><span class="copy-item" id="copy-item-9" v-on:click="copyContent($event)">1217 OLD COOCH BRIDGE RD</span> <span v-if="copied_id=='copy-item-9'" style="color:green">Copied!</span></p>
              <p><strong>Suite #:</strong> <span class="copy-item" id="copy-item-10" v-on:click="copyContent($event)">{{ siuteNum }} </span><span v-if="copied_id=='copy-item-10'" style="color:green">Copied!</span></p>
              <p><strong>City:</strong><span class="copy-item" id="copy-item-11" v-on:click="copyContent($event)">NEWARK</span> <span v-if="copied_id=='copy-item-11'" style="color:green">Copied!</span></p>
              <p><strong>State:</strong><span class="copy-item" id="copy-item-12" v-on:click="copyContent($event)">DE </span><span v-if="copied_id=='copy-item-12'" style="color:green">Copied!</span></p>
              <p><strong>Phone Number:</strong><span class="copy-item" id="copy-item-13" v-on:click="copyContent($event)">657-201-7881</span> <span v-if="copied_id=='copy-item-13'" style="color:green">Copied!</span></p>
            </div>
            <br/><br/><br/>
            <a href="javascript:void(0)" class="btn btn-primary" @click="copyToClipBoard(address2)">
              <span v-if="!copied2">Click Here To Copy</span>
              <span v-if="copied2">Copied</span>
            </a>
          </div>
        </div>
      </div>
    </div>


  </MainLayout>
</template>
<style scoped>
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
  height: 190px;
}

.copy-item:hover {
  cursor: pointer;
  border: 1px dotted green;
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
  data() {
    return {
      address1: '3578 w savanna st Suite #:AD - 400' + this.$page.props.auth.user.id + ',Anaheim CA ,92804',
      address2: '1217 old cooch bridge rd Suite #:AD - 400' + this.$page.props.auth.user.id + ' ,Newark, Delaware ',
      copied1: false,
      copied2: false,
    }
  },
  props: {
    auth: Object,
    addresses: Object
  },
  computed: {
    siuteNum() {
      return 4000 + this.$page.props.auth.user.id;
    },
  },
  methods: {
    copyToClipBoard(address) {
      if (address.includes("92804")) {
        this.copied1 = true;
      } else {
        this.copied2 = true;
      }
      var text = address;
      navigator.clipboard.writeText(text).then(function () {
      }, function (err) {
        console.error('Async: Could not copy text: ', err);
      });
    },
    copyContent(event) {
      var text = event.target.innerText;
      this.copied_id = event.target.id;
      navigator.clipboard.writeText(text).then(function () {
      }, function (err) {
        console.error('Async: Could not copy text: ', err);
      });
    },
  }
}
</script>
