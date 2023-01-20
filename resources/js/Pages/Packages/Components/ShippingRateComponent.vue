<template>
	<div
		class="row"
		v-if="
			$page.props.auth.user.type == 'customer' &&
			(packag.status == 'filled' ||
				packag.status == 'consolidated' ||
				packag.address_type == 'domestic') &&
			packag.pkg_dim_status == 'done'
		"
	>
		<div class="col-md-12">
			<div class="card mt-2">
				<div class="card-header">
					<h3 class="text-uppercase">Shipping Rates</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3">
							<a class="btn btn-success" v-on:click="getShippingRatesByOrders()"
								>Get Shipping Rates</a
							>
						</div>
						<div class="col-md-9">
							<table
								class="table table-sm table-striped"
								v-if="showEstimatedPrice"
							>
								<thead>
									<tr>
										<th>Shipping Services</th>
										<th>Price</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<template
										v-for="service in shipping_services"
										:key="service.service_id"
									>
										<tr v-if="service.isReady === true">
											<td>{{ service.serviceLabel }}</td>
											<td>{{ service.totalAmount }} {{ service.currency }}</td>
											<td>
												<a
													v-on:click="setShippingService(service)"
													class="btn btn-info"
													>Confirm</a
												>
											</td>
										</tr>
									</template>
									<tr>
										<td colspan="3">
											<p class="text-white bg-warning p-1">
												<b>Note:</b> Selected service cannot be changed. So make
												sure you choose correct service.
											</p>
											<p
												class="text-white bg-danger p-1"
												v-show="displayNoteShipping"
											>
												<b>Note:</b> Make sure your address is valid to get
												Shipping Service
											</p>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<loader-component v-if="overlay"></loader-component>
</template>

<script>
	import LoaderComponent from "@/Components/LoaderComponent.vue";
	export default {
		components: { LoaderComponent },
		name: "Shipping Rate Component",
		props: {
			packag: Object,
			shipping_services: Object,
		},
		data() {
			return {
				serverError: "",
				overlay: false,
				showEstimatedPrice: false,
				form_shipping_service: this.$inertia.form({
					package_id: this.packag.id,
					status: "labeled",
					service: null,
				}),
			};
		},
		methods: {
			// getShippingRates() {
			// 	this.overlay = true;
			// 	this.showEstimatedPrice = false;
			// 	this.isLoading = true;
			// 	let quote_params = {
			// 		ship_from: this.packag.warehouse_id,
			// 		ship_to: this.packag.address.country_id,
			// 		weight: this.packag.package_weight,
			// 		unit: this.packag.weight_unit,
			// 		weight_unit: this.packag.weight_unit + "_" + this.packag.dim_unit,
			// 		length: this.packag.package_length,
			// 		width: this.packag.package_width,
			// 		height: this.packag.package_height,
			// 		zipcode: this.packag.address.zip_code,
			// 		city: this.packag.address.city,
			// 		is_residential: this.packag.address.is_residential,
			// 	};
			// 	axios
			// 		.get(this.route("getServicesList"))
			// 		.then((response) => {
			// 			response.data.services.forEach((ele, index) => {
			// 				quote_params.service = ele;
			// 				this.getRates(quote_params);
			// 			});
			// 			this.overlay = false;
			// 		})
			// 		.catch((error) => {
			// 			this.overlay = false;
			// 		});
			// },
			getShippingRatesByOrders() {
				this.showEstimatedPrice = false;
				this.overlay = true;
				let pieces = [];

				this.packag.boxes.forEach(function (value, index) {
					let piece = {
						weight: value.weight.toString(),
						length: value.length.toString(),
						width: value.width.toString(),
						height: value.height.toString(),
						insuranceAmount: "0",
						declaredValue: "1",
					};
					pieces.push(piece);
				});

				let quote_params = {
					ship_from: this.packag.warehouse_id,
					ship_to: this.packag.address.country_id,
					weight: this.packag.package_weight,
					unit: this.packag.weight_unit,
					weight_unit: this.packag.weight_unit + "_" + this.packag.dim_unit,
					pieces: pieces,
					zipcode: this.packag.address.zip_code,
					city: this.packag.address.city,
					is_residential: this.packag.address.is_residential,
				};
				axios
					.get(this.route("getServicesList"))
					.then((response) => {
						console.log(response.data.services);
						response.data.services.forEach((ele, index) => {
							console.log(ele);
							quote_params.service = ele;
							this.getRatesByOrders(quote_params);
						});
					})
					.catch((error) => {
						console.log(error);
					});
			},
			getRates(quote_params) {
				axios
					.get("/getQuote", {
						params: quote_params,
					})
					.then((response) => {
						this.isLoading = false;
						if (response.data.status) {
							this.showEstimatedPrice = true;
							this.shipping_services[response.data.service.service_id] =
								response.data.service;
						} else {
							this.serverError = response.data.message;
						}
						if (
							response.data.service.isReady !== undefined &&
							response.data.service.isReady === true
						) {
							this.displayNoteShipping = false;
						}
					});
			},
			getRatesByOrders(quote_params) {
				axios
					.get("/getQuoteByOrders", {
						params: quote_params,
					})
					.then((response) => {
						this.overlay = false;
						if (response.data.status) {
							this.showEstimatedPrice = true;
							this.shipping_services[response.data.service.service_id] =
								response.data.service;
						} else {
							this.serverError = response.data.message;
						}
						if (
							response.data.service.isReady !== undefined &&
							response.data.service.isReady === true
						) {
							this.displayNoteShipping = false;
						}
					});
			},
			setShippingService(service) {
				var result = window.confirm(
					"After Confirming the shippment method you wont be able to change. Are you sure to confirm ?"
				);
				if (result) {
					this.form_shipping_service.service = service;
					this.form_shipping_service.post(
						this.route("packages.set-shipping-service")
					);
				}
			},
		},
	};
</script>
