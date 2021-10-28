<template>
  <MainLayout>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">Notifications</h2>
    <div class="row" v-if="$page.props.notifications.length" style="margin-top:20px;">
      <div class="col">
        <div class="d-block"  v-for="notification in $page.props.notifications" :key="notification.id">
          <template v-if="notification.read_at == null">
            <p v-on:click="markAsRead($event,notification)" class="alert alert-danger d-block" v-html="notification.data.message">              
            </p>
          </template>
          <template v-else>
            <p class="alert alert-success d-block" v-html="notification.data.message">
            </p>
          </template>
        </div>
        <inertia-link :href="route('notifications.mark-all-read')" class="float-right" >Mark All Read</inertia-link>
      </div>
    </div>
  </MainLayout>
</template>
<style scoped>
.row {
  margin-right: 0px !important;
}
.click-here {
  color: orangered;
}
</style>
<script>
import MainLayout from '@/Layouts/Main'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'

export default {
  components: {
    BreezeAuthenticatedLayout,
    MainLayout
  },
  props: {
    auth: Object,
    notifications:Object
  },
    data() {
      return {
          form: this.$inertia.form({
              notification_id: '',
          })
      }
  },
  methods:{
    markAsRead(event,notification){
      //event.preventDefault();
      // if(event.target == event.currentTarget){
      // }

      this.form.notification_id = notification.id;
      this.form.post(this.route('notifications.mark-read'), {
          onFinish: () => {
            this.form.notification_id='';
            //window.location.href = notification.url;
            // if(notification.url){
            // }
          }
      }); 
    }
  }
}
</script>
