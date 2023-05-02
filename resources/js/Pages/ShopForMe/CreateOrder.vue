<template>
	<MainLayout>
		<div class="card p-4">
			<form @submit.prevent="submit" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-4 form-group">
						<breeze-label for="warehouse_id" value="Warehouse" />
						<select
							name="warehouse_id"
							class="form-control custom-select"
							v-model="form.warehouse_id"
							required
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
						<breeze-label for="package_length" value="Shop URL" />
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

					<div
						class="d-flex"
						v-for="(item, index) in form.items"
						:key="item.id"
						:id="'order-' + index"
						:data-id="index"
					>
						<div class="form-group mr-1">
							<breeze-label for="" v-if="index == 0" value="Name" />
							<input
								v-model="item.name"
								name="name"
								id="name"
								type="text"
								class="form-control"
								placeholder="Name"
								required
							/>
						</div>
						<div class="form-group mr-1">
							<breeze-label for="" v-if="index == 0" value="Description" />
							<input
								v-model="item.option"
								name="option"
								id="option"
								type="text"
								class="form-control"
								placeholder="Description"
							/>
						</div>
						<div class="form-group mr-1">
							<breeze-label for="" v-if="index == 0" value="URL" />
							<input
								v-model="item.url"
								name="url"
								id="url"
								type="url"
								class="form-control"
								placeholder="URL"
								required
							/>
						</div>
						<div class="form-group mr-1">
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
								required
							/>
						</div>
						<div class="form-group mr-1">
							<breeze-label for="" v-if="index == 0" value="Price with Tax" />
							<input
								v-model="item.price_with_tax"
								name="price_with_tax"
								id="price_with_tax"
								type="number"
								class="form-control"
								required
								readonly
							/>
						</div>
						<div class="form-group mr-1">
							<breeze-label for="" v-if="index == 0" value="Quantity" />
							<input
								v-model="item.qty"
								@keyup="getLineTotal(index)"
								type="number"
								class="form-control"
								placeholder="Quantity"
								min="1"
								required
							/>
						</div>
						<div class="form-group mr-1">
							<breeze-label for="" v-if="index == 0" value="Total" />
							<input
								v-model="item.sub_total"
								name="sub_total"
								id="sub_total"
								type="number"
								class="form-control sub_total"
								placeholder="T.Price"
								required
								readonly
							/>
						</div>
						<button
							@click="removeItem(index)"
							class="btn btn-link"
							:disabled="index == 0"
						>
							<i class="fas fa-times"></i>
						</button>
					</div>

					<div class="row">
						<div class="col-6 col-md-2 offset-md-8">
							<breeze-label
								class="float-right"
								for="form_pickup.subtotal"
								value="Subtotal"
							/>
						</div>
						<div class="col-6 col-md-1 p-0">
							<input
								v-model="form.sub_total"
								name="sub_total"
								id="form.subtotal"
								type="number"
								class="form-control sub_total"
								required
								readonly
							/>
						</div>
					</div>
					<div class="row">
						<div class="col-6 col-md-2 offset-md-8">
							<breeze-label
								class="float-right"
								for="form_pickup.subtotal"
								value="Service Charges"
							/>
							<br /><small class="float-right"
								>Minimum USD 5 or 5% of Subtotal</small
							>
						</div>
						<div class="col-6 col-md-1 p-0">
							<input
								v-model="form.service_charges"
								name="service_charges"
								id="form-service_charges"
								type="number"
								class="form-control service_charges"
								required
								readonly
							/>
						</div>
					</div>
					<div class="row">
						<div class="col-6 col-md-1 offset-md-9">
							<breeze-label for="grand_total" value="Grand Total" />
						</div>

						<div class="col-6 col-md-1 p-0">
							<input
								v-model="form.grand_total"
								type="number"
								class="form-control grand_total"
								required
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

	export default {
		components: {
			BreezeAuthenticatedLayout,
			MainLayout,
			BreezeLabel,
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
