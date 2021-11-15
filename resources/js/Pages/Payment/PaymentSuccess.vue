<template>
  <MainLayout>
    <div class="container">
      <div class="card" style="margin-top: 0px">
        <div class="card-header">Payment</div>
        <div class="card-body text-center">
          <h2 style="font-size: 25px;color: green">Payment Success</h2>
          <br>
          <br>
          <br>
          <inertia-link class="btn btn-primary" :href="route('dashboard')" >
            Dashboard
          </inertia-link>
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
import BreezeNavLink from '@/Components/NavLink'
import BreezeResponsiveNavLink from '@/Components/ResponsiveNavLink'
export default {
  components: {
    BreezeAuthenticatedLayout,
    MainLayout,
    BreezeLabel,
    BreezeValidationErrors,
    BreezeNavLink,
    BreezeResponsiveNavLink,
  },
  methods : {

  },
}
</script>

<style scoped>

</style>