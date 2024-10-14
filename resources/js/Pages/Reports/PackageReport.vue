<template>
	<MainLayout>
		<div class="card mb-5 mt-2">
			<div class="card-header">Manage Reports</div>
			<div class="card-body">
				<form @submit.prevent="submit" class="search-form">
					<div class="d-flex search">
						<div class="form-group" v-if="filters.slug == 'packages'">
							<label for="">Service Type</label>
							<select class="form-control" v-model="form.search_service_type" style="width: 150px;">
								<option value="">All</option>
								<option value="dhl">DHL</option>
								<option value="fedex">Fedex</option>
								<option value="ups">UPS</option>
								<option value="usps">USPS</option>
							</select>
						</div>
						<div class="form-group">
							<label for="">Invoice No</label>
							<input type="text" class="form-control" v-model="form.search_invoice_no" />
						</div>
						<div class="form-group">
							<label for="">Suit No</label>
							<input type="text" class="form-control" v-model="form.search_suit_no" />
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

				<div class="table table-bordered stats-table">
					<tbody>
						<tr>
							<td><b>Total Charged Amount:</b> ${{ stats.total }}</td>

							<template v-if="filters.slug == 'packages'">
								<td><b>Total Shipping Gross:</b> ${{ stats.gross_shipping }}</td>

								<td><b>Total Markup:</b> ${{ stats.profit }}</td>
							</template>

							<template v-if="filters.slug == 'orders'">
								<td><b>Total Service Charges:</b> ${{ stats.service_charges }}</td>
							</template>
						</tr>
					</tbody>
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
								<th>SR #</th>
								<th>Invoice ID</th>
								<th>Customer</th>
								<th>Payment Type</th>
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
								<td>{{ payment.p_id }}</td>
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
								<td>{{ payment.t_id }}</td>
								<td>{{ payment.p_method }}</td>
								<td>{{ payment.charged_at }}</td>

								<template v-if="filters.slug == 'packages'">
									<td>{{ payment.pkg_service_label }}</td>
									<td>{{ payment.pkg_tracking_out }}</td>
									<td>${{ format_number(payment.shipping_charges_gross) }}</td>
									<td>{{ payment.shipping_markup_percentage }}%</td>
									<td>${{ format_number(payment.shipping_markup_fee) }}</td>
								</template>

								<template v-if="filters.slug == 'orders'">
									<td>${{ format_number(payment.order_service_charges) }}</td>
								</template>

								<td>${{ payment.charged_amount }}</td>

								<template v-if="filters.slug === 'packages'">
									<td>${{ format_number(payment.xls_carrier_cost) }}</td>
									<td>${{ format_number(payment.charged_amount - payment.xls_carrier_cost) }}</td>
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
			const url = `${route("report.index", this.filters.slug)}?${queryParams.toString()}`;
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
		format_number(number) {
			// if (typeof number !== 'number' || isNaN(number)) {
			// 	return 0;
			// }

			return new Intl.NumberFormat('en-US', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			}).format(number);
		}
	},
	created() {
		console.log(this.data);
	},
};
</script>


<style>
.dp__input {
	border-radius: 0px;
	padding: 4px 12px;
}


/* Handle mobile view better */
@media (max-width: 768px) {
	.search {
		flex-wrap: wrap;
	}

	.form-group {
		flex: 1 1 100%;
		margin-bottom: 5px;
		width: 100%;
	}

	.stats-table {
		display: block;
		/* Convert table into a block layout */
	}

	.stats-table tr {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
	}

	.stats-table td {
		width: 100%;
		/* Full width on mobile */
		display: flex;
		justify-content: space-between;
		/* margin-bottom: 10px; */
		border: none;
		/* Remove borders between items */
		border-bottom: 1px solid #dee2e6;
		/* Optional separator */
	}

	.stats-table td:last-child {
		border-bottom: none;
		/* Remove last border */
	}
}
</style>