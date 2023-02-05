<template>
	<MainLayout>
		<div>
			<section>
				<div class="container">
					<div class="stock-subscription-form mb-4 border p-4">
						<h2
							class="font-bold text-xl text-gray-800 leading-tight form-title text-center mb-4"
						>
							Customs Declaration Form
						</h2>
						<template v-if="printable.includes(packag.status)">
							<h4 class="text-center text-white bg-danger p-2 m-4">
								<i class="fas fa-exclamation-circle mr-1"></i> Customs
								declaration form already filled and package has been processed.
								Changes will not be saved.
							</h4>
						</template>

						<form @submit.prevent="submit" enctype="multipart/form-data">
							<input type="hidden" name="packag_id" v-model="packag_id" />
							<div class="packag-form">
								<breeze-validation-errors class="mb-4" />
								<flash-messages class="mb-4" />

								<div class="row">
									<div class="col-md-6" style="border: 1px solid #6b7280">
										<h1><b>SHIPPED FROM :</b>&nbsp;{{ warehouse.name }}</h1>
										<div class="mb-2">
											<b>Contact Name:</b> {{ warehouse.contact_person }} <br />
											<b>Phone:</b> {{ warehouse.phone }} <br />
											<b>Email:</b> {{ warehouse.email }} <br />
											<b>Company/Address:</b> {{ warehouse.address }},
											{{ warehouse.city }},{{ warehouse.state }},{{
												warehouse.zip
											}}
											<br />
											<b>Country:</b> {{ warehouse.country ?? "USA" }} <br />
											<b>Incoterms</b> DDU/DAP
										</div>
									</div>

									<div class="col-md-6" style="border: 1px solid #6b7280">
										<p><b>Tracking Number:</b></p>
										<br />
										<p><b>Date:</b> {{ package_date }}</p>
										<p><b>Package ID:</b> PKG #{{ packag.id }}</p>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6" style="border: 1px solid #6b7280">
										<h1><b>SHIPPED TO :</b></h1>
										<div class="mb-2">
											<b>Contact Name:</b>&nbsp;{{ packag.address.fullname
											}}<br />
											<b>Phone:</b>&nbsp;{{ packag.address.phone }}<br />
											<b>Address:</b>&nbsp;{{ packag.address.address }}<br />
											<b>City:</b>&nbsp;{{ packag.address.city }}<br />
											<b>State:</b>&nbsp;{{ packag.address.state }}<br />
											<b>Zip code:</b>&nbsp;{{ packag.address.zip_code }}<br />
											<b>Country:</b>&nbsp;{{ packag.address.country.name }}
										</div>
									</div>

									<div class="col-md-6" style="border: 1px solid #6b7280">
										<p><b>SOLD TO :</b> Same as Shipped</p>
									</div>
								</div>

								<div class="row">
									<fieldset class="mt-4 mb-2 border p-4">
										<div class="col-md-12">
											<div
												v-for="(item, index) in form.package_items"
												:key="item.id"
												class="row"
											>
												<div class="col-md-3">
													<div class="form-group">
														<label for=""><b>Description *</b></label>
														<input
															v-model="item.description"
															name="description"
															id="description"
															type="text"
															class="form-control"
															placeholder="Description"
															required
														/>
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														<label for=""><b>HS Code</b></label>
														<input
															v-model="item.hs_code"
															name="hs_code"
															id="hs_code"
															type="text"
															class="form-control"
															placeholder="HS Code"
														/>
													</div>
												</div>

												<div class="col-md-1">
													<div class="form-group">
														<label for=""><b>Qty *</b></label>
														<input
															v-model="item.quantity"
															@keyup="calculateShippingTotal"
															v-on:change="calculateShippingTotal"
															name="quantity"
															min="1"
															step="1"
															id="quantity"
															type="number"
															class="form-control"
															placeholder="Qty"
															required
														/>
													</div>
												</div>

												<div class="col-md-1">
													<div class="form-group">
														<label for=""><b>Price *</b></label>
														<input
															v-model="item.price"
															@keyup="calculateShippingTotal"
															v-on:change="calculateShippingTotal"
															min="1"
															type="number"
															step="0.01"
															class="form-control"
															placeholder="Price"
															required
														/>
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														<label for=""><b>Origin Country *</b></label>
														<select
															name="origin_country"
															class="form-select"
															v-model="item.origin_country"
															required
														>
															<option selected value="">Select</option>
															<template
																v-for="country in countries"
																:key="country.id"
															>
																<option :value="country.id">
																	{{ country.name }}
																</option>
															</template>
														</select>
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														<label for=""><b>Batteries *</b></label>
														<select
															name="batteries"
															class="form-select"
															v-model="item.batteries"
															required
														>
															<option selected value="">Select</option>
															<option value="0">No Battery</option>
															<option value="1">
																Simple Batteries (Shipped on on Fedex)
															</option>
															<option value="2">
																Batteries Packaed with Equipment
															</option>
															<option value="3">
																Batteries Contained in Equipment
															</option>
														</select>
													</div>
												</div>

												<div class="col-md-1" v-show="index != 0">
													<div class="form-group">
														<label for=""></label>
														<a
															v-on:click="removeItem(index)"
															class="btn btn-primary"
														>
															<span>Delete</span>
														</a>
													</div>
												</div>
											</div>

											<div class="row">
												<div
													class="col-md-1 offset-md-4"
													style="padding-right: 0px"
												>
													<div class="form-group">
														<span class="text-lg"><b>Total:</b></span>
													</div>
												</div>

												<div class="col-md-2" style="padding-right: 0px">
													<div class="form-group">
														<input
															v-model="form.shipping_total"
															readonly
															name="shipping_total"
															id="shipping_total"
															type="text"
															class="form-control"
															required
														/>
													</div>
												</div>

												<div
													class="col-md-2 offset-md-3"
													style="padding-right: 0px"
												>
													<a
														v-on:click="addItem"
														class="btn btn-primary"
														style="float: right"
													>
														<span>Add Item </span>
													</a>
												</div>
											</div>

											<div class="row">
												<div class="form-group">
													<breeze-label value="Package Type" />
													<label>
														<input
															v-model="form.package_type"
															type="radio"
															id="merchandise"
															value="merchandise"
														/>
														Merchandise
													</label>
													<br />
													<label>
														<input
															v-model="form.package_type"
															type="radio"
															id="gift"
															value="gift"
														/>
														Gift
													</label>
													<br />
													<label>
														<input
															v-model="form.package_type"
															type="radio"
															id="sample"
															value="sample"
														/>
														Sample
													</label>
												</div>
											</div>
										</div>
									</fieldset>
								</div>
								<div class="row">
									<div class="col-md-12 form-group">
										<template v-if="packag.status == 'open'">
											<button type="submit" class="btn btn-success float-right">
												Save & Submit
											</button>
										</template>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</section>
		</div>
	</MainLayout>
</template>

<script>
	import MainLayout from "@/Layouts/Main";
	import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
	import BreezeLabel from "@/Components/Label";

	export default {
		components: {
			BreezeAuthenticatedLayout,
			MainLayout,
			BreezeLabel,
		},
		data() {
			return {
				form: this.$inertia.form({
					package_id: this.packag.id,
					address_book_id: this.address_book_id,
					package_items: this.package_items,
					shipping_total: this.packag.shipping_total,
					package_type: this.packag.package_type,
				}),
				current_address: this.selected_address,
				printable: ["filled", "labeled", "shipped", "delivered"],
			};
		},
		props: {
			auth: Object,
			countries: Object,
			address_book_id: Number,
			package_items: Object,
			address_book: Object,
			selected_address: String,
			packag: Object,
			warehouse: Object,
			tracking_numbers: Object,
			package_date: String,
		},
		methods: {
			submit() {
				if (this.printable.includes(this.packag.status)) {
					alert(
						"Customs declaration form already filled and package has been processed. Changes will not be saved."
					);
					return false;
				}
				this.form.post(this.route("package.store"));
			},
			addItem() {
				this.form.package_items.push({
					hs_code: "",
					description: "",
					quantity: 1,
					price: 0,
					origin_country: "",
					batteries: "",
				});
			},
			removeItem(index) {
				this.form.package_items.splice(index, 1);
			},
			storePhoto() {
				if (this.$refs.photo) {
					this.form.post_image = this.$refs.photo.files[0];
				}
				this.form.post(route("photo.store"), {
					preserveScroll: true,
				});
			},
			getTotalValue() {
				return this.package_items.reduce(function (a, c) {
					return a + Number(c.quantity * c.value || 0);
				}, 0);
			},
			selectAddress(event) {
				var address = this.address_book[event.target.value];
				this.current_address = address.full_address;
				console.log("target value " + event.target.value);
				console.log("old value " + this.form.address_book_id);
				this.form.address_book_id = event.target.value;
				console.log("new value " + this.form.address_book_id);
			},
			calculateShippingTotal() {
				let final_amount = 0;
				this.form.package_items.forEach((pkg_item, index) => {
					final_amount =
						final_amount +
						parseFloat(pkg_item.price) * parseInt(pkg_item.quantity);
				});

				if (final_amount > 0) {
					this.form.shipping_total = parseFloat(final_amount).toFixed(2);
				} else {
					this.form.shipping_total = "";
				}
			},
		},
		mounted() {
			this.addItem();
		},
		// computed: {
		// 	shipping_total() {
		// 		return this.form.package_items.reduce((acc, item) => {
		// 			var res = acc + parseFloat(item.unit_price) * parseInt(item.quantity);
		// 			if (res > 0) {
		// 				return parseFloat(res).toFixed(2);
		// 			} else {
		// 				return "";
		// 			}
		// 		}, 0);
		// 	},
		// },
	};
</script>
