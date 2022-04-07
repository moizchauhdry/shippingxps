<template>
	<MainLayout>
		<div class="card mt-4 pr-2">
			<div class="card-header"><b>Gift Card</b></div>
			<div class="card-body">
				<form @submit.prevent="submit" enctype="multipart/form-data">
					<div class="row">
						<div class="form-group col-md-6">
							<label for="title">Title</label>
							<input
								type="text"
								class="form-control"
								name="title"
								id="title"
								v-model="form.title"
								required
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
								<option value="Physical">Physical</option>
								<option value="Electronic">Electronic</option>
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
						<div
							class="form-group col-md-6"
							v-show="$page.props.auth.user.type == 'admin'"
						>
							<label for="status">Status</label>
							<select
								class="form-control"
								name="status"
								v-model="form.status"
								id="status"
								required
							>
								<option value="" selected>--Select Status--</option>
								<option value="Accepted">Accepted</option>
								<option value="Rejected">Rejected</option>
							</select>
						</div>

						<div class="row">
							<div class="col-md-12">
								<h2
									class="font-semibold text-xl text-gray-800 leading-tight form-title"
								>
									Gift Card Files
								</h2>
							</div>
							<div class="col-md-6">
								<a
									v-on:click="addImage"
									class="btn btn-primary"
									style="float: right"
								>
									<span>Add More Image </span>
								</a>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<breeze-label for="name" value="Image" />
							</div>
						</div>
						<div
							v-for="(image, index) in form.images"
							:key="image.id"
							class="row"
						>
							<div class="col-md-3">
								<div class="form-group">
									<input
										type="file"
										@input="image.image = $event.target.files[0]"
									/>
									<progress
										v-if="form.progress"
										:value="form.progress.percentage"
										max="100"
									>
										{{ form.progress.percentage }}%
									</progress>
								</div>
							</div>

							<div class="col-md-1" v-show="index != 0">
								<div class="form-group">
									<a
										v-on:click="removeImage(index)"
										class="btn btn-primary"
										style="margin-top: 10px"
									>
										<span>Remove</span>
									</a>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12" v-show="gift_card.files.length > 0">
								<h2
									class="font-semibold text-xl text-gray-800 leading-tight form-title"
								>
									Images
								</h2>
							</div>
							<div
								v-for="(image, index) in gift_card.files"
								:key="image.id"
								class="col-md-3"
							>
								`
								<div class="text-center">
									<img
										style="width: 100px; height: auto"
										class="img-thumbnail"
										:src="imgURL(image.file_name)"
										@click="viewImage($event)"
									/>
									<a
										href="void(0);"
										@click="deleteImage($event, index, image.id)"
										><i class="fa fa-trash"></i
									></a>
								</div>
							</div>
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
							<button type="submit" name="submit" class="btn btn-primary">
								Save & Update
							</button>
							<button
								v-show="
									$page.props.auth.user.type != 'admin' &&
									form.amount != NULL &&
									gift_card.payment_status != 'Paid' &&
									gift_card.admin_approved_at != null
								"
								type="button"
								name="approve"
								class="btn btn-primary ml-2"
								@click="approve()"
							>
								Accept & Checkout
							</button>
						</div>
					</div>
				</form>

				<fieldset class="border p-4 mb-4 col-12 mt-2">
					<legend class="w-auto">
						<small><b>Comments</b></small>
					</legend>
					<div class="row">
						<div class="form-group col-12">
							<input
								class="form-control"
								v-model="commentForm.message"
								name="message"
								id="cmessage"
								placeholder="Add new comment"
							/>
						</div>
						<div class="col-12">
							<label
								class="btn btn-primary btn-sm float-right"
								@click="saveComment()"
								>Add Comment</label
							>
						</div>
					</div>
					<div
						v-for="comment in comments"
						:key="comment.id"
						class="card"
						style="margin: 10px 0px"
					>
						<div class="card-body" style="padding: 5px 10px; font-size: 16px">
							{{ comment.message }}
						</div>
						<div class="card-footer" style="padding: 5px 10px; font-size: 12px">
							<p class="float-right">
								{{ comment.user.name }} commented at {{ comment.created_at }}
							</p>
						</div>
					</div>
				</fieldset>
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
		data() {
			return {
				form: this.$inertia.form({
					title: this.gift_card.title,
					type: this.gift_card.type,
					amount: this.gift_card.amount,
					qty: this.gift_card.qty,
					notes: this.gift_card.notes,
					approve: 0,
					status: this.gift_card.status,
					images: [
						{
							image: "",
						},
					],
				}),
				commentForm: this.$inertia.form({
					message: "",
				}),
				comments: this.comments,
			};
		},
		props: {
			gift_card: Object,
			comments: Object,
		},
		methods: {
			submit() {
				this.form.post(this.route("gift-card.edit", this.gift_card.id));
			},
			approve() {
				this.form.approve = 1;
				this.form.post(this.route("gift-card.edit", this.gift_card.id));
			},
			loadComments() {},
			saveComment() {
				if (this.commentForm.message == "") {
					return false;
				}
				this.commentForm.post(
					this.route("gift-card.storeComment", this.gift_card.id)
				);
				this.commentForm.reset();
			},
			addImage() {
				this.form.images.push({
					name: "",
				});
			},
			removeImage(index) {
				this.form.images.splice(index, 1);
			},
			deleteImage(e, index, id) {
				e.preventDefault();
				if (typeof id !== "undefined") {
					let r = confirm("Are you sure you want to delete this image?");
					if (r) {
						axios
							.post(this.route("gift-card.removeImage"), { id: id })
							.then(({ data }) => {
								this.gift_card.images.splice(index, 1);
							});
					}
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
		},
		created() {},
	};
</script>

<style scoped></style>
