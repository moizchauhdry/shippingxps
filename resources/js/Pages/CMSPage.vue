<template>
    <header-component ></header-component>
    <div role="main" class="main">
        <div class="container mt-5 mb-5" v-html="pageContext.description">
            
        </div>
    </div>
    <FooterComponent />

</template>

<style scoped>

</style>

<script>
import HeaderComponent from '../Components/Header.vue'
import FooterComponent from '../Components/Footer.vue'


export default {
components: {
    'header-component': HeaderComponent,
    FooterComponent,
  },    
  data() {
    
  },
  props: {
            auth: Object,
            canLogin: Boolean,
            canRegister: Boolean,
            errors: Object,
            laravelVersion: String,
            phpVersion: String,
            pageContext: Object,
        },
  methods: {
  },
  mounted() {},
  created() {
    //this.$Progress.start();
    //this.$Progress.finish();
  }
};
</script>
