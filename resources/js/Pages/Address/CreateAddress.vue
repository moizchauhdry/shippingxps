<template>
	<MainLayout>
		<div class="container">
			<div class="stock-subscription-form">
				<form @submit.prevent="submit">
					<div class="order-form">
						<div class="col-md-6 offset-md-3">
							<h1
								class="text-5 text-lg-5 text-xl-5 line-height-3 text-transform-none font-weight-semibold mb-4"
							>
								Create - Shipping Address
							</h1>

							<breeze-validation-errors class="mb-4" />

							<div class="row">
								<div class="form-group col-md-6">
									<breeze-label for="fullname" value="Full Name *" />
									<input
										name="fullname"
										id="fullname"
										type="text"
										class="form-control"
										placeholder="Full Name"
										v-model="form.fullname"
										required
									/>
								</div>

								<div class="form-group col-md-6">
									<breeze-label
										for="Business/Residential"
										value="Business/Residential *"
									/>
									<select class="form-control" v-model="form.is_residential">
										<option value="0">Business</option>
										<option value="1">Residential</option>
									</select>
								</div>

								<div class="form-group col-md-12">
									<breeze-label for="address" value="Address *" />
									<input
										type="text"
										class="form-control"
										placeholder="Address"
										v-model="form.address"
										required
									/>
								</div>

								<div class="form-group col-md-12">
									<breeze-label for="" value="Address 2 (optional)" />
									<input
										type="text"
										class="form-control"
										placeholder="Address 2"
										v-model="form.address_2"
									/>
								</div>

								<div class="form-group col-md-12">
									<breeze-label for="address" value="Address 3 (optional)" />
									<input
										type="text"
										class="form-control"
										placeholder="Address 3"
										v-model="form.address_3"
									/>
								</div>

								<div class="form-group col-md-6">
									<breeze-label for="country" value="Country *" />
									<select
										required
										v-model="form.country_id"
										class="form-control"
										v-on:change="country"
									>
										<option value="0">--Select Country--</option>
										<template v-for="country in countries" :key="country">
											<option :value="country.id">{{ country.name }}</option>
										</template>
									</select>
								</div>

								<div class="form-group col-md-6">
									<breeze-label
										for="state"
										:value="
											form.state_required ? 'State *' : 'State (optional)'
										"
									/>
									<input
										name="state"
										id="state"
										type="text"
										class="form-control"
										placeholder="State"
										v-model="form.state"
									/>
								</div>

								<div class="form-group col-md-6">
									<breeze-label for="city" value="City *" />
									<input
										name="city"
										id="city"
										type="text"
										class="form-control"
										placeholder="City"
										v-model="form.city"
										required
									/>
								</div>

								<div class="form-group col-md-6">
									<breeze-label for="zip_code" value="Zip Code *" />
									<input
										name="zip_code"
										id="zip_code"
										type="text"
										class="form-control"
										placeholder="Zip Code"
										v-model="form.zip_code"
										required
									/>
								</div>

								<div class="form-group col-md-6">
									<breeze-label for="phone" value="Phone *" />
									<input
										name="phone"
										id="phone"
										type="text"
										class="form-control"
										placeholder="Phone"
										v-model="form.phone"
										required
									/>
								</div>

								<div class="form-group col-md-6">
									<breeze-label for="email" value="Email *" />
									<input
										type="email"
										class="form-control"
										placeholder="Phone"
										v-model="form.email"
										required
									/>
								</div>
							</div>

							<div class="order-button">
								<input
									type="submit"
									value="Create Address"
									class="btn btn-danger"
								/>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</MainLayout>
</template>

<script>
	import MainLayout from "@/Layouts/Main";
	import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
	import BreezeLabel from "@/Components/Label";
	import BreezeValidationErrors from "@/Components/ValidationErrors";

	export default {
		components: {
			BreezeAuthenticatedLayout,
			MainLayout,
			BreezeLabel,
			BreezeValidationErrors,
		},
		data() {
			return {
				form: this.$inertia.form({
					fullname: "",
					country_id: 0,
					state: "",
					city: "",
					phone: "",
					email: "",
					address: "",
					address_2: "",
					address_3: "",
					zip_code: "",
					is_residential: "1",
					state_required: false,
				}),
			};
		},
		props: {
			auth: Object,
			countries: Object,
		},
		methods: {
			submit() {
				this.form.post(this.route("address.store"));
			},
			country() {
				if (
					this.form.country_id == 226 ||
					this.form.country_id == 138 ||
					this.form.country_id == 38
				) {
					this.form.state_required = true;
				} else {
					this.form.state_required = false;
				}
			},
		},
	};
</script>
