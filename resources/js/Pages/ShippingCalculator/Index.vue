<template>
	<MainLayout>
		<div class="col">
			<h1 class="text-center font-bold text-xl">Shipping Calculator</h1>
		</div>

		<section
			class="section price-section-padd section-height-3 bg-light border-0 pt-4 m-0"
			style="background-size: 100%; background-repeat: no-repeat"
		>
			<div class="container">
				<div class="row mx-3 mx-xl-0">
					<div class="col-md-12 px-0">
						<div class="bg-grey-soft h-100">
							<div class="text-center text-md-start p-5 h-100">
								<form
									@submit.prevent="submit"
									class="contact-form form-style-4 form-placeholders-light form-errors-light mb-5 mb-lg-0"
								>
									<div class="row">
										<div class="col-md-5">
											<div class="form-group">
												<div
													class="input-title text-dark mb-2 text-6 font-weight-medium text-center"
												>
													Where is your merchandise?
												</div>
												<select
													required
													v-model="form.ship_from_postal_code"
													class="form-select text-4"
												>
													<option value="">Select</option>
													<option
														v-for="warehouse in warehouses"
														:value="warehouse.zip"
														:key="warehouse.id"
													>
														{{ warehouse.name }}
													</option>
												</select>
											</div>
										</div>

										<div class="col-md-5">
											<div class="form-group md-mrgn">
												<div
													class="input-title text-dark mb-2 text-6 font-weight-medium text-center"
												>
													Where are you shipping to?
												</div>
												<select
													required
													v-model="form.ship_to_country_code"
													class="form-select text-4"
												>
													<option value="">Select</option>
													<option
														v-for="country in countries"
														:value="country.iso"
														:key="country.id"
													>
														{{ country.name }}
													</option>
												</select>
											</div>
										</div>

										<div class="col-md-2">
											<div
												class="form-group sizes-input mt-5"
												style="margin-top: 0px !important"
											>
												<label
													class="text-6 text-center text-dark font-weight-medium d-block"
													for="weight"
													>Zip</label
												>
												<input
													v-model="form.ship_to_postal_code"
													type="text"
													class="form-control text-dark text-4 mt-2"
												/>
											</div>
										</div>

										<div class="form-heading text-center mt-2">
											<h6 class="text-6 text-warning font-weight-bold">
												Tell us more about your shipment.
											</h6>
										</div>
									</div>
									<div
										class="row"
										v-for="(item, index) in form.dimensions"
										:key="item.id"
									>
										<div class="col-md-3" v-show="index == 0">
											<div
												class="form-group sizes-input mt-3"
												v-if="index == 0"
											>
												<label
													class="text-6 text-center text-dark font-weight-medium d-block"
													for="weight"
													>Unit</label
												>
												<select
													v-model="form.units"
													class="form-select text-4 mt-2"
													aria-label="Default select example"
												>
													<option value="LB_IN">Lb / Inch</option>
													<option value="KG_CM">Kg / Cm</option>
												</select>
											</div>
										</div>
										<div
											class="col-md-3"
											:class="index != 0 ? 'offset-md-3' : ''"
										>
											<div class="form-group sizes-input mt-3">
												<label
													class="text-6 text-center text-dark font-weight-medium d-block"
													for="weight"
													>Weight</label
												>
												<input
													v-model="item.weight"
													type="number"
													class="form-control text-dark text-4 mt-2"
													name="name"
													:step="0.01"
													:min="1"
													required=""
												/>
											</div>
										</div>

										<div class="col-md-2">
											<div class="form-group sizes-input mt-3">
												<label
													class="text-6 text-center text-dark font-weight-medium d-block"
													for="weight"
													>Length</label
												>
												<input
													v-model="item.length"
													type="number"
													class="form-control text-dark text-4 mt-2"
													name="name"
													:step="0.01"
													:min="1"
													required=""
												/>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group sizes-input mt-3">
												<label
													class="text-6 text-center text-dark font-weight-medium d-block"
													for="weight"
													>Width</label
												>
												<input
													v-model="item.width"
													type="number"
													class="form-control text-dark text-4 mt-2"
													name="name"
													:step="0.01"
													:min="1"
													required=""
												/>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group sizes-input mt-3">
												<label
													class="text-6 text-center text-dark font-weight-medium d-block"
													for="height"
													>Height</label
												>
												<input
													v-model="item.height"
													type="number"
													class="form-control text-dark text-4 mt-2"
													name="name"
													:step="0.01"
													:min="1"
													required=""
												/>
											</div>
										</div>
									</div>

									<div class="">
										<button
											class="btn btn-link mt-2 mb-2 font-bold"
											v-on:click="add_dimension()"
										>
											+ ADD ANOTHER PACKAGE
										</button>
									</div>

									<div class="row">
										<div class="form-group col text-center mt-4">
											<button
												type="submit"
												class="btn btn-primary custom-btn-style-1 font-weight-normal btn-px-4 btn-py-2 text-5-5"
												data-loading-text="Loading..."
												data-cursor-effect-hover="plus"
												data-cursor-effect-hover-color="light"
												ref="buttonRates"
											>
												<span>Get Shipping Rates</span>
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>

					<div class="col-md-12 mt-2">
						<div class="card" v-for="rate in shipping_rates" :key="rate.id">
							<div class="card-body">
								<div class="row">
									<div class="col-md-4">
										<img
											src="https://app.shippingxps.com/theme/img/demos/business-consulting-3/partner-03.png"
											style="width: 100px"
										/>
									</div>
									<div class="col-md-4">
										<h6 class="text-5-5 font-weight-medium mb-0">
											{{ rate.name }}
										</h6>
									</div>
									<div class="col-md-4">
										<h6 class="text-5-5 font-weight-medium mb-0 float-right">
											${{ rate.price }}
										</h6>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</MainLayout>
	<LoaderComponent v-if="loader"></LoaderComponent>
</template>

<script>
	import LoaderComponent from "@/Components/LoaderComponent.vue";
	import MainLayout from "@/Layouts/Main";

	export default {
		components: {
			MainLayout,
			LoaderComponent,
		},
		props: {
			auth: Object,
			warehouses: Object,
			countries: Object,
		},
		data() {
			return {
				loader: false,
				shipping_rates: [],
				form: this.$inertia.form({
					ship_from_postal_code: "",
					ship_from_country_code: "US",
					ship_to_postal_code: "",
					ship_to_country_code: "",
					units: "LB_IN",
					dimensions: [
						{
							length: "",
							width: "",
							weight: "",
							height: "",
						},
					],
				}),
			};
		},
		methods: {
			submit() {
				this.loader = true;
				axios
					.post(route("shipping-rates.index"), this.form)
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
			add_dimension() {
				this.form.dimensions.push({
					weight: "",
					length: "",
					width: "",
					height: "",
				});
			},
		},
		mounted() {
			//
		},
	};
</script>

<style>
	.center {
		display: block;
		margin-left: auto;
		margin-right: auto;
	}
	.bg-grey-soft {
		background: #fff8ee;
	}
	.bg-yellow {
		background: #f1b523;
	}
	section.section {
		background: #ffff;
	}
	.contact-form .form-select {
		padding: 0.8rem 2.25rem 0.8rem 0.75rem;
	}
	.contact-form .form-heading h6 {
		letter-spacing: 0.1px;
	}
	.contact-form .sizes-input .form-control {
		background-color: #fff;
		background-clip: initial;
		border: none !important;
		padding: 0.6rem 0.75rem;
	}
	.contact-form .sizes-input .form-select {
		border: none !important;
	}
	.contact-form .dim-warning a:hover {
		text-decoration: none;
	}
	.contact-form .dim-warning .fa {
		color: #999999;
	}
	.page-header.page-header-modern.page-header-lg {
		padding: 15px 0 !important;
	}
</style>
