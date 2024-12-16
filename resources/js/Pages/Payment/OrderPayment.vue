<template>
	<MainLayout>
		<div class="container">
			<div class="card" style="margin-top: 0px">
				<BreezeValidationErrors />
				<div class="card-body">
					<div class="container">
						<!-- AUTHORIZE.NET PAYMENT -->
						<form @submit.prevent="submit" enctype="multipart/form-data">
							<div class="row">
								<div class="col-md-8 offset-md-2">
									<div class="bg-dark border-3 border-warning container mb-3 text-center text-white">
										<h2 style="color: #fff; font-weight: bold; font-size: 25px">
											Checkout
										</h2>
									</div>

									<div v-show="response_message != null" class="alert alert-danger">
										{{ response_message }}
									</div>

									<h3>Select Billing Address to proceed</h3>
									<select class="form-control custom-select"
										v-model="square_payment_form.billing_address_id" required>
										<option value="">Select Address</option>
										<template v-for="address in billing_addresses" :key="address.id">
											<option :value="address.id">{{ address.label }}</option>
										</template>
									</select>

									<hr class="mb-3 mt-3" />

									<div class="row">
										<!-- <div class="col-12" v-if="status != undefined">
											<p style="color: red">{{ status.message[0].text }}</p>
										</div> -->

										<!-- <div class="row">
											<div class="form-group col-12">
												<breeze-label value="Card Number *" />
												<input v-model="form.card_no" class="form-control" type="number"
													:maxlength="card_max" pattern="[0-9]*" name="card_no" placeholder=""
													required />
											</div>
											<div class="form-group col-4">
												<breeze-label for="month" value="Expiry Month *" />
												<select v-model="form.month" class="form-control" name="month" required>
													<option value="">Select</option>
													<option v-for="n in 12" :value="n" :key="n">
														{{ n }}
													</option>
												</select>
											</div>
											<div class="form-group col-4">
												<breeze-label value="Expiry Year *" />
												<select v-model="form.year" class="form-control" name="year" required>
													<option value="">Year</option>
													<option :value="new Date().getFullYear()">
														{{ new Date().getFullYear() }}
													</option>
													<option v-for="n in 17" :value="n + new Date().getFullYear()" :key="n">
														{{ n + new Date().getFullYear() }}
													</option>
												</select>
											</div>
											<div class="form-group col-4">
												<breeze-label value="CVV *" />
												<input v-model="form.cvv" class="form-control" type="text" placeholder="123"
													required />
											</div>
										</div> -->

										<!-- <div class="row">
											<div class="col-md-12">
												<h1 class="text-lg"><b>Billing Address</b></h1>
											</div>
											<div class="form-group col-md-6">
												<breeze-label value="First Name*" />
												<input v-model="form.first_name" class="form-control" type="text"
													maxlength="55" name="first_name" placeholder="" required />
											</div>
											<div class="form-group col-md-6">
												<breeze-label value="Last Name*" />
												<input v-model="form.last_name" class="form-control" type="text"
													maxlength="55" name="last_name" placeholder="" required />
											</div>
											<div class="form-group col-12">
												<breeze-label value="Address*" />
												<input v-model="form.address" class="form-control" type="text"
													maxlength="55" name="address" placeholder="" required />
											</div>
											<div class="form-group col-md-6">
												<breeze-label value="City*" />
												<input v-model="form.city" class="form-control" type="text" maxlength="55"
													name="city" placeholder="" required />
											</div>
											<div class="form-group col-md-6">
												<breeze-label value="State*" />
												<input v-model="form.state" class="form-control" type="text" maxlength="55"
													name="state" placeholder="" required />
											</div>
											<div class="form-group col-md-6">
												<breeze-label value="Country *" />
												<input v-model="form.country" class="form-control" type="text"
													maxlength="55" name="country" placeholder="" required />
											</div>
											<div class="form-group col-md-6">
												<breeze-label value="Postcode*" />
												<input v-model="form.zip" class="form-control" type="text" maxlength="55"
													name="zip" placeholder="" required />
											</div>
											<div class="form-group col-md-6">
												<breeze-label value="Mobile*" />
												<input v-model="form.phone_no" class="form-control" type="number"
													maxlength="14" name="phone_no" placeholder="" required />
											</div>
											<div class="form-group col-md-6">
												<breeze-label value="Email*" />
												<input v-model="form.email" class="form-control" type="email" name="email"
													placeholder="" required />
											</div>
										</div> -->

										<!-- <table class="table" cols="4">
											<tbody>
												<tr>
													<th class="w-75 text-end">
														Total:
														<span class="text-lg ml-2">${{ amount }}</span>
													</th>
												</tr>
											</tbody>
										</table>
										<div class="form-group col-12" style="font-size: 12px">
											<input type="checkbox" id="terms" required />
											<label for="terms">&nbsp; I confirm that I have read and understood the
												&nbsp;</label>
											<inertia-link :href="route('page-show', 'terms_and_conditions')"
												class="link-hover-style-1">Terms and Conditions.</inertia-link> <br>
											<input type="checkbox" id="signature" required />
											<label for="terms">&nbsp; I possess an electronic signature confirming both the
												initiation and authorization of the payment, which was made by me.</label>
										</div>


										<div class="form-group col-12">
											<button type="submit" v-on:submit="submit()" class="btn btn-primary w-100">
												<span class="text-lg">Pay ${{ amount }}</span>
											</button>
										</div> -->

										<!-- <div class="container">
											<span class="card p-4 mb-4"
												style="color: red ; font-size: 15px; border: 3px solid red;">
												<b style="font-size: 14px;">Note: PayPal is the only available option.
													Please use PayPal for smooth and fast payments.</b>
											</span>
										</div> -->
									</div>
								</div>
							</div>
						</form>


						<!-- SQUARE START -->
						<form id="payment-form">
							<div class="row">
								<div class="col-md-8 offset-md-2">
									<h1 class="text-uppercase"><b>Debit or Credit Card</b></h1>
									<div id="card-container"></div>
									<button id="card-button" type="button" class="btn btn-primary w-100"
										:class="{ 'opacity-25': square_payment_form.processing }"
										:disabled="square_payment_form.processing">
										<span class="text-lg" v-if="square_payment_form.processing">
											Loading ... Please wait.</span>
										<span class="text-lg mr-1" v-else>Pay ${{ amount }}</span>

									</button>
									<div id="payment-status-container"></div>
								</div>
							</div>
						</form>
						<!-- SQUARE END -->

						<hr class="mb-3 mt-3" />

						<!-- PAYPAL PAYMENT -->
						<div class="row">
							<div class="col-md-8 offset-md-2">
								<div class="text-center">
									<h4 class="p-2"><strong>OR</strong></h4>
									<h5>
										<strong>Pay by PayPal with {{ paypal_processing_fee }}% processing
											Fee.</strong>
									</h5>
								</div>
								<div class="form-group mt-2 mb-2" style="font-size: 12px">
									<input type="checkbox" id="terms-paypal" required />
									<label for="terms">&nbsp; I confirm that I have read and understood the
										&nbsp;</label><inertia-link :href="route('page-show', 'terms_and_conditions')"
										class="link-hover-style-1">Terms
										and Conditions.</inertia-link> <br>
									<input type="checkbox" id="signature-paypal" required />
									<label for="terms">&nbsp; I possess an electronic signature confirming both the
										initiation and authorization of the payment, which was made by me.</label>
								</div>
								<button class="btn btn-info w-100" v-on:click="submitPayPal()">
									<span class="text-lg">Pay ${{ paypal_charged_amount }}</span>
								</button>
							</div>
							<div class="col-md-6 offset-md-3">
								<a href="javascript:void(0)" id="paymentSuccess" @click="paymentSuccess"
									class="hidden">pay
									Success</a>
								<div id="smart-button-container">
									<div style="text-align: center">
										<div id="paypal-button-container"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<input type="text" ref="transaction_id" id="transaction_id" class="hidden" @change="getValues()" />
		<textarea name="payment_detail" id="payment_detail" cols="30" rows="10" class="hidden"></textarea>

		<div v-if="overlay" class="overlay">
			<div class="overlay__inner">
				<div class="overlay__content"><span class="spinner"></span></div>
			</div>
		</div>
	</MainLayout>
</template>
<style scoped>
.overlay {
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	position: fixed;
	background: rgba(0, 0, 0, 0.5);
}

.overlay__inner {
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	position: absolute;
}

.overlay__content {
	left: 50%;
	position: absolute;
	top: 50%;
	transform: translate(-50%, -50%);
}

.spinner {
	width: 75px;
	height: 75px;
	display: inline-block;
	border-width: 2px;
	border-color: rgba(255, 255, 255, 0.05);
	border-top-color: #fff;
	animation: spin 1s infinite linear;
	border-radius: 100%;
	border-style: solid;
}

@keyframes spin {
	100% {
		transform: rotate(360deg);
	}
}
</style>

<script>
import MainLayout from "@/Layouts/Main";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import BreezeLabel from "@/Components/Label";
import BreezeValidationErrors from "@/Components/ValidationErrors";
import $ from "jquery";

export default {
	components: {
		BreezeAuthenticatedLayout,
		MainLayout,
		BreezeLabel,
		BreezeValidationErrors,
	},
	data() {
		return {
			isHidden: true,
			info: "",
			overlay: false,
			response: "",
			response_message: null,
			coupon_status: 2,
			coupon_message: "",
			card_max: 16,
			card_csv_max: 3,
			paypal_amount: 0,
			hasPackage: this.hasPackage,
			form: this.$inertia.form({
				payment_module_type: this.payment_module,
				payment_module_id: this.payment_module_id,
				amount: this.amount,
				first_name: "",
				last_name: "",
				country: "",
				city: "",
				state: "",
				zip: "",
				address: "",
				phone_no: "",
				email: "",
				card_no: "",
				month: "",
				year: "",
				cvv: "",
				coupon_code: "",
				coupon_code_id: "",
				discount: 0.0,
			}),
			square_payment_form: this.$inertia.form({
				payment_module: this.payment_module,
				payment_module_id: this.payment_module_id,
				payment_token: "",
				verification_token: "",
				billing_address_id: "",
			}),
			billing_addresses: [],
		};
	},
	props: {
		amount: Object,
		status: Object,
		payment_module: Object,
		payment_module_id: Object,
		billing_addresses: Object,
		paypal_processing_fee: Object,
		paypal_charged_amount: Object,
		square_application_id: String,
		square_location_id: String,
	},
	watch: {
		form: {
			handler(val) {
				console.log(val);
			},
			deep: true,
		},
	},
	mounted() {
		var formdata = this.form;
		this.coupon_message = "";
		this.coupon_status = 2;

		this.square();

	},
	methods: {
		async square() {
			const payments = Square.payments(this.square_application_id, this.square_location_id);
			const card = await payments.card();
			await card.attach('#card-container');

			const cardButton = document.getElementById('card-button');
			cardButton.addEventListener('click', async () => {
				const statusContainer = document.getElementById('payment-status-container');

				try {

					cardButton.disabled = true;
					const result = await card.tokenize();

					if (result.status === 'OK') {

						var address_data = {};

						try {
							const response = await axios.post('/api/fetch-address', {
								address_id: this.square_payment_form.billing_address_id,
							});

							address_data = response.data.data.address;
						} catch (error) {
							console.error('Error fetching address details:', error);
						}

						const verificationDetails = {
							amount: String(this.amount),
							billingContact: {
								givenName: address_data.fullname,
								familyName: address_data.fullname,
								email: address_data.email,
								phone: address_data.phone,
								addressLines: [address_data.address, address_data.address_2 ?? ''],
								city: address_data.city,
								state: '',
								countryCode: address_data.country_code,
							},
							currencyCode: 'USD',
							intent: 'CHARGE',
						};

						const verificationResult = await payments.verifyBuyer(
							result.token,
							verificationDetails,
						);

						this.square_payment_form.payment_token = result.token;
						this.square_payment_form.verification_token = verificationResult.token;

						this.square_payment_form.post(this.route("payment.square-success"));
					} else {
						cardButton.disabled = false;
						throw new Error("CARD DECLINE");
					}
				} catch (e) {
					cardButton.disabled = false;
					statusContainer.innerHTML = "PAYMENT ERROR.";
				}
			});
		},
		submit() {
			if (document.getElementById("terms").checked == true && document.getElementById("signature").checked == true) {
				this.response_message = null;
				this.overlay = true;
				axios
					.post(this.route("payment.pay"), this.form)
					.then(
						(response) => (
							(this.response = response),
							(this.response_message = response.data.message),
							(this.overlay = false)
						)
					)
					.finally(
						() =>
						(location.href = this.route(
							"payments.PaymentSuccess",
							this.response.data.payment_id
						))
					);
			} else {
				alert("Please mark both checkboxes to proceed.");
			}

		},
		submitPayPal() {
			if (document.getElementById("terms-paypal").checked == true && document.getElementById("signature-paypal").checked == true) {
				this.response_message = null;
				this.overlay = true;
				this.form.amount = this.paypal_charged_amount;
				this.form.post(this.route("payment.payPalInit"));
			} else {
				alert("Please mark both checkboxes to proceed with PayPal.");
			}
		},
		responseFromSubmit() {
			this.overlay = false;
			location.href = this.route("payments.PaymentSuccess", data.payment_id);
		},
		checkCouponCode() {
			axios
				.post(this.route("checkCoupon"), {
					code: this.form.coupon_code,
				})
				.then((response) => (this.info = response))
				.catch(function (error) {
					this.coupon_status = 0;
					this.coupon_message = "Something went wrong";
					console.log(error);
				})
				.finally(() => this.couponResponse());
		},
		couponResponse() {
			console.log(this.info);
			var info = this.info;
			if (info.data.status == 1) {
				this.coupon_status = 1;
				this.coupon_message = info.data.message;
				this.form.discount = info.data.discount;
				this.form.coupon_code_id = info.data.coupon_id;
				let amount = (this.amount * this.discount) / 100;
			} else {
				console.log(info);
				console.log(this);
				this.coupon_status = 0;
				this.coupon_message = info.data.message;
				this.form.coupon_code_id = info.data.coupon_id;
			}
		},
		initPayPalButton(amount, route, formData, axios) {
			paypal
				.Buttons({
					style: {
						shape: "rect",
						color: "gold",
						layout: "vertical",
						label: "paypal",
					},

					createOrder: function (data, actions) {
						return actions.order.create({
							purchase_units: [
								{ amount: { currency_code: "USD", value: amount } },
							],
						});
					},

					onApprove: function (data, actions) {
						return actions.order.capture().then(function (orderData) {
							console.log(
								"Capture result",
								orderData,
								JSON.stringify(orderData, null, 2)
							);
							document.getElementById("transaction_id").value = orderData.id;
							document.getElementById("payment_detail").value =
								JSON.stringify(orderData, null, 2);
							document.getElementById("paymentSuccess").click();
							const element = document.getElementById(
								"paypal-button-container"
							);
							element.innerHTML = "";
							element.innerHTML = "<h3>Thank you for your payment!</h3>";
						});
					},

					onError: function (err) {
						console.log(err);
					},

					onSuccess: function (order) {
						axios
							.post(this.route("payment.payPalSuccess"), formData)
							.then((response) => (this.response = response))
							.finally(() => this.responseFromSubmit());
					},
				})
				.render("#paypal-button-container");
		},
		paymentSuccess() {
			this.overlay = true;
			this.form.transaction_id = $("#transaction_id").val();
			this.form.payment_detail = $("#payment_detail").val();
			axios
				.post(this.route("payment.payPalSuccess"), this.form)
				.then((response) => (this.response = response))
				.finally(() => this.responseFromSubmit());
		},
		getValues() {
			// console.log(document.getElementById("transaction_id"));
		},
	},
};
</script>
