<template>
  <v-app light>
    <base-header />
    <v-main>
      <v-container :fluid="isFullPage" pa-0>
        <Nuxt />
      </v-container>
    </v-main>
    <base-footer />
  </v-app>
</template>

<script>
import BaseFooter from './includes/BaseFooter'
import BaseHeader from './includes/BaseHeader'
export default {
  name: 'DefaultLayout',
  components: { BaseHeader, BaseFooter },
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
