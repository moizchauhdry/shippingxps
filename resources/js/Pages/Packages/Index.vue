<template>
	<MainLayout>
		<div class="card">
			<div class="card-header">					
				<b>Manage Packages</b>
				<template v-if="open_pkgs_count >= 2">
					<inertia-link
						:href="route('packages.consolidation')"
						class="btn btn-success float-right m-1"
						v-if="$page.props.auth.user.type == 'customer'"
					>
						<i class="fa fa-plus mr-1"></i>Package
						Consolidation</inertia-link
					>

					<inertia-link
						:href="route('packages.multipiece')"
						class="btn btn-primary float-right m-1"
						v-if="$page.props.auth.user.type == 'customer'"
					>
						<i class="fa fa-plus mr-1"></i>Multipiece
						Package</inertia-link
					>
				</template>

				<inertia-link :href="route('orders.create')" class="btn btn-success float-right m-1" v-if="$page.props.auth.user.type == 'admin'">
					<i class="fa fa-plus mr-1"></i>Add Package</inertia-link>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<form @submit.prevent="submit">
							<div class="row">
								<div class="form-group col-md-3">
									<label for="">Package Number</label>
									<input type="number" name="number" v-model="form.pkg_id" class="form-control"/>
								</div>
								<div class="form-group col-md-3">
									<label for="">Suit Number</label>
									<input type="number" name="number" v-model="form.suit_no" class="form-control"/>
								</div>
								<div class="form-group col-md-3">
									<label for="">Package Status</label>
									<select class="form-control custom-select" v-model="form.pkg_status" >
										<option value="" selected>All</option>
										<option value="open">Open</option>
										<option value="filled">Filled</option>
										<option value="checkout">Checkout</option>
										<option value="mailout">Mailout</option>
									</select>
								</div>
								<div class="form-group col-md-3">
									<label for="">Package Type</label>
									<select class="form-control custom-select" v-model="form.pkg_type" >
										<option value="" selected>All</option>
										<option value="single">Single</option>
										<option value="consolidation">Consolidation</option>
										<option value="multipiece">Multipiece</option>
										<option value="assigned">Assigned</option>
									</select>
								</div>
								<div class="form-group col-md-3">
									<label for="">Payment Status</label>
									<select class="form-control custom-select" v-model="form.payment_status" >
										<option value="" selected>All</option>
										<option value="Paid">Paid</option>
										<option value="Pending">Pending</option>
									</select>
								</div>
								<div class="col-md-3 form-group">
									<label for="">Date Range</label>
									<Datepicker v-model="date" range :format="format" :enableTimePicker="false"></Datepicker>
								</div>
								<div class="form-group col-md-4">
									<button type="submit" class="btn btn-primary mr-1">Search</button>
									<button type="button" class="btn btn-info" @click="clear()">Clear</button>
								</div>
							</div>
						</form>
					</div>
				</div>

				<div class="row my-4">
					<div class="col-md-6">
						<button
							type="button"
							:class="{ active: active === 'packages' }"
							class="btn btn-light w-100"
							@click="searchPackage('packages')"
						>
							Packages
						</button>
					</div>
					<div class="col-md-6">
						<button
							type="button"
							:class="{ active: active === 'rejected' }"
							class="btn btn-light w-100"
							@click="searchPackage('rejected')"
						>
							Rejected
						</button>
					</div>
				</div>
				<package-list-component v-bind="$props"></package-list-component>
			</div>
		</div>
	</MainLayout>
</template>
<style scoped>
	.label {
		padding: 5px;
	}
</style>
<script>
	import MainLayout from "@/Layouts/Main";
	import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
	import { Inertia } from "@inertiajs/inertia";
	import { useForm } from "@inertiajs/inertia-vue3";
	import PackageListComponent from "./Components/PackageListComponent.vue";
	import Datepicker from "vue3-date-time-picker";
	import "vue3-date-time-picker/dist/main.css";
	
	export default {
		components: {
			BreezeAuthenticatedLayout,
			MainLayout,
			PackageListComponent,
			Datepicker
		},
		props: {
			auth: Object,
			pkgs: Object,
			open_pkgs_count: Object,
			filter: Object,
		},
		data() {
			return {
				active: "packages",
				date: "",
				form: useForm({
					pkg_id: "",
					suit_no: "",
					pkg_status: "",
					pkg_type: "",
					payment_status: "",
					date_range: "",
				}),
				pkg_form: {
					status: this.filter.status,
					pkg_id: "",
					suit_no: "",
					pkg_status: "",
					pkg_type: "",
					payment_status: "",
					date_range: "",
					processing: false,
				},
			};
		},
		methods: {
			searchPackage(status) {
				this.active = status;
				this.pkg_form.status = status;
				this.pkg_form.pkg_id = this.form.pkg_id;
				this.pkg_form.suit_no = this.form.suit_no;
				this.pkg_form.pkg_status = this.form.pkg_status;
				this.pkg_form.pkg_type = this.form.pkg_type;
				this.pkg_form.payment_status = this.form.payment_status;
				this.pkg_form.date_range = this.form.date_range;
				Inertia.post(route("packages.index"), this.pkg_form);
			},
			siuteNum(user_id) {
				return 4000 + user_id;
			},
			submit() {
				this.searchPackage(this.pkg_form.status);
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
				// this.getResults(route("payments.getPayments"));
				return `${startDay}/${startMonth}/${startYear} - ${endDay}/${endMonth}/${endYear}`;
			},
		},
		watch: {
			params: {
				handler() {
					this.$inertia.get(this.route("packages.index"), this.params, {
						replace: true,
						preserveState: true,
					});
				},
			},
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
</style>
