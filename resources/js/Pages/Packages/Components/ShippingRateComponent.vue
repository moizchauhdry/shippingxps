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
		<div class="col-md-12" v-if="packag.service_code == null">
			<div class="card mt-2">
				<div class="card-header">
					<h3 class="text-uppercase">Shipping Rates</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3">
							<template v-if="service_request_pending_count == 0">
								<a class="btn btn-success" v-on:click="getShippingRates()"
									>Get Shipping Rates</a
								>
							</template>
							<template v-else>
								<span class="text-uppercase badge badge-danger p-1">
									<i class="fas fa-exclamation-circle mr-1"></i>There are some
									pending service request.
								</span>
							</template>
						</div>
						<div class="col-md-9">
							<table class="table table-sm table-striped">
								<thead>
									<tr>
										<th>Shipping Service</th>
										<th>Price</th>
										<th></th>
									</tr>
								</thead>
								<!-- <tbody>
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
								</tbody> -->
								<tbody>
									<template v-for="rate in shipping_rates" :key="rate.id">
										<tr>
											<td>
												<img
													src="https://app.shippingxps.com/theme/img/demos/business-consulting-3/partner-03.png"
													style="width: 75px"
												/>
												{{ rate.name }}
											</td>
											<td>${{ rate.total }}</td>
											<td>
												<a
													v-on:click="setShippingService(rate)"
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

	<loader-component v-if="loader"></loader-component>
</template>

<script>
	import LoaderComponent from "@/Components/LoaderComponent.vue";
	export default {
		components: { LoaderComponent },
		name: "Shipping Rate Component",
		props: {
			packag: Object,
			shipping_services: Object,
			service_request_pending_count: Object,
		},
		data() {
			return {
				loader: false,
				shipping_rates: [],
				ship_service_form: this.$inertia.form({
					package_id: this.packag.id,
					code: "",
					type: "",
					name: "",
					pkg_type: "",
					markup: "",
					total: "",
				}),
			};
		},
		methods: {
			getShippingRates() {
				this.loader = true;

				let pieces = [];
				this.packag.boxes.forEach(function (value, index) {
					let piece = {
						length: value.length.toString(),
						width: value.width.toString(),
						weight: value.weight.toString(),
						height: value.height.toString(),
					};
					pieces.push(piece);
				});

				let quote_params = {
					ship_from: this.packag.warehouse_id,
					ship_from_country_code: "US",
					ship_to_postal_code: this.packag.address.zip_code,
					ship_to_country_code: this.packag.address.country.iso,
					units: "LB_IN",
					dimensions: pieces,
				};

				axios
					.post(route("shipping-rates.index"), quote_params)
					.then((response) => {
						this.shipping_rates = response.data.data;
						this.loader = false;
					})
					.catch((error) => {
						this.loader = false;
					})
					.finally(() => {
						this.loader = false;
					});
			},
			setShippingService(rate) {
				var result = window.confirm(
					"After Confirming the shippment method you wont be able to change. Are you sure to confirm ?"
				);
				if (result) {
					this.ship_service_form.code = rate.code;
					this.ship_service_form.name = rate.name;
					this.ship_service_form.type = rate.type;
					this.ship_service_form.pkg_type = rate.pkg_type;
					this.ship_service_form.markup = rate.markup;
					this.ship_service_form.total = rate.total;

					this.ship_service_form.post(
						this.route("packages.set-shipping-service")
					);
				}
			},

			// getShippingRatesByOrders() {
			// 	this.showEstimatedPrice = false;
			// 	this.overlay = true;
			// 	let pieces = [];

			// 	this.packag.boxes.forEach(function (value, index) {
			// 		let piece = {
			// 			weight: value.weight.toString(),
			// 			length: value.length.toString(),
			// 			width: value.width.toString(),
			// 			height: value.height.toString(),
			// 			insuranceAmount: "0",
			// 			declaredValue: "1",
			// 		};
			// 		pieces.push(piece);
			// 	});

			// 	let quote_params = {
			// 		ship_from: this.packag.warehouse_id,
			// 		ship_to: this.packag.address.country_id,
			// 		weight: this.packag.package_weight,
			// 		unit: this.packag.weight_unit,
			// 		weight_unit: this.packag.weight_unit + "_" + this.packag.dim_unit,
			// 		pieces: pieces,
			// 		zipcode: this.packag.address.zip_code,
			// 		city: this.packag.address.city,
			// 		is_residential: this.packag.address.is_residential,
			// 	};
			// 	axios
			// 		.get(this.route("getServicesList"))
			// 		.then((response) => {
			// 			console.log(response.data.services);
			// 			response.data.services.forEach((ele, index) => {
			// 				console.log(ele);
			// 				quote_params.service = ele;
			// 				this.getRatesByOrders(quote_params);
			// 			});
			// 		})
			// 		.catch((error) => {
			// 			console.log(error);
			// 		});
			// },
			// getRates(quote_params) {
			// 	axios
			// 		.get("/getQuote", {
			// 			params: quote_params,
			// 		})
			// 		.then((response) => {
			// 			this.isLoading = false;
			// 			if (response.data.status) {
			// 				this.showEstimatedPrice = true;
			// 				this.shipping_services[response.data.service.service_id] =
			// 					response.data.service;
			// 			} else {
			// 				this.serverError = response.data.message;
			// 			}
			// 			if (
			// 				response.data.service.isReady !== undefined &&
			// 				response.data.service.isReady === true
			// 			) {
			// 				this.displayNoteShipping = false;
			// 			}
			// 		});
			// },
			// getRatesByOrders(quote_params) {
			// 	axios
			// 		.get("/getQuoteByOrders", {
			// 			params: quote_params,
			// 		})
			// 		.then((response) => {
			// 			this.overlay = false;
			// 			if (response.data.status) {
			// 				this.showEstimatedPrice = true;
			// 				this.shipping_services[response.data.service.service_id] =
			// 					response.data.service;
			// 			} else {
			// 				this.serverError = response.data.message;
			// 			}
			// 			if (
			// 				response.data.service.isReady !== undefined &&
			// 				response.data.service.isReady === true
			// 			) {
			// 				this.displayNoteShipping = false;
			// 			}
			// 		});
			// },
		},
	};
</script>
