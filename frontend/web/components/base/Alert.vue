<template>
  <v-alert
    :type="variant"
  >
    {{content}}
  </v-alert>
</template>

<script>
    export default {
        name: "Alert",
      props: {
        variant: {
          type: String,
          default: 'success'
        },
        content: {
          type: String,
          default: ''
        },
      },
    }
</script>

<style scoped>

</style>
