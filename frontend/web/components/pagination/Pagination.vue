<template>
  <div class="text-center mb-16 pb-4 pagination__margin">
    <v-pagination
      v-model="page"
      :length="3"
      circle
      color="#00ACA2"
      class="mb-16"
    ></v-pagination>
  </div>
</template>

<script>
export default {
  name: 'Pagination',
  data() {
    return {
      page: 1,
    }
  },
}
</script>
<style></style>
