<template>
  <v-app light>
    <main-header class="mb-4 mt4 color-header header-height" search />
    <full-search-form></full-search-form>
    <v-main app class="pa0">
      <Nuxt />
    </v-main>
    <main-footer></main-footer>
  </v-app>
</template>

<script>
import FullSearchForm from '../components/forms/search/FullSearchForm'
import MainHeader from './includes/MainHeader'
import MainFooter from './includes/MainFooter.vue'
export default {
  name: 'SearchLayout',
  components: { FullSearchForm, MainHeader, MainFooter },
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
<style>
.theme--light.v-app-bar.v-toolbar.v-sheet {
  background-color: #ebebec;
}
.header-height {
  margin-top: 20px;
  margin-bottom: 20px;
}
.v-main__wrap {
  background: #d3d3d4;
}
</style>
