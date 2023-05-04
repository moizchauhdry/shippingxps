<template>
	<MainLayout>
		<div class="card p-4 mb-5">
			<breeze-validation-errors class="mb-4 text-lg" />

			<form @submit.prevent="submit" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-4 form-group">
						<breeze-label for="warehouse_id" value="Warehouse" />
						<select
							name="warehouse_id"
							class="form-control custom-select"
							v-model="form.warehouse_id"
							@change="changeWarehouse()"
						>
							<option value="">--Select Warehouse-</option>
							<option
								v-for="warehouse in warehouses"
								:value="warehouse.id"
								:key="warehouse.id"
							>
								{{ warehouse.name }}
							</option>
						</select>
					</div>
					<div class="col-md-4 form-group">
						<breeze-label for="package_weight" value="Site Name" />
						<input
							v-model="form.site_name"
							name="site_name"
							id="site_name"
							type="text"
							class="form-control"
							placeholder="e.g Amazon, Alibaba etc."
						/>
					</div>
					<div class="col-md-4 form-group">
						<breeze-label for="" value="Shop URL" />
						<input
							v-model="form.shop_url"
							name="shop_url"
							id="shop_url"
							type="url"
							class="form-control"
							placeholder="https://www.amazon.com"
						/>
					</div>
					<div class="col-md-4 form-group">
						<breeze-label for="notes" value="Notes" />
						<textarea
							v-model="form.notes"
							name="notes"
							id="notes"
							class="form-control"
							placeholder="Notes"
							rows="5"
						>
						</textarea>
					</div>
				</div>

				<div class="col-md-12">
					<div class="row">
						<div class="col-md-6">
							<h2
								class="font-semibold text-xl text-gray-800 leading-tight form-title"
							>
								Items List
							</h2>
						</div>
						<div class="col-md-6">
							<a
								v-on:click="addItem"
								class="btn btn-primary"
								style="float: right"
							>
								<span>Add Item </span>
							</a>
						</div>
					</div>

					<div class="row" v-for="(item, index) in form.items">
						<div class="form-group col-md-2">
							<breeze-label for="" v-if="index == 0" value="Name" />
							<input
								v-model="item.name"
								name="name"
								id="name"
								type="text"
								class="form-control"
								placeholder="Name"
							/>
						</div>
						<div class="form-group col-md-4">
							<breeze-label for="" v-if="index == 0" value="Description" />
							<input
								v-model="item.option"
								type="text"
								class="form-control"
								placeholder="Description"
							/>
						</div>
						<div class="form-group col-md-2">
							<breeze-label for="" v-if="index == 0" value="URL" />
							<input
								v-model="item.url"
								name="url"
								id="url"
								type="url"
								class="form-control"
								placeholder="URL"
							/>
						</div>
						<div class="form-group col-md">
							<breeze-label for="" v-if="index == 0" value="Price" />
							<input
								v-model="item.price"
								@keyup="getLineTotal(index)"
								@click="getLineTotal(index)"
								ref="price_online"
								type="number"
								class="form-control"
								placeholder="Price"
								min="0"
								step="0.01"
							/>
						</div>
						<div class="form-group col-md">
							<breeze-label for="" v-if="index == 0" value="Tax Price" />
							<input
								v-model="item.price_with_tax"
								name="price_with_tax"
								id="price_with_tax"
								type="number"
								class="form-control"
								readonly
							/>
						</div>
						<div class="form-group col-md">
							<breeze-label for="" v-if="index == 0" value="Quantity" />
							<input
								v-model="item.qty"
								@keyup="getLineTotal(index)"
								type="number"
								class="form-control"
								min="1"
							/>
						</div>
						<div class="form-group col-md">
							<breeze-label for="" v-if="index == 0" value="Total" />
							<input
								v-model="item.sub_total"
								name="sub_total"
								id="sub_total"
								type="number"
								class="form-control sub_total"
								placeholder="T.Price"
								readonly
							/>
						</div>
						<button
							@click="removeItem(index)"
							class="btn btn-link mb-2"
							:disabled="index == 0"
						>
							<i class="fas fa-times"></i>
						</button>
					</div>

					<div class="row">
						<div class="col-md-10">
							<label for="" class="float-right font-bold">Subtotal</label>
						</div>
						<div class="col-md-2">
							<input
								v-model="form.sub_total"
								name="sub_total"
								id="form.subtotal"
								type="number"
								class="form-control sub_total"
								readonly
							/>
						</div>
					</div>

					<div class="row mb-2 mt-2">
						<div class="col-md-10">
							<label class="float-right font-bold"> Shipping Charges </label>
						</div>
						<div class="col-md-2">
							<input
								v-model="form.shipping_from_shop"
								type="number"
								class="form-control"
							/>
						</div>
					</div>

					<div class="row">
						<div class="col-md-10">
							<label class="float-right font-bold"> Service Charges </label>
						</div>
						<div class="col-md-2">
							<input
								v-model="form.service_charges"
								name="service_charges"
								id="form-service_charges"
								type="number"
								class="form-control service_charges"
								readonly
							/>
						</div>
					</div>

					<div class="row">
						<div class="col-md-10">
							<label for="" class="float-right font-bold">Grand Total</label>
						</div>
						<div class="col-md-2">
							<input
								v-model="form.grand_total"
								type="number"
								class="form-control"
								readonly
							/>
						</div>
					</div>
				</div>

				<div class="order-button">
					<input type="submit" value="Finish Order" class="btn btn-danger" />
				</div>
			</form>
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
					form_type: "shopping",
					warehouse_id: "",
					site_name: "",
					shop_url: "",
					notes: "",
					shipping_from_shop: "",
					sales_tax: "",
					pickup_type: "",
					pickup_date: "",
					shipping_from_shop: "",
					items: [
						{
							name: "",
							option: "",
							url: "",
							price: 0,
							price_with_tax: 0,
							qty: 1,
							sub_total: 0,
						},
					],
					pickup_charges: 0,
					grand_total: 0,
					discount: 0,
					sub_total: 0,
					service_charges: 0,
					discount_percentage: 0,
				}),
				tabs: {
					tab1: true,
					tab2: false,
				},
				stores: [],
			};
		},
		props: {
			auth: Object,
			warehouses: Object,
		},
		methods: {
			submit() {
				this.form.post(this.route("shop-for-me.store"));
			},
			addItem() {
				this.form.items.push({
					name: "",
					option: "",
					url: "",
					price: 0,
					price_with_tax: 0,
					qty: 1,
					sub_total: 0,
				});
			},
			removeItem(index) {
				this.form.items.splice(index, 1);
			},
			changeWarehouse() {
				this.$refs.price_online[0].click();
			},
			getGrandTotal() {
				var sum = 0;
				this.form.grand_total = 0;
				this.form.items.forEach(function (n) {
					sum += parseFloat(n["sub_total"]);
				});
				this.form.sub_total = parseFloat(sum).toFixed(2);
				var servivceCharges = sum * 0.05;
				if (servivceCharges <= 5 && sum > 0) {
					this.form.service_charges = parseFloat(5).toFixed(2);
				} else {
					this.form.service_charges = parseFloat(servivceCharges).toFixed(2);
				}
				var total = parseFloat(sum) + parseFloat(this.form.service_charges);

				this.form.grand_total = parseFloat(total).toFixed(2);
			},
			getLineTotal(index) {
				var sale_tax = 0;
				var line_total = 0;

				for (var i = 0; i < this.warehouses.length; i++) {
					if (this.warehouses[i]["id"] == this.form.warehouse_id) {
						sale_tax = this.warehouses[i]["sale_tax"];
					}
				}

				const item = this.form.items[index];

				var gross_total = item.price * (sale_tax / 100);
				var net_total = (item.price_with_tax = (
					parseFloat(gross_total) + parseFloat(item.price)
				).toFixed(2));

				line_total = net_total * item.qty;
				item.sub_total = line_total.toFixed(2);

				this.getGrandTotal();
			},
		},
	};
</script>
