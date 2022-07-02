<template>
	<MainLayout>
		<div class="card mt-4 pr-2">
			<div class="card-header">
				Gift Card
				<div class="float-right">
					<inertia-link
						:href="route('gift-card.create')"
						v-if="$page.props.auth.user.type == 'customer'"
						class="btn btn-primary float-right"
						>Add Request</inertia-link
					>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-responsive">
					<thead class="text-center">
						<tr>
							<th>ID</th>
							<th>Customer</th>
							<th>Title</th>
							<th>Type</th>
							<th>Amount ($)</th>
							<th>Quantity</th>
							<th>Payment Status</th>
							<th>Notes</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody class="text-center">
						<tr v-for="card in gift_cards" :key="card.id">
							<td>{{ card.id }}</td>
							<td>
								<inertia-link :href="route('detail-customer', card.user.id)" class="btn btn-link">
								# {{ siuteNum(card.user.id) }} - {{ card.user.name }} 
								</inertia-link>
							</td>
							<td>{{ card.title }}</td>
							<td>{{ card.type }}</td>
							<td>{{ card.amount }}</td>
							<td>{{ card.qty }}</td>
							<td>
								<span class="badge badge-secondary">
									{{ card.payment_status }}
								</span>
							</td>
							<td>{{ card.notes }}</td>
							<td>
								<inertia-link
									:href="route('gift-card.edit', card.id)"
									class="btn btn-primary float-right"
									>Edit</inertia-link
								>
							</td>
						</tr>
					</tbody>
				</table>

				<div class="col-md-12 text-center" v-if="gift_cards.length == 0">
					<span>There are no gift card requests</span>
				</div>
			</div>
			<div class="card-footer"></div>
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
		props: {
			gift_cards: Object,
		},
		methods: {
			changeStatus(id, status, event) {
				axios
					.post(this.route("gift-card.changeStatus"), {
						id: id,
						status: status,
					})
					.then(function (response) {
						console.log(response.data.insurance.status);
						let status = response.data.insurance.status;
						if (status === 1) {
							event.target.classList.remove("btn-success");
							event.target.classList.add("btn-danger");
						} else {
							event.target.classList.add("btn-success");
							event.target.classList.remove("btn-danger");
						}
					})
					.catch(function (error) {
						console.log(error);
					});
			},
			siuteNum(user_id){
				return 4000 + user_id;
			},
		},
	};
</script>

<style scoped></style>
