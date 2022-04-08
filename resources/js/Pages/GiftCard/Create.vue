<template>
	<MainLayout>
		<div class="card mt-4 pr-2">
			<div class="card-header">Gift Card</div>
			<div class="card-body">
				<form @submit.prevent="submit" enctype="multipart/form-data">
					<breeze-validation-errors class="mb-4" />
					<div class="row">
						<div class="form-group col-md-6">
							<label for="title">Title</label>
							<input
								type="text"
								class="form-control"
								name="title"
								id="title"
								v-model="form.title"
							/>
						</div>
						<div class="form-group col-md-6">
							<label for="type">Type</label>
							<select
								class="form-control"
								name="type"
								v-model="form.type"
								id="type"
								required
							>
								<option value="" selected>--Select Type--</option>
								<option value="PHYSICAL">Physical</option>
								<option value="ELECTRONIC">Electronic</option>
							</select>
						</div>
						<div class="form-group col-md-3">
							<label for="amount">Amount</label>
							<input
								type="number"
								step="0.01"
								class="form-control"
								name="amount"
								id="amount"
								v-model="form.amount"
								required
							/>
						</div>
						<div class="form-group col-md-3">
							<label for="qty">Quantity</label>
							<input
								type="number"
								class="form-control"
								name="qty"
								id="qty"
								v-model="form.qty"
								required
							/>
						</div>
						<div class="form-group col-md-12">
							<label for="notes">Notes</label>
							<textarea
								class="form-control"
								name="notes"
								id="notes"
								v-model="form.notes"
								cols="10"
								rows="5"
								required
							></textarea>
						</div>
						<div class="form-group col-12">
							<button type="submit" class="btn btn-primary">
								Send Request
							</button>
						</div>
					</div>
				</form>
			</div>
			<div class="card-footer"></div>
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
					title: "",
					type: "",
					amount: "",
					qty: "",
					notes: "",
				}),
			};
		},
		props: {
			//
		},
		methods: {
			submit() {
				this.form.post(this.route("gift-card.create"));
			},
		},
	};
</script>

<style scoped></style>
