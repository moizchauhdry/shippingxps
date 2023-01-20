<template>
	<MainLayout>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<package-notification v-bind="$props"></package-notification>
					<child-package-component v-bind="$props"></child-package-component>
					<shipping-address-component
						v-bind="$props"
					></shipping-address-component>
					<package-box-component v-bind="$props"></package-box-component>
					<shipping-rate-component v-bind="$props"></shipping-rate-component>
					<consolidation-component v-bind="$props"></consolidation-component>
					<service-component v-bind="$props"></service-component>
					<mailout-component v-bind="$props"></mailout-component>
					<shipout-component v-bind="$props"></shipout-component>
					<package-image-component v-bind="$props"></package-image-component>

					<div v-show="overlay === true" class="overlay">
						<div class="overlay__inner">
							<div class="overlay__content"><span class="spinner"></span></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</MainLayout>

	<ImageViewer></ImageViewer>
	<delete-package-modal></delete-package-modal>
</template>

<script>
	import MainLayout from "@/Layouts/Main";
	import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
	import BreezeLabel from "@/Components/Label";
	import ImageViewer from "@/Components/ImageViewer";
	import $ from "jquery";
	import PackageNotification from "./PackageNotification.vue";
	import ConsolidationComponent from "./Components/ConsolidationComponent.vue";
	import ChildPackageComponent from "./Components/ChildPackageComponent.vue";
	import ShippingAddressComponent from "./Components/ShippingAddressComponent.vue";
	import PackageBoxComponent from "./Components/PackageBoxComponent.vue";
	import ShippingRateComponent from "./Components/ShippingRateComponent.vue";
	import DeletePackageModal from "./Modals/DeletePackageModal.vue";
	import PackageImageComponent from "./Components/PackageImageComponent.vue";
	import ServiceComponent from "./Components/ServiceComponent.vue";
	import MailoutComponent from "./Components/MailoutComponent.vue";
	import ShipoutComponent from "./Components/ShipoutComponent.vue";

	export default {
		components: {
			BreezeAuthenticatedLayout,
			MainLayout,
			BreezeLabel,
			ImageViewer,
			PackageNotification,
			ConsolidationComponent,
			ChildPackageComponent,
			ShippingAddressComponent,
			PackageBoxComponent,
			ShippingRateComponent,
			DeletePackageModal,
			PackageImageComponent,
			ServiceComponent,
			MailoutComponent,
			ShipoutComponent,
		},
		data() {
			return {
				// form: this.$inertia.form({
				//   package_id: this.packag.id,
				//   service_id: null,
				//   customer_message: '',
				//   service: null
				// }),
				// form_respond: this.$inertia.form({
				//   admin_message: '',
				//   status: 'pending',
				//   request: null
				// }),

				address_form: this.$inertia.form({
					package_id: this.packag.id,
					address_book_id: this.packag.address_book_id,
				}),
				// form_shipping_service: this.$inertia.form({
				//   package_id: this.packag.id,
				//   status: 'labeled',
				//   service: null,
				// }),
				form_ship: this.$inertia.form({
					package_id: this.packag.id,
					status: "shipped",
					tracking_number_out: "",
					service: null,
				}),
				tabs: {
					tab1: true,
					tab2: false,
					tab3: false,
					tab4: false,
				},
				// serverError: '',
				// showEstimatedPrice: false,
				// form_checkout: this.$inertia.form({
				//   package_id: this.packag.id,
				//   payment_module: 'package',
				// }),
				storage_fee: 0,
				overlay: false,
				displayNoteShipping: true,
				tracking_edit: false,
			};
		},
		props: {
			auth: Object,
			packag: Object,
			services: Object,
			service_requests: Object,
			images: Object,
			order_charges: Object,
			mailout_fee: Number,
			shipping_services: Object,
			// hasConsolidationRequest: Object,
			// hasConsolidationServed: Object,
			// hasMultiPieceServed: Object,
			// hasMultiPieceStatus: Object,s
			package_service_requests: Object,
			shipping_address: Object,
			// subtotal: Object,
			total: Object,
			package_boxes: Object,
		},
		computed: {
			siuteNum() {
				return 4000 + this.$page.props.auth.user.id;
			},
		},
		mounted() {
			this.getStorageFee();
		},
		methods: {
			formatNumber(num) {
				return parseFloat(num).toFixed(2);
			},
			// submit() {
			//   this.form.post(this.route('packages.service-request'));
			//   this.form.reset();
			//   Inertia.reload({only: ['service_requests']});
			// },
			// setActiveService(service) {
			//   this.form.service_id = service.id;
			//   this.form.service = service;
			// },
			// cancelServiceForm() {
			//   this.form.service_id = null;
			// },
			// submitRespondForm() {
			//   this.form_respond.post(this.route('packages.service-handle'));
			//   this.form_respond.reset();
			//   Inertia.reload({only: ['service_requests']});
			// },
			// setServiceResponse(request) {
			//   this.form_respond.request_id = request.id;
			//   this.form_respond.request = request;
			// },
			// requestComplete() {
			//   this.form_respond.status = 'served';
			//   this.submitRespondForm();
			// },
			// requestReject() {
			//   this.form_respond.status = 'rejected';
			//   this.submitRespondForm();
			// },
			submitShippingAddressForm() {
				this.address_form.post(this.route("packages.address.update"));
				Inertia.reload();
			},
			submitShipOutForm() {
				this.form_ship.post(this.route("packages.ship-package"));
				this.tracking_edit = false;
			},
			// setShippingService(service) {
			//   var result = window.confirm('After Confirming the shippment method you wont be able to change. Are you sure to confirm ?')
			//   if(result){
			//     this.form_shipping_service.service = service;
			//     this.form_shipping_service.post(this.route('packages.set-shipping-service'));
			//   }
			// },
			// getShippingRates() {
			//   this.overlay = true;
			//   this.showEstimatedPrice = false;
			//   this.isLoading = true;
			//   let quote_params = {
			//     ship_from: this.packag.warehouse_id,
			//     ship_to: this.packag.address.country_id,
			//     weight: this.packag.package_weight,
			//     unit: this.packag.weight_unit,
			//     weight_unit: this.packag.weight_unit +"_"+ this.packag.dim_unit,
			//     length: this.packag.package_length,
			//     width: this.packag.package_width,
			//     height: this.packag.package_height,
			//     zipcode: this.packag.address.zip_code,
			//     city : this.packag.address.city,
			//     is_residential : this.packag.address.is_residential,
			//   };
			//   axios.get(this.route('getServicesList')).then(response => {
			//     response.data.services.forEach((ele, index) => {
			//       quote_params.service = ele;
			//       this.getRates(quote_params);
			//     })
			//     this.overlay = false;
			//   }).catch(error => {
			//     this.overlay = false;
			//   })
			// },
			// getShippingRatesByOrders() {

			//   this.showEstimatedPrice = false;
			//   this.isLoading = true;
			//   let pieces = [];

			//   this.packag.orders.forEach(function (value,index) {
			//     let piece = {
			//       "weight": value.package_weight.toString(),
			//       "length": value.package_length.toString(),
			//       "width": value.package_width.toString(),
			//       "height": value.package_height.toString(),
			//       "insuranceAmount": "0",
			//       "declaredValue": value.declared_value == 0 ? "1" : value.declared_value.toString()
			//     };

			//     pieces.push(piece)
			//   })

			//   let quote_params = {
			//     ship_from: this.packag.warehouse_id,
			//     ship_to: this.packag.address.country_id,
			//     weight: this.packag.package_weight,
			//     unit: this.packag.weight_unit,
			//     weight_unit: this.packag.weight_unit +"_"+ this.packag.dim_unit,
			//     pieces:pieces,
			//     zipcode: this.packag.address.zip_code,
			//     city : this.packag.address.city,
			//     is_residential : this.packag.address.is_residential,
			//   };
			//   axios.get(this.route('getServicesList')).then(response => {
			//     console.log(response.data.services)
			//     response.data.services.forEach((ele, index) => {
			//       console.log(ele);
			//       quote_params.service = ele;
			//       this.getRatesByOrders(quote_params);
			//     })
			//   }).catch(error => {
			//     console.log(error)
			//   })

			// },
			getServiceSubTotal() {
				let request_total = 0;
				this.service_requests.forEach(function (item) {
					if (item.status == "served") {
						request_total += item.price;
					}
				});

				var consolidation_total = 0;
				console.log(this.hasConsolidationServed);
				if (this.hasConsolidationServed) {
					consolidation_total = this.packag.orders.length * 1.5;
				} else {
					consolidation_total = 0;
				}

				return this.formatNumber(request_total + consolidation_total);
			},
			getGrandTotal() {
				var r1 = this.formatNumber(
					this.packag.shipping_total + this.mailout_fee + this.storage_fee
				);
				var r2 = this.getServiceSubTotal();

				return this.formatNumber(parseFloat(r1) + parseFloat(r2));
			},
			setActiveTabAB(tab) {
				for (var key in this.tabs) {
					if (key === tab) {
						this.tabs[key] = true;
					} else {
						this.tabs[key] = false;
					}
				}
			},
			getTabClass(tab) {
				if (this.tabs[tab] === true) {
					return "nav-link active";
				} else {
					return "nav-link";
				}
			},
			getTabPaneClass(tab) {
				if (this.tabs[tab] === true) {
					return "tab-pane show active";
				} else {
					return "tab-pane fade";
				}
			},
			imgURL(url) {
				return "/public/uploads/" + url;
			},
			viewImage(event) {
				console.log(event.target.src);
				var modal = document.getElementById("imageViewer");
				var imageSRC = document.querySelector("#imageViewer img");
				imageSRC.src = event.target.src;
				modal.classList.add("show");
				$("#imageViewer").show();
			},
			makePackageUrl(order_id) {
				return route("package.create") + "?order_id=" + order_id;
			},

			// checkout() {
			//   this.$inertia.post(route("payment.index", this.form_checkout));
			// },
			// getRates(quote_params) {
			//   axios.get("/getQuote", {
			//     params: quote_params,
			//   }).then((response) => {
			//         console.log(response.data.service)
			//         this.isLoading = false;
			//         if (response.data.status) {
			//           this.showEstimatedPrice = true;
			//           this.shipping_services[response.data.service.service_id] = response.data.service;

			//         } else {
			//           this.serverError = response.data.message;
			//         }
			//         if(response.data.service.isReady !== undefined && response.data.service.isReady  === true){
			//           this.displayNoteShipping = false;
			//         }
			//       }
			//   );
			// },
			// getRatesByOrders(quote_params) {
			//   axios.get("/getQuoteByOrders", {
			//     params: quote_params,
			//   }).then((response) => {
			//         console.log(response.data.service)
			//         this.isLoading = false;
			//         if (response.data.status) {
			//           this.showEstimatedPrice = true;
			//           this.shipping_services[response.data.service.service_id] = response.data.service;

			//         } else {
			//           this.serverError = response.data.message;
			//         }
			//         if(response.data.service.isReady !== undefined && response.data.service.isReady  === true){
			//           this.displayNoteShipping = false;
			//         }
			//       }
			//   );
			// },
			getStorageFee() {
				axios
					.get(this.route("getStorageFee"), {
						params: {
							package_id: this.packag.id,
						},
					})
					.then((response) => {
						this.storage_fee = response.data;
					});
			},

			confirmDeletion() {
				let packageID = this.packag.id;
				var modal = document.getElementById("deleteModal");
				modal.classList.add("show");
				$("#deleteModal").show();
			},
			closeDeletionModal() {
				var modal = document.getElementById("deleteModal");
				modal.style.display = "none";
			},
			deletePackage() {
				this.overlay = true;
				axios
					.post(this.route("packages.destroy"), { id: this.packag.id })
					.then(({ data }) => {
						if (data.status == 1) {
							alert(data.message);
							location.href = data.url;
						} else {
							alert(data.message);
							this.overlay = false;
							var modal = document.getElementById("deleteModal");
							modal.style.display = "none";
						}
					});
			},
			/*imgURL(url) {
      return "/public" + url;
    },*/
		},
	};
</script>

<!-- 
<style scoped>
.overlay {
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  position: fixed;
  background: rgba(0,0,0,0.5);
  z-index:9999999999;
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
.card {
  margin-top: 25px;
}

.card-body {
  padding: 0.8rem;
}

.card-body p strong {
  color: #212529;
  margin-right: 12px;
}

.address-card {
  height: 130px;
}

.card-header > h3 {
  font-weight: bold;
}
</style> -->
