<template>
	<div class="table-responsive">
		<table class="table table-striped table-bordered text-center text-sm table-sm table-hover">
			<thead>
				<tr>
					<th scope="col">SR #</th>
					<th scope="col">Package ID</th>
					<th scope="col">Warehouse</th>
					<th scope="col">Status</th>
					<th scope="col">Customer</th>
					<th scope="col">Total</th>
					<th scope="col">Created Date</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(pkg, index) in pkgs.data" :key="pkg.id">
					<td>{{ ++index }}</td>
					<td style="width: 200px;">
						<span class="badge badge-primary text-sm">PKG #{{ pkg.id }}</span> <br>
						<template v-for="child_pkg in pkg.child_packages" :key="child_pkg.id">
							<span class="badge badge-info mr-1 mb-1" v-if="child_pkg.id != pkg.id">
								PKG #{{ child_pkg.id }}</span>
						</template>
					</td>
					<td>
						{{ pkg?.warehouse?.name }}
					</td>
					<td>
						<span class="mr-1" :class="getLabelClass(pkg.status)">{{ pkg.status }}</span>
						<span class="badge badge-warning text-uppercase mr-1"
							v-if="pkg.pkg_type == 'consolidation' || pkg.pkg_type == 'multipiece'">{{ pkg.pkg_type }}</span>
						<span class="badge badge-success text-uppercase mr-1"
							v-if="pkg.payment_status == 'Paid'">Paid</span>
						<span class="badge badge-warning text-uppercase mr-1" v-if="pkg.auctioned == 1">Auctioned</span>
					</td>
					<td>
						<inertia-link :href="route('customers.show', pkg?.customer?.id)" class="btn btn-link">
							{{ pkg?.customer?.name }} - {{ siuteNum(pkg?.customer?.id) }}
						</inertia-link>
					</td>
					<td>{{ pkg.grand_total }}</td>
					<td>{{ pkg.created_at }}</td>
					
				</tr>
				<tr v-if="pkgs.data.length == 0">
					<td class="text-primary text-center" colspan="9">
						There are no packages found.
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
export default {
	name: "Packages List",
	props: {
		auth: Object,
		pkgs: Object,
		filter: Object,
	},
	data() {
		return {
			//
		};
	},
	methods: {
		getLabelClass(status) {
			switch (status) {
				case "pending":
					return "text-uppercase badge badge-warning text-white";
					break;
				case "open":
					return "text-uppercase badge badge-info text-white";
					break;
				case "filled":
					return "text-uppercase badge badge-info text-white";
					break;
				case "open":
					return "text-uppercase badge badge-success text-white";
					break;
				case "labeled":
					return "text-uppercase badge badge-success text-white";
					break;
				case "shipped":
					return "text-uppercase badge badge-primary p-1";
					break;
				case "delivered":
					return "text-uppercase badge badge-success text-white";
					break;
				case "consolidation":
					return "text-uppercase badge badge-danger text-white";
					break;
				case "served":
					return "label bg-success";
					break;
				case "rejected":
					return "label bg-danger";
					break;
				default:
					return "text-uppercase badge badge-primary text-white";
			}
		},
		siuteNum(user_id) {
			return 4000 + user_id;
		},
	},
};
</script>
