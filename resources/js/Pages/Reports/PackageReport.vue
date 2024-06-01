<template>
	<MainLayout>
		<div class="card mb-5">
			<div class="card-header">Manage Reports</div>
			<div class="card-body">
				<form @submit.prevent="submit">
					<div class="d-flex search">
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

				<div class="table-responsive">
					<table class="table table-striped table-bordered text-uppercase">
						<thead>
							<tr>
								<th>Invoive ID</th>
								<th>Customer</th>
								<th>Payment Type</th>
								<th>Transaction ID</th>
								<th>Charged Date</th>
								<th>charged Amount</th>
								<th>Shipping Total</th>
								<th>Markup Percentage</th>
								<th>Markup / Profit</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(payment, index) in payments.data" :key="payment.id">
								<td>{{ payment.p_id }}</td>
								<td>{{ payment.u_name }} - {{ siuteNum(payment.u_id) }}</td>
								<td>
									<template v-if="payment.p_type === 'package'">
										<inertia-link :href="route('packages.show', payment.p_type_id)">
											<span class="font-bold text-primary underline">{{ payment.p_type }} - {{
												payment.p_type_id }}</span>
										</inertia-link>
										<br>
										{{ payment.pkg_service_label }}
										<br>
										{{ payment.pkg_tracking_out }}
									</template>


									<template v-if="payment.p_type === 'order'">
										{{ payment.p_type }} - {{ payment.p_type_id }}
									</template>
									<inertia-link v-if="payment.p_type === 'gift'"
										:href="route('gift-card.edit', payment.p_type_id)">
										{{ payment.p_type }} - {{ payment.p_type_id }}
									</inertia-link>
								</td>
								<td>
									{{ payment.t_id }} <br>
									{{ payment.p_method }}
								</td>
								<td>{{ payment.charged_at }}</td>
								<td>${{ payment.charged_amount }}</td>
								<td>${{ payment.shipping_charges_gross }}</td>
								<td>{{ payment.shipping_markup_percentage }}%</td>
								<td>${{ payment.shipping_markup_fee }}</td>
							</tr>
							<tr v-if="payments.data.length == 0">
								<td class="text-primary text-center" colspan="9">
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
		siuteNum(user_id) {
			return 4000 + user_id;
		},
		clear() {
			this.form = {};
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
