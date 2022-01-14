<template>
  <nav class="float-right">
    <ul class="pagination">
      <template v-for="link in links">
        <li v-if="link.active" class="page-item active" aria-current="page"><span class="page-link" v-html="link.label"></span></li>
        <li v-else class="page-item"><a href="javascript:void(0)" class="page-link" @click="getResults(link.url)" v-html="link.label"></a></li>
      </template>
    </ul>
  </nav>
</template>

<script>
export default {
  props: {
    links: Array
  },
  methods: {
    getResults(url) {
      this.$parent.getResults(url);
    }
  }
}
</script>

<style scoped>

</style>