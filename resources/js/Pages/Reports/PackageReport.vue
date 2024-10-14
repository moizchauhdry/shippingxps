<template>
	<MainLayout>
		<div class="card mb-5 mt-2">
			<div class="card-header">Manage Reports</div>
			<div class="card-body">
				<form @submit.prevent="submit">
					<div class="d-flex search">
						<!-- <div class="form-group">
							<label for="">Payment Module</label>
							<select class="form-control form-select" v-model="filters.slug"
								@change="submit" style="width: 150px;">
								<option value="">All</option>
								<option value="package">Package</option>
								<option value="order">Order</option>
							</select>
						</div> -->
						<div class="form-group">
							<label for="">Invoice No</label>
							<input type="text" class="form-control" v-model="form.search_invoice_no" />
						</div>
						<div class="form-group">
							<label for="">Suit No</label>
							<input type="text" class="form-control" v-model="form.search_suit_no" />
						</div>
						<div class="form-group">
							<label for="">Service Type</label>
							<select class="form-control form-select" v-model="form.search_service_type"
								style="width: 150px;">
								<option value="">All</option>
								<option value="dhl">DHL</option>
								<option value="fedex">Fedex</option>
								<option value="ups">UPS</option>
								<option value="usps">USPS</option>
							</select>
						</div>
						<div class="form-group">
							<label for="">Tracking Out</label>
							<input type="text" class="form-control" v-model="form.search_tracking_out" />
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

				<div class="container-fluid mb-2">
					<div class="row">
						<div class="col-md-2">
							<div class="card">
								<div class="card-header">
									<h2>Total Charged Amount</h2>
								</div>
								<div class="card-body">
									<p class="text-lg">$ {{ stats.total }}</p>
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="card">
								<div class="card-header">
									<h2>Total Shipping Gross</h2>
								</div>
								<div class="card-body">
									<p class="text-lg">$ {{ stats.gross_shipping }}</p>
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="card">
								<div class="card-header">
									<h2>Total Markup</h2>
								</div>
								<div class="card-body">
									<p class="text-lg">$ {{ stats.profit }}</p>
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="card">
								<div class="card-header">
									<h2>Total Service Charges</h2>
								</div>
								<div class="card-body">
									<p class="text-lg">$ {{ stats.service_charges }}</p>
								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="table-responsive">
					<table class="table table-bordered" style="white-space: nowrap;text-transform: uppercase">
						<thead class="bg-light">
							<tr v-if="filters.slug == 'packages' || filters.slug == 'orders'">
								<th colspan="7" class="text-center"></th>
								<th colspan="6" class="text-center" v-if="filters.slug == 'packages'">
									Package Charges</th>
								<th colspan="4" class="text-center" v-if="filters.slug == 'orders'">Order
									Charges</th>
								<th colspan="2" class="text-center" v-if="filters.slug == 'packages'">
									{{ form.search_service_type }}
								</th>
							</tr>
							<tr>
								<th>SR. #</th>
								<th>Customer</th>
								<th>Payment Type</th>
								<th>Invoice ID</th>
								<th>Transaction ID</th>
								<th>Payment Method</th>
								<th>Charged Date</th>

								<template v-if="filters.slug == 'packages'">
									<th>Shipping Service</th>
									<th>Tracking Number</th>

									<th>Shipping Gross</th>
									<th>Markup %</th>
									<th>Markup Amount</th>
								</template>

								<template v-if="filters.slug == 'orders'">
									<th>Service Charges</th>
								</template>

								<th>Net Charge</th>

								<template v-if="filters.slug == 'packages'">
									<th>{{ form.search_service_type }} Cost</th>
									<th>Profit Amount</th>
								</template>

							</tr>
						</thead>
						<tbody>
							<tr v-for="(payment, index) in payments.data" :key="payment.id">
								<td>{{ (payments.current_page - 1) * payments.per_page + index + 1 }}</td>
								<td>{{ payment.u_name }} - {{ suiteNum(payment.u_id) }}</td>
								<td>
									<template v-if="payment.p_type === 'package'">
										<inertia-link :href="route('packages.show', payment.p_type_id)">
											<span class="font-bold text-primary underline">{{ payment.p_type }} - {{
												payment.p_type_id }}</span>
										</inertia-link>
									</template>

									<template v-if="payment.p_type === 'order'">
										{{ payment.p_type }} - {{ payment.p_type_id }}
									</template>
									<inertia-link v-if="payment.p_type === 'gift'"
										:href="route('gift-card.edit', payment.p_type_id)">
										{{ payment.p_type }} - {{ payment.p_type_id }}
									</inertia-link>
								</td>
								<td>{{ payment.p_id }}</td>
								<td>{{ payment.t_id }}</td>
								<td>{{ payment.p_method }}</td>
								<td>{{ payment.charged_at }}</td>

								<template v-if="filters.slug == 'packages'">
									<td>{{ payment.pkg_service_label }}</td>
									<td>{{ payment.pkg_tracking_out }}</td>
									<td>${{ payment.shipping_charges_gross }}</td>
									<td>{{ payment.shipping_markup_percentage }}%</td>
									<td>${{ payment.shipping_markup_fee }}</td>
								</template>

								<template v-if="filters.slug == 'orders'">
									<td>${{ payment.order_service_charges }}</td>
								</template>

								<td>${{ payment.charged_amount }}</td>

								<template v-if="filters.slug === 'packages'">
									<td>-</td>
									<td>-</td>
								</template>
							</tr>
							<tr v-if="payments.data.length == 0">
								<td class="text-primary text-center" colspan="14">
									There are no payments found.
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="card-footer">
				<pagination :links="payments.links" class="float-right"></pagination>
			</div>
		</div>
	</MainLayout>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import BreezeLabel from "@/Components/Label";
import Paginate from "@/Components/Paginate";
import Datepicker from "vue3-date-time-picker";
import "vue3-date-time-picker/dist/main.css";
import Pagination from "@/Components/Pagination.vue";
import { Inertia } from "@inertiajs/inertia";

export default {
	data() {
		return {
			form: {
				search_payment_module: this.filters.search_payment_module,
				search_service_type: this.filters.search_service_type,
				search_invoice_no: this.filters.search_invoice_no,
				search_suit_no: this.filters.search_suit_no,
				search_tracking_out: this.filters.search_tracking_out,
				date_range: this.filters.date_range,
			},
			date: "",
		};
	},
	components: {
		BreezeAuthenticatedLayout,
		MainLayout,
		BreezeLabel,
		Paginate,
		Datepicker,
		Pagination
	},
	props: {
		auth: Object,
		stats: Object,
		payments: Object,
		filters: Object,
	},
	mounted() { },
	methods: {
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
		submit() {
			const queryParams = new URLSearchParams(this.form);
			const url = `${route("report.index")}?${queryParams.toString()}`;
			Inertia.visit(url, { preserveState: true });
		},
		suiteNum(user_id) {
			return 4000 + user_id;
		},
		clear() {
			this.form = {
				search_payment_module: "",
				search_service_type: "",
			};
			this.date = "";
			this.submit();
		},
	},
	created() {
		console.log(this.data);
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
	font-family: -apple-system, blinkmacsystemfont, "Segoe UI", roboto, oxygen, ubuntu, cantarell, "Open Sans", "Helvetica Neue", sans-serif;
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
	margin-left: 1px
}
</style>
<style>
/* .full-width-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  } */

/* .full-width-table th,
  .full-width-table td {
    white-space: nowrap;
    overflow: hidden; 
    text-overflow: ellipsis;
    border: 1px solid #ddd;
    padding: 8px;
  } */
</style>