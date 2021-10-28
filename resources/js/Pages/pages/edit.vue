<template>
    <MainLayout>
			<div class="card mt-4">
				<div class="card-body stock-subscription-form">
            <form @submit.prevent="submit">
          
              <h5>{{ form.post_title }}</h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <input name="post_title" id="post_title" type="text" class="form-control" placeholder="Post Title Name" v-model="form.post_title" required />
                  </div>
                </div>
                <div class="col-md-10 ">
                  <div class="form-group">
                     <Editor
                        api-key="hx4hzcjw0ap8hrbz29eh0nypxqnkqw0h8pxp9pmou5mmifaz"
                        :init="{
                            height: 500,
                            menubar: false,
                            plugins: [
                            'advlist autolink lists link image charmap print preview anchor',
                            'searchreplace visualblocks code fullscreen',
                            'insertdatetime media table paste code help wordcount image code'
                            ],
                            file_picker_types :'image',
                            images_upload_handler: save_image_upload_handler,
                            toolbar:
                            'undo redo | image code | bold italic underline strikethrough | alignleft aligncenter alignright  | blockquote | formatselect | spellchecker |  \
                            cut copy paste removeformat | searchreplace | bullist numlist | outdent indent | hr | link unlink anchor | inserttime |  \
                            table | subscript superscript | charmap | visualchars visualblocks nonbreaking | template | helloworld '
                        }"
                        v-model="form.post_content"
                        />   
                        </div>
                    </div>
                </div>
          
            <div class="order-button text-center">
              <input type="submit" value="Update" class="btn btn-danger" />
            </div>
           
          </form>
        </div>
      </div>
 
    </MainLayout>
</template>

<script>
    import MainLayout from '@/Layouts/Main'
    import Editor from '@tinymce/tinymce-vue'

    export default {
        components: {
            MainLayout,
            Editor
        },
         props: {
            errors: Object,
            post: Object,
        },
    data() {
            return {
                form: this.$inertia.form({
                    post_title: this.post.post_title,
                    post_content: this.post.post_content,
                    id:this.post.id
                }),
                currentYear: new Date().getFullYear(),
                Years : [],
            }
        },
        computed : {
            years () {
              const year = new Date().getFullYear()
              return Array.from({length: year - 2000}, (value, index) => year + index)
            }
          },
    methods: {
            submit() {
                this.form.post(this.route('page_update'))
                // this.form.post(this.route('Createlead'), {
                //     onFinish: () => this.form.reset(),
                // })
            },
            inArray: function(needle, haystack) {
                  console.log(needle + ' -- ' + haystack);
                    var length = haystack.length;
                    for(var i = 0; i < length; i++) {
                        if(haystack[i] == needle) return true;
                    }
                    return false;
                },
            updatePhotoPreview() {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.preview = e.target.result;
                };
                reader.readAsDataURL(this.$refs.photo.files[0]);
            },
            storePhoto() {
                if (this.$refs.photo) {
                    this.form.post_image = this.$refs.photo.files[0]
                }
                this.form.post(route('photo.store'), {
                    preserveScroll: true
                });
            },     
            save_image_upload_handler (blobInfo, success, failure, progress) {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '/api/upload_image');
                xhr.upload.onprogress = function (e) {
                    progress(e.loaded / e.total * 100);
                };
                xhr.onload = function() {
                    var json;
                    if (xhr.status === 403) {
                        failure('HTTP Error: ' + xhr.status, { remove: true });
                        return;
                    }
                    if (xhr.status < 200 || xhr.status >= 300) {
                        failure('HTTP Error: ' + xhr.status);
                        return;
                    }
                    json = JSON.parse(xhr.responseText);
                    if (!json || typeof json.location != 'string') {
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    success(json.location);
                };

                xhr.onerror = function () {
                    failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                };
                formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                xhr.send(formData);
            }            

        }
    }
</script>
