<template>

	<img style="width: 100px; height: auto" class="img-thumbnail" :src="imgURL(packag.return_label_file)"/>

	<a :href="packag.return_label_file" class="m-1" download>{{packag.return_label_file}}</a>

	<button @click="open()">
		<i class="fa fa-list mr-1"></i>Return To Sender
	</button>

	<div class="modal fade" id="return_to_sender" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-backdrop="true">
		<div class="modal-dialog border" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">RETURN TO SENDER</h5>
					<button type="button" @click="close()" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="">Have Label?</label>
						<select v-model="form.return_label" class="form-control custom-select">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</div>
					<div class="form-group">
					    <input type="file" @input="form.return_label_file = $event.target.files[0]" />
						<progress v-if="form.progress" :value="form.progress.percentage" max="100">{{ form.progress.percentage }}%</progress>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-dark" @click="close()" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary" @click="submit()">Submit</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import $ from "jquery";

	export default {
		name: "Package Delete Component",
		props: {
			packag: Object,
		},
		data() {
			return {
				form: this.$inertia.form({
					package_id: this.packag.id,
					return_label: "no",
					return_label_file: null,
				}),
			};
		},
		methods: {
			imgURL(url) {
				return "/public/uploads/" + url;
			},
			open() {
				var modal = document.getElementById("return_to_sender");
				modal.classList.add("show");
				$("#return_to_sender").show();
			},
			close() {
				var modal = document.getElementById("return_to_sender");
				modal.style.display = "none";
			},
			submit() {
				this.form.post(this.route("packages.return-package"));
			},
		},
	};
</script>
