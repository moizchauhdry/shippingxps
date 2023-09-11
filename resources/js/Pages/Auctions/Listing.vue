<template>
  <MainLayout>
    <div class="card mb-5">
      <div class="card-header">
        <b>Manage Auctions</b>
<!--        <template v-if="open_pkgs_count >= 2">
          <inertia-link
              :href="route('packages.consolidation')"
              class="btn btn-success float-right mr-1"
              v-if="$page.props.auth.user.type == 'customer'"
          >
            <i class="fa fa-plus mr-1"></i>Package
            Consolidation</inertia-link
          >

          <inertia-link
              :href="route('packages.multipiece')"
              class="btn btn-primary float-right mr-1"
              v-if="$page.props.auth.user.type == 'customer'"
          >
            <i class="fa fa-plus mr-1"></i>Multipiece
            Package</inertia-link
          >
        </template>-->

        <inertia-link :href="route('auctions.create')" class="btn btn-success float-right mr-1">
          <i class="fa fa-plus mr-1"></i>Add Product</inertia-link>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
           <form @submit.prevent="submit">
              <div class="d-flex search">
                <div class="form-group">
                  <label for="">Category</label>
                  <select class="form-control custom-select" v-model="form.auction_category_id" >
                    <option value="" selected>All</option>
                    <option
													v-for="category in categories"
													:value="category.id"
													:key="category.id"
												>
													{{ category.name }}
												</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="">Status</label>
                  <select class="form-control custom-select" v-model="form.status" >
                    <option value="" selected>All</option>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                  </select>
                </div>
                
                <div class="form-group">
                  <label for="">Date Range</label>
                  <Datepicker v-model="date" range :format="format" :enableTimePicker="false"></Datepicker>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-12">
                  <button type="submit" class="btn btn-primary mr-1">Search</button>
                  <button type="button" class="btn btn-info" @click="clear()">Clear</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-striped table-bordered text-center text-sm table-sm table-hover">
            <thead>
            <tr>
              <th scope="col">SR #</th>
              <th scope="col">Name</th>
              <th scope="col">ٰImage</th>
              <th scope="col">Ending At</th>
              <th scope="col">Status</th>
              <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(auction, index) in auctions.data" :key="auction.id">
              <td>{{ ++index }}</td>
              <td>{{ auction.name }}</td>
              <td> <img style="width: 150px;height:auto" :src="imgURL(auction.thumbnail)"></td>
              <td>{{ formatDate(auction.ending_at) }}</td>
              <td>
                <span class="mr-1" :class="getLabelClass(auction.status)">{{ auction.status == 1 ? 'Active' : 'Inactive' }}</span>
              </td>
              <td>
                        <inertia-link class="btn btn-primary btn-sm m-1" :href="route('auctions.edit', auction.id)"><i class="fa fa-pencil-alt mr-1"></i>Edit</inertia-link>
                        <inertia-link class="btn btn-info btn-sm m-1" :href="route('auctions.show', auction.id)"><i class="fa fa-list mr-1"></i>Detail</inertia-link>
                    </td>
            </tr>
            <tr v-if="auctions.data.length == 0">
              <td class="text-primary text-center" colspan="9">
                There are no auctions found.
              </td>
            </tr>
            </tbody>
          </table>
        </div>

      </div>
      <div class="card-footer">
        <pagination :links="auctions.links" class="float-right"></pagination>
      </div>
    </div>
  </MainLayout>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import { Inertia } from "@inertiajs/inertia";
import Datepicker from "vue3-date-time-picker";
import "vue3-date-time-picker/dist/main.css";
import Pagination from "@/Components/Pagination.vue";

export default {
  components: {
    BreezeAuthenticatedLayout,
    MainLayout,
    Datepicker,
    Pagination
  },
  props: {
    auth: Object,
    auctions: Object,
    categories: Object,
    filters: Object,
  },
  data() {
    return {
      active: "auctions",
      date: "",
      form: {
        status: this.filters.status,
        auction_category_id: this.filters.auction_category_id,
        date_range: this.filters.date_range,
      },
    };
  },
  methods: {
    imgURL(url) {
				return '/'+url;
			},
      submit() {
      const queryParams = new URLSearchParams(this.form);
      const url = `${route("auctions.listing")}?${queryParams.toString()}`;
      Inertia.visit(url, { preserveState: true });
    },
    format() {
      var start = new Date(this.date[0]);
      var end = new Date(this.date[1]);
      var startDay = start.getDate();
      var startMonth = start.getMonth() + 1;
      var startYear = start.getFullYear();
      var endDay = end.getDate();
      var endMonth = end.getMonth() + 1;
      var endYear = end.getFullYear();

      this.form.date_range = `${startYear}/${startMonth}/${startDay} - ${endYear}/${endMonth}/${endDay}`;
      return `${startDay}/${startMonth}/${startYear} - ${endDay}/${endMonth}/${endYear}`;
    },
    clear() {
      this.form.status = "";
      this.form.auction_category_id = "";
      this.form.date_range = "";
      this.submit();
    },
    getLabelClass(status) {
			switch (status) {
				case "0":
					return "text-uppercase badge badge-danger text-white";
					break;
				case "1":
					return "text-uppercase badge badge-success text-white";
					break;
				default:
					return "text-uppercase badge badge-primary text-white";
			}
		},
    formatDate(dateTime) {
      const today = new Date(dateTime);
      const formattedDate = today.toLocaleString("en-GB", {
        dateStyle: "short",
      });
      return formattedDate;
    },
    /*submit() {
      const queryParams = new URLSearchParams(this.form);
      const url = `${route("packages.index")}?${queryParams.toString()}`;
      Inertia.visit(url, { preserveState: true });
    },*/
    /*siuteNum(user_id) {
      return 4000 + user_id;
    },
    clear() {
      this.form.pkg_id = "";
      this.form.suit_no = "";
      this.form.pkg_status = "";
      this.form.pkg_type = "";
      this.form.payment_status = "";
      this.form.date_range = "";
      this.date = "";
      this.submit();
    },
    format() {
      var start = new Date(this.date[0]);
      var end = new Date(this.date[1]);
      var startDay = start.getDate();
      var startMonth = start.getMonth() + 1;
      var startYear = start.getFullYear();
      var endDay = end.getDate();
      var endMonth = end.getMonth() + 1;
      var endYear = end.getFullYear();

      this.form.date_range = `${startYear}/${startMonth}/${startDay} - ${endYear}/${endMonth}/${endDay}`;
      return `${startDay}/${startMonth}/${startYear} - ${endDay}/${endMonth}/${endYear}`;
    },*/
  },
};
</script>

<style>
button.active.btn.btn-light.w-100 {
  background-color: red !important;
  color: white;
}

.dp__input {
  background-color: var(--dp-background-color);
  border-radius: 0px;
  font-family: -apple-system,blinkmacsystemfont,"Segoe UI",roboto,oxygen,ubuntu,cantarell,"Open Sans","Helvetica Neue",sans-serif;
  border: 1px solid var(--dp-border-color);
  outline: none;
  transition: border-color .2s cubic-bezier(0.645, 0.045, 0.355, 1);
  width: 100%;
  font-size: 1rem;
  line-height: 1.5rem;
  padding: 4px 33px;
  color: var(--dp-text-color);
  box-sizing: border-box;
}

.label {
  padding: 5px;
}

.search .form-group {
  margin-left:1px
}
</style>
