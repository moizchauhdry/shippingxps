<template>
  <MainLayout>
    <div>

      <section>
        <div class="container">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Orders </h2>
          <div class="stock-subscription-form">

            <form @submit.prevent="submit" enctype="multipart/form-data">
              <div class="order-form">
                <breeze-validation-errors class="mb-4" />
                <flash-messages class="mb-4" />

                <div class="row">
                  <div class="col-md-3">

                    <div class="form-group">
                      <breeze-label for="customer_id" value="Customer" />
                      <select name="customer_id" class="form-select" v-model="form.customer_id" disabled required>
                        <option selected>Select</option>
                        <option v-for="customer in customers" :value="customer.id" >{{ customer.name}}</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <breeze-label for="status" value="Status" />
                      <select name="status" class="form-select" v-model="form.status"  required>
                        <option v-for="status in status_list" :value="status" >{{ status}}</option>
                      </select>
                    </div>


                    <div class="form-group">
                      <breeze-label for="warehouse_id" value="Received At" />
                      <select name="warehouse_id" class="form-select" v-model="form.warehouse_id" required>
                        <option selected>Select</option>
                        <option v-for="warehouse in warehouses" :value="warehouse.id"  :key="warehouse.id" >{{ warehouse.name}}</option>
                      </select>
                    </div>

                  </div>

                  <div class="col-md-3">

                    <div class="form-group">
                      <breeze-label for="tracking_number_in" value="Tracking #" />
                      <input v-model="form.tracking_number_in" name="tracking_number_in" id="tracking_number_in" type="text" class="form-control" placeholder="Tracking Number" required />
                    </div>

                    <div class="form-group">
                      <breeze-label for="received_from" value="Received From" />
                      <input v-model="form.received_from" name="received_from" id="received_from" type="text" class="form-control" placeholder="Like Amazon, Ebay, etc" required />
                    </div>

                    <div class="form-group">
                      <breeze-label for="package_weight" value="Package Weight" />
                      <input v-model="form.package_weight" name="package_weight" id="package_weight" type="number" class="form-control" placeholder="Package Weight" min="1" required />
                    </div>
                  </div>


                  <div class="col-md-3">

                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <breeze-label for="warehouse_id" value="Weight Unit" />
                          <select name="weight_unit" class="form-select" v-model="form.weight_unit" required>
                            <option value="lb">Lb</option>
                            <option value="kg">Kg</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <breeze-label for="dim_unit" value="Dimention Unit" />
                          <select name="dim_unit" class="form-select" v-model="form.dim_unit" required>
                            <option value="in">Inch</option>
                            <option value="cm">Cm</option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <breeze-label for="package_length" value="Package Length" />
                      <input v-model="form.package_length" name="package_length" id="package_length" type="number" class="form-control" placeholder="Package Length" min="1" required />
                    </div>

                    <div class="form-group">
                      <breeze-label for="package_height" value="Package Height" />
                      <input v-model="form.package_height" name="package_height" id="package_height" type="number" class="form-control" placeholder="Package Height" min="1" required />
                    </div>
                  </div>

                  <div class="col-md-3">

                    <div class="form-group">
                      <breeze-label for="package_weight" value="Package Width" />
                      <input v-model="form.package_width" name="package_width" id="package_width" type="number" class="form-control" placeholder="Package Width" min="1" required />
                    </div>

                    <div class="form-group">
                      <breeze-label for="notes" value="Notes" />
                      <textarea v-model="form.notes"
                                name="notes"
                                id="notes"
                                class="form-control"
                                placeholder="Notes"
                                rows="4"
                                style="resize:none;"
                                required >
                      </textarea>
                    </div>

                  </div>

                </div>

                <div class="row">

                  <h1>Items</h1>

                  <div class="col-md-12">

                    <div class="row">
                      <div class="col-md-2 offset-md-10">
                        <a v-on:click="addItem" class="btn btn-primary" style="float:right;">
                          <span>Add Item </span>
                        </a>
                      </div>
                    </div>

                    <div v-for="(item,index) in form.items" :key="item.id" class="row">

                      <div class="col-md-4">
                        <div class="form-group">
                          <breeze-label for="name" value="Name" />
                          <input v-model="item.name" name="name" id="name" type="text" class="form-control" placeholder="Name" required />
                        </div>
                      </div>

                      <div class="col-md-5">
                        <div class="form-group">
                          <breeze-label for="description" value="Description" />
                          <input v-model="item.description" name="description" id="description" type="text" class="form-control" placeholder="Description" required />
                        </div>
                      </div>

                      <div class="col-md-1">
                        <div class="form-group">
                          <breeze-label for="quantity" value="Qty" />
                          <input v-model="item.quantity" name="quantity" id="quantity" type="number" class="form-control" placeholder="Qty" required />
                        </div>
                      </div>
                      <!--
                                            <div class="col-md-2">
                                                <div class="text-center" v-if="item.image !=''">
                                                    <img style="width:100px;height:auto;" class="img-thumbnail" :src="imgURL(item.image)" />
                                                </div>
                                            </div> -->

                      <!-- <div class="col-md-3">
                        <div class="form-group">
                            <input type="file" @input="item.image = $event.target.files[0]" />    
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                {{ form.progress.percentage }}%
                            </progress>
                        </div>
                      </div> -->

                      <div class="col-md-1" v-show="index!=0">
                        <div class="form-group">
                          <a v-on:click="removeItem(index,item.id)" class="btn btn-primary" style="margin-top:20px;">
                            <span>Remove</span>
                          </a>
                        </div>
                      </div>

                    </div>

                  </div>
                </div>


                <div class="row">
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-12">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                          Order Images
                        </h2>
                      </div>
                      <div class="col-md-6">
                        <a v-on:click="addImage" class="btn btn-primary" style="float:right;">
                          <span>Add More Image </span>
                        </a>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-3">
                        <breeze-label for="name" value="Image" />
                      </div>
                    </div>

                    <div v-for="(image,index) in form.images" :key="image.id" class="row">

                      <div class="col-md-3">
                        <div class="form-group">
                          <input type="file"  @input="image.image = $event.target.files[0]" />
                          <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                            {{ form.progress.percentage }}%
                          </progress>
                        </div>
                      </div>

                      <div class="col-md-1" v-show="index!=0">
                        <div class="form-group">
                          <a v-on:click="removeImage(index)" class="btn btn-primary" style="margin-top:10px;">
                            <span>Remove</span>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">

                  <div class="col-md-12">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                      Images
                    </h2>
                  </div>
                  <div v-for="(image, index) in order.images" :key="image.id" class="col-md-3">
                    `                       <div class="text-center">
                    <img style="width:100px;height:auto;" class="img-thumbnail" :src="imgURL(image.image)" />
                    <a href="void(0);" @click="deleteImage($event, index, image.id)"><i class="fa fa-trash"></i></a>
                  </div>
                  </div>
                </div>

                <div class="order-button">
                  <input type="submit" value="Update Order" class="btn btn-danger" />
                </div>
              </div>

            </form>
          </div><!-- subscription -->
        </div><!-- container -->
      </section>

    </div>
  </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/Main'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import BreezeLabel from '@/Components/Label'

export default {
  components: {
    BreezeAuthenticatedLayout,
    MainLayout,
    BreezeLabel
  },
  data() {
    return {
      form: this.$inertia.form({
        id:this.order.id,
        status : this.order.status,
        customer_id: this.order.customer_id,
        warehouse_id: this.order.warehouse_id,
        tracking_number_in: this.order.tracking_number_in,
        shipping_total: this.order.shipping_total,
        package_weight:this.order.package_weight,
        package_length:this.order.package_length,
        package_width:this.order.package_width,
        package_height:this.order.package_height,
        weight_unit: this.order.weight_unit,
        dim_unit: this.order.dim_unit,
        notes: this.order.notes,
        received_from:this.order.received_from,
        address: this.order.address,
        post_image: '',
        items:this.order.items,
        images : [
          {
            image: ""
          }
        ],
      })
    };
  },
  props: {
    auth: Object,
    customers:Object,
    order:Object,
    status_list:Object,
    warehouses:Object
  },
  methods : {
    submit() {
      this.form.post(this.route('orders.update'))
    },
    addItem(){
      this.form.items.push(
          {
            name: "",
            description: "",
            quantity: "",
            image: ""
          }
      )
    },
    removeItem(index,item_id){
      //when deleting already saved item, remove from server also.
      if (typeof item_id !== 'undefined'){

        axios.post(this.route('orders.removeItem'),{item_id:item_id})
        .then(({ data }) => {
              this.form.items.splice(index, 1);
            }
        );
      }else{
        this.form.items.splice(index, 1);
      }

    },
    addImage(){
      this.form.images.push(
          {
            name: ""
          }
      )
    },
    removeImage(index){
      this.form.images.splice(index, 1);
    },
    deleteImage(e, index, id) {
      e.preventDefault();
      if (typeof id !== 'undefined') {
        let r = confirm('Are you sure you want to delete this image?');
        if (r) {
          axios.post(this.route('orders.removeImage'), {id: id})
          .then(({data}) => {
                this.order.images.splice(index, 1);
              }
          );
        }
      }
    },
    imgURL(url) {
      return "/uploads/"+url;
    },
  }
}
</script>
