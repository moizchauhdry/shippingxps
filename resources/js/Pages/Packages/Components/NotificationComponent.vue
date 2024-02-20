<template>
	<div class="row">
		<div class="col-md-12">
			<div class="text-center m-3" v-if="package_expired">
				<span class="text-uppercase badge badge-danger p-1 text-lg">
					<i class="fas fa-exclamation-circle mr-1"></i>The package has exceeded 80 days, so it has been
					terminated and has become the property of ShippingXPS.
				</span>
			</div>
			<div class="text-center" v-else>
				<div v-if="packag.admin_status == 'pending'">
					<span class="text-uppercase badge badge-danger p-1">
						<i class="fas fa-exclamation-circle mr-1"></i>The package is not approved by ShippingXPS.
					</span>
				</div>
				<div v-if="packag.pkg_type == 'consolidation' && packag.pkg_dim_status == 'pending'">
					<span class="text-uppercase badge badge-danger p-1">
						<i class="fas fa-exclamation-circle mr-1"></i>The dimensions for consolidation package is not added
						by ShippingXPS.
					</span>
				</div>
				<div v-if="!packag.address_book_id">
					<span class="text-uppercase badge badge-danger p-1">
						<i class="fas fa-exclamation-circle mr-1"></i>The shipping address for this package is not added.
					</span>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
export default {
	name: "Package Notification",
	props: {
		packag: Object,
		service_request_pending_count: Object,
		package_expired: Boolean,
	},
	data() {
		return {
			//
		};
	},
	methods: {
		//
	},
};
</script>
