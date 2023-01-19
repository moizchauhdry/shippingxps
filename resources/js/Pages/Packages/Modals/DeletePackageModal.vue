<template>
    <div v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'" 
      class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-backdrop="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Delete Package</h5>
              <button type="button" @click="closeDeletionModal()" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="text-center mt-2 mb-5">
                Are You sure you want to delete this "Package # {{ packag.package_no }}"?
                <br>
                You can't undo this action.
              </div>
              <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Warning!</h4>
                <p>By deleting this package it will delete all the connected orders.</p>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-dark" @click="closeDeletionModal()" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-danger" @click="deletePackage()">Delete Anyway</button>
            </div>
          </div>
        </div>
      </div>
</template>

<script>
    export default {
        name: "Delete Package Modal",
        props: {
            packag:Object,
        },
        data() {
            return {
                // 
            }
        },
        methods:{
            // 
        }
    }
</script>