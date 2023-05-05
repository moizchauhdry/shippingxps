<template>
	<MainLayout>
		<div class="card mb-5">
			<div class="card-header"><b>Manage Shopping List</b></div>
			<div class="card-body">
				<div class="row mb-4">
					<div class="col-md-6">
						<form action="/shop-for-me" method="get">
							<input
								type="text"
								name="search"
								class="form-control"
								placeholder="Search"
							/>
						</form>
					</div>
					<div class="col-md-6">
						<template v-if="$page.props.auth.user.type == 'customer'">
							<inertia-link
								:href="route('shop-for-me.create')"
								class="btn btn-success float-right"
							>
								<i class="fa fa-plus mr-1"></i>Add New Order</inertia-link
							>
						</template>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table table-striped table-bordered">
						<thead class="text-center">
							<tr>
								<th scope="col">Sr. #</th>
								<th scope="col">Warehouse</th>
								<th scope="col">Customer</th>
								<th scope="col">Type</th>
								<th scope="col">Status</th>
								<th scope="col">Site Name</th>
								<th scope="col">Site URL</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="text-center">
							<tr v-for="order in orders.data" :key="order.id">
								<td>{{ order.id }}</td>
								<td>{{ order.warehouse.name }}</td>
								<td v-if="order.customer">
									<inertia-link
										:href="route('detail-customer', order.customer.id)"
										class="btn btn-link"
									>
										# {{ siuteNum(order.customer.id) }} -
										{{ order.customer.name }}
									</inertia-link>
								</td>
								<td class="capitalize">{{ order.order_origin }}</td>
								<td class="capitalize">
									<span v-bind:class="getLabelClass(order.status)">{{
										order.status
									}}</span>
									<span
										v-if="order.payment_status == 'Paid'"
										class="label bg-success text-white font-bold ml-2"
									>
										{{ order.payment_status }}
									</span>
								</td>
								<td>
									<template v-if="order.site_name !== null">
										<span v-if="order.site_name.length < 30">{{
											order.site_name
										}}</span>
										<span v-else
											>Welcome,
											{{ order.site_name.substring(0, 30) + "..." }}</span
										>
									</template>
								</td>
								<td>
									<template v-if="order.site_url !== null">
										<a
											target="_blank"
											class="link-primary"
											:href="'//' + order.site_url"
										>
											<span v-if="order.site_url.length < 30">{{
												order.site_url != null ? order.site_url : "- -"
											}}</span>
											<span v-else
												>Welcome,
												{{ order.site_url.substring(0, 30) + "..." }}</span
											>
										</a>
									</template>
								</td>
								<td style="min-width: 70px">
									<inertia-link
										class="btn btn-primary btn-xs mr-1 mb-1"
										:href="route('shop-for-me.show', order.id)"
									>
										<span><i class="fa fa-list mr-1"></i></span>Detail
									</inertia-link>

									<template
										v-if="
											order.status == 'pending' ||
											order.payment_status == 'Pending' ||
											$page.props.auth.user.type == 'admin'
										"
									>
										<inertia-link
											class="btn btn-success btn-xs mr-1 mb-1"
											:href="route('shop-for-me.edit', order.id)"
										>
											<span><i class="fa fa-pencil-alt"></i></span> Edit &
											Continue
										</inertia-link>
									</template>

									<template
										v-if="
											order.status == 'labeled' && order.order_type == 'package'
										"
									>
										<a
											target="_blank"
											class="btn btn-info btn-xs mr-1 mb-1"
											:href="route('packages.pdf', order.id)"
											title="Invoice"
										>
											<i class="fa fa-file"></i> Print Invoice
										</a>
									</template>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
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

	export default {
		components: {
			BreezeAuthenticatedLayout,
			MainLayout,
		},
		props: {
			auth: Object,
			orders: Object,
		},
		data() {
			return {
				form: this.$inertia.form({
					search: "",
				}),
				params: {
					search: null,
				},
			};
		},
		watch: {
			params: {
				handler() {
					this.$inertia.get(this.route("shop-for-me"), this.params, {
						replace: true,
						preserveState: true,
					});
				},
			},
		},
		methods: {
			getLabelClass(status) {
				switch (status) {
					case "pending":
						return "label bg-warning text-white font-bold";
						break;
					case "arrived":
						return "label bg-primary text-white font-bold";
						break;
					case "labeled":
						return "label bg-info text-white font-bold";
						break;
					case "shipped":
						return "label bg-warning text-white font-bold";
						break;
					case "delivered":
						return "label bg-success text-white font-bold";
						break;
					case "rejected":
						return "label bg-danger text-white font-bold";
						break;
					case "approved":
						return "label bg-success text-white font-bold";
						break;
					default:
						return "label bg-warning text-white font-bold";
				}
			},
			siuteNum(user_id) {
				return 4000 + user_id;
			},
		},
	};
</script>
