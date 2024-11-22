<template>
	<div class="row" v-if="packag.payment_status == 'Pending' && !packag.carrier_code">
		<div class="col-md-12">
			<div class="card mt-2">
				<div class="card-header">
					<h3 class="text-uppercase">Shipping Address</h3>
				</div>
				<div class="card-body">
					<div class="row" v-if="packag.address">
						<div class="col-md-6">
							<p class="mb-2">
								<button type="button" class="btn btn-primary" @click="edit()">
									<i class="fa fa-edit mr-1"></i>Edit Address</button>
							</p>

							<p>{{ packag?.address?.fullname }} </p>
							<p>{{ packag?.address?.address }} </p>
							<p>{{ packag?.address?.address_2 }} </p>
							<p>{{ packag?.address?.address_3 }} </p>
							<p>
								{{ packag?.address?.city }},
								{{ packag?.address?.state }},
								{{ packag?.address?.zip_code }},
								{{ packag?.address?.country_name }}
							</p>
							<p>
								Contact: {{ packag?.address?.phone }} <br>
								Email: {{ packag?.address?.email }} <br>
								<template v-if="packag?.address?.tax_no">
									VAT ID: {{ packag?.address?.tax_no }} <br>
								</template>
								Type: {{ packag?.address?.is_residential == 1 ? 'Residential' :
									'Commercial'
								}} <br />
							</p>
						</div>
					</div>
					<div class="row" v-else>
						<button type="button" class="btn btn-primary" @click="open()">
							<i class="fa fa-plus mr-1"></i>Add Address</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="shipping_address_modal"  v-if="packag.payment_status == 'Pending' && !packag.carrier_code">
		<div class="modal-dialog border modal-dialog-scrollable">
			<form @submit.prevent="submitShippingAddress()">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="">Edit - Shipping Address</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"
							@click="close()"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<breeze-validation-errors class="mb-4" />
						<div class="row" v-if="shipping_address.length > 0">
							<div class="col-md-12">
								<div class="form-group">
									<select name="address_book_id" class="form-select text-uppercase"
										v-model="address_form.address_book_id" @change="getAddressByID()">
										<!-- <option :value="0">--Select Address--</option> -->
										<template v-for="address in shipping_address" :key="address.id">
											<option :value="address.id">
												{{ address.fullname }}, {{ address.address }}
											</option>
										</template>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-7">
								<breeze-label for="name" value="Full Name *" />
								<input type="text" class="form-control" v-model="address_form.fullname" required />
							</div>

							<div class="form-group col-md-5">
								<breeze-label for="Business/Residential" value="Business/Residential *" />
								<select class="form-control" v-model="address_form.is_residential
									" required>
									<option value="" selected>Select</option>
									<option value="0">Business</option>
									<option value="1">Residential</option>
								</select>
							</div>

							<div class="form-group col-md-12">
								<breeze-label for="address" value="Address *" />
								<input type="text" class="form-control" v-model="address_form.address" required />
							</div>

							<div class="form-group col-md-12">
								<breeze-label for="address2" value="Address 2" />
								<input type="text" class="form-control" v-model="address_form.address_2" />
							</div>

							<div class="form-group col-md-12">
								<breeze-label for="address3" value="Address 3" />
								<input type="text" class="form-control" v-model="address_form.address_3" />
							</div>

							<div class="form-group col-md-6">
								<breeze-label for="country" value="Country *" />
								<select required v-model="address_form.country_id" class="form-control"
									v-on:change="country()">
									<option value="" selected>Select</option>
									<template v-for="country in countries" :key="country">
										<option :value="country.id">
											{{ country.name }}
										</option>
									</template>
								</select>
							</div>

							<div class="form-group col-md-6">
								<breeze-label for="state" :value="address_form.state_required
									? 'State *'
									: 'State (optional)'
									" />
								<input type="text" class="form-control" placeholder="e.g NY,CA,DE"
									v-model="address_form.state" />
							</div>

							<div class="form-group col-md-6">
								<breeze-label for="city" value="City *" />
								<input type="text" class="form-control" v-model="address_form.city" required />
							</div>

							<div class="form-group col-md-6">
								<breeze-label for="zip_code" value="Zip Code *" />
								<input type="text" class="form-control" v-model="address_form.zip_code" required />
							</div>

							<div class="form-group col-md-6">
								<breeze-label for="tax_no" value="Tax ID" />
								<input name="tax_no" id="tax_no" type="text" class="form-control"
									placeholder="Tax/ VAT ID" v-model="address_form.tax_no" />
							</div>

							<div class="form-group col-md-6">
								<breeze-label for="phone" value="Phone *" />
								<input type="text" class="form-control" v-model="address_form.phone" required />
							</div>

							<div class="form-group col-md-6">
								<breeze-label for="email" value="Email *" />
								<input type="email" class="form-control" v-model="address_form.email" required />
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal"
							@click="close()">Cancel</button>
						<button type="submit" class="btn btn-success">Save</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import BreezeLabel from "@/Components/Label";
import BreezeValidationErrors from "@/Components/ValidationErrors";
import { Inertia } from "@inertiajs/inertia";
import $ from "jquery";
import axios from 'axios';

export default {
	name: "Shipping Address Component",
	props: {
		packag: Object,
		shipping_address: Object,
		countries: Object,
		signature_types: Array,
	},
	components: {
		BreezeAuthenticatedLayout,
		MainLayout,
		BreezeLabel,
		BreezeValidationErrors,
	},
	data() {
		return {
			address_form: this.$inertia.form({
				package_id: "",
				address_book_id: "",

				fullname: "",
				is_residential: "",
				address: "",
				address_2: "",
				address_3: "",
				city: "",
				state: "",
				zip_code: "",
				tax_no: "",
				country_id: "",
				phone: "",
				email: "",
				state_required: false,
				packages_address: true,
			}),
		};
	},
	methods: {
		submitShippingAddress() {
			this.address_form.package_id = this.packag.id;

			Inertia.post(route("address.store"), this.address_form, {
				onSuccess: (response) => {
					this.close();
				},
				onError: () => {
					// 
				},
			});
		},
		country() {
			if (
				this.address_form.country_id == 226 ||
				this.address_form.country_id == 138 ||
				this.address_form.country_id == 38
			) {
				this.address_form.state_required = true;
			} else {
				this.address_form.state_required = false;
			}
		},
		mounted() {
			this.country();
		},
		open() {
			var modal = document.getElementById("shipping_address_modal");
			modal.classList.add("show");
			$("#shipping_address_modal").show();
		},
		close() {
			document.getElementById("shipping_address_modal").style.display = "none";
		},
		getAddressByID() {
			axios.post(route("getAddressByID"), this.address_form)
				.then(response => {
					console.log(response.data)
					this.address_form = {
						address_book_id: response.data.id,
						fullname: response.data.fullname,
						address: response.data.address,
						address_2: response.data.address_2,
						address_3: response.data.address_3,
						city: response.data.city,
						state: response.data.state,
						zip_code: response.data.zip_code,
						tax_no: response.data.tax_no,
						country_id: response.data.country_id,
						phone: response.data.phone,
						email: response.data.email,
						is_residential: response.data.is_residential,
						state_required: response.data.state_required,
						packages_address: true,
					};
				})
				.catch(error => {
					console.error(error);
				});
		},
		edit() {
			this.address_form.address_book_id = this.packag.address_book_id;
			this.getAddressByID();
			this.open();
		}
	},
};
</script>
