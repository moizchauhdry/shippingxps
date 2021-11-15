<template>
  <MainLayout>
    <div class="container">
      <div class="card" style="margin-top: 0px">
        <div class="card-header">Payment</div>
        <div class="card-body">

        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/Main'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import BreezeLabel from '@/Components/Label'
import BreezeValidationErrors from '@/Components/ValidationErrors'
export default {
  components: {
    BreezeAuthenticatedLayout,
    MainLayout,
    BreezeLabel,
    BreezeValidationErrors
  },
  methods : {

  },
}
</script>

<style scoped>

</style>