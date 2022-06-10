<template>
  <v-app light>
    <base-header search />
    <full-search-form></full-search-form>
    <v-main app class="pa0">
      <Nuxt />
    </v-main>
  </v-app>
</template>

<script>
import FullSearchForm from '../components/forms/search/FullSearchForm'
import BaseHeader from './includes/BaseHeader'
export default {
  name: 'SearchLayout',
  components: { FullSearchForm, BaseHeader },
  data() {
    return {
      clipped: false,
      drawer: false,
      fixed: false,
      items: [
        {
          icon: 'mdi-apps',
          title: 'Welcome',
          to: '/',
        },
        {
          icon: 'mdi-chart-bubble',
          title: 'Inspire',
          to: '/inspire',
        },
      ],
    }
  },
  computed: {
    isFullPage() {
      return (
        this.$route.matched.map((r) => {
          return r.components.default.options.isFullPage
        })[0] || false
      )
    },
  },
}
</script>
<style></style>
