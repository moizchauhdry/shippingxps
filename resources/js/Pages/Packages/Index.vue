<template>
	<MainLayout>
		<div class="card mt-4">
			<div class="card-body">
				<div class="row">
					<h2
						class="font-semibold text-xl text-gray-800 leading-tight form-title mb-4"
					>
						Manage Packages
					</h2>
				</div>
				<div class="row">
					<div class="col-md-6">
						<form @submit.prevent="submit">
							<div class="row">
								<div class="form-group col-md-4">
									<input
										type="search"
										name="search"
										v-model="form.suite_no"
										class="form-control"
										placeholder="Search By Suite #"
									/>
								</div>
								<div class="form-group col-md-4">
									<button type="submit" class="btn btn-primary">Search</button>
								</div>
							</div>
						</form>
					</div>
					<div class="col-md-6">
						<h2 class="font-semibold text-xl text-gray-800 leading-tight">
							<inertia-link
								:href="route('packages.consolidation')"
								class="btn btn-success float-right m-1"
								v-if="$page.props.auth.user.type == 'customer'"
							>
								<i class="fa fa-plus mr-1"></i>Package
								Consolidation</inertia-link
							>

							<inertia-link
								:href="route('packages.multipiece')"
								class="btn btn-primary float-right m-1"
								v-if="$page.props.auth.user.type == 'customer'"
							>
								<i class="fa fa-plus mr-1"></i>Multipiece Package</inertia-link
							>

							<inertia-link
								:href="route('orders.create')"
								class="btn btn-success float-right m-1"
								v-if="$page.props.auth.user.type == 'admin'"
							>
								<i class="fa fa-plus mr-1"></i>Add Package</inertia-link
							>
						</h2>
					</div>
				</div>

				<div class="row my-4">
					<div class="col-md-6">
						<button
							type="button"
							:class="{ active: active === 'packages' }"
							class="btn btn-light w-100"
							@click="searchPackage('packages')"
						>
							Packages
						</button>
					</div>
					<div class="col-md-6">
						<button
							type="button"
							:class="{ active: active === 'rejected' }"
							class="btn btn-light w-100"
							@click="searchPackage('rejected')"
						>
							Rejected
						</button>
					</div>
				</div>
				<package-list-component v-bind="$props"></package-list-component>
			</div>
		</div>
	</MainLayout>
</template>
<style scoped>
	.label {
		padding: 5px;
	}
</style>
<script>
	import MainLayout from "@/Layouts/Main";
	import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
	import { Inertia } from "@inertiajs/inertia";
	import { useForm } from "@inertiajs/inertia-vue3";
	import PackageListComponent from "./Components/PackageListComponent.vue";

	export default {
		components: {
			BreezeAuthenticatedLayout,
			MainLayout,
			PackageListComponent,
		},
		props: {
			auth: Object,
			pkgs: Object,
			filter: Object,
		},
		data() {
			return {
				active: "packages",
				form: useForm({
					suite_no: "",
				}),
				pkg_form: {
					status: this.filter.status,
					suite_no: "",
					processing: false,
				},
			};
		},
		methods: {
			searchPackage(status) {
				this.active = status;
				this.pkg_form.status = status;
				this.pkg_form.suite_no = this.form.suite_no;
				Inertia.post(route("packages.index"), this.pkg_form);
			},
			siuteNum(user_id) {
				return 4000 + user_id;
			},
			submit() {
				this.searchPackage(this.pkg_form.status);
			},
		},
		watch: {
			params: {
				handler() {
					this.$inertia.get(this.route("packages.index"), this.params, {
						replace: true,
						preserveState: true,
					});
				},
			},
		},
	};
</script>

<style>
	button.active.btn.btn-light.w-100 {
		background-color: red !important;
		color: white;
	}
</style>
