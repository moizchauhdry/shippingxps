<template>
	<div class="table-responsive">
		<table class="table table-striped table-bordered text-center text-sm">
			<thead>
				<tr>
					<th scope="col">SR #</th>
					<th scope="col">Package</th>
					<th scope="col">Status</th>
					<th scope="col">Customer</th>
					<th scope="col">Created Date</th>
					<th scope="col"></th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(pkg, index) in pkgs.data" :key="pkg.id">
					<td>{{ ++index }}</td>
					<td>
						<span class="badge badge-primary text-sm">PKG #{{ pkg.id }}</span>
						<p>
							Location: <strong>{{ pkg?.warehouse?.name }}</strong>
						</p>
						<p v-if="pkg.package_length > 0">
							Dimentions : {{ pkg.package_length }} {{ pkg.dim_unit }} x
							{{ pkg.package_width }} {{ pkg.dim_unit }} x
							{{ pkg.package_height }}
							{{ pkg.dim_unit }}
						</p>
						<p>
							Shipped :
							<strong>{{ pkg.status == "shipped" ? "Yes" : "No" }}</strong>
						</p>
						<template
							v-for="child_pkg in pkg.child_packages"
							:key="child_pkg.id"
						>
							<span
								class="badge badge-info p-1 mr-1 mb-1"
								v-if="child_pkg.id != pkg.id"
								>PKG #{{ child_pkg.id }}</span
							>
						</template>
					</td>
					<td>
						<span v-bind:class="getLabelClass(pkg.status)">{{
							pkg.status
						}}</span>
						<br />
						<span
							class="badge badge-warning text-uppercase"
							v-if="
								pkg.pkg_type == 'consolidation' || pkg.pkg_type == 'multipiece'
							"
						>
							{{ pkg.pkg_type }}</span
						>
					</td>
					<td>
						<inertia-link
							:href="route('detail-customer', pkg?.customer?.id)"
							class="btn btn-link"
						>
							# {{ siuteNum(pkg?.customer?.id) }} - {{ pkg?.customer?.name }}
						</inertia-link>
					</td>
					<td>{{ pkg.created_at }}</td>
					<td>
						<template v-if="pkg.pkg_type != 'assigned'">
							<inertia-link
								class="btn btn-info btn-sm m-1"
								:href="route('packages.show', pkg.id)"
							>
								<i class="fa fa-list mr-1"></i>Detail</inertia-link
							>
							<template v-if="pkg.status != 'open'">
								<a
									class="btn btn-warning btn-sm m-1"
									:href="route('packages.pdf', pkg.id)"
									target="_blank"
								>
									<i class="fa fa-print mr-1"></i>Print Commercial Invoice</a
								>
							</template>
						</template>
						<template v-else>
							<span class="badge badge-danger"
								>This package is assigned to PKG #{{
									pkg.package_handler_id
								}}</span
							>
						</template>
					</td>
				</tr>
				<tr v-if="pkgs.data.length == 0">
					<td class="text-primary text-center" colspan="9">
						There are no packages found.
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<pagination class="mt-6" :links="pkgs.links"></pagination>
</template>

<script>
	import Pagination from "@/Components/Pagination.vue";
	export default {
		components: { Pagination },
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
						return "text-uppercase badge badge-warning p-1 text-white";
						break;
					case "open":
						return "text-uppercase badge badge-info p-1 text-white";
						break;
					case "filled":
						return "text-uppercase badge badge-info p-1 text-white";
						break;
					case "open":
						return "text-uppercase badge badge-success p-1 text-white";
						break;
					case "labeled":
						return "text-uppercase badge badge-success p-1 text-white";
						break;
					case "shipped":
						return "text-uppercase badge badge-primary p-1";
						break;
					case "delivered":
						return "text-uppercase badge badge-success p-1 text-white";
						break;
					case "consolidation":
						return "text-uppercase badge badge-danger p-1 text-white";
						break;
					case "served":
						return "label bg-success";
						break;
					case "rejected":
						return "label bg-danger";
						break;
					default:
						return "text-uppercase badge badge-primary p-1 text-white";
				}
			},
			siuteNum(user_id) {
				return 4000 + user_id;
			},
		},
	};
</script>
