<template>
  <v-container fluid pa-0 class="search__container mb-16 mr-5 ml-5">
    <v-row class="search__container__row">
      <v-col
        cols="12"
        md="6"
        lg="5"
        xl="4"
        class="pr-md-1 pt-0"
        color="#D3D3D4"
      >
        <v-list color="#D3D3D4" class="pt-0">
          <v-list-item v-for="i in 10" :key="i" class="pa-0 mb-6">
            <object-card></object-card>
          </v-list-item>
          <pagination></pagination>
        </v-list>
      </v-col>

      <v-col
        cols="12"
        md="6"
        lg="7"
        xl="8"
        class="flex-column align-end pa-0 search__container__row__map"
      >
        <search-map @update:bounds="updateMapBoundsHandler"></search-map>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions } from 'vuex'

import ObjectCard from '../../components/cards/ObjectCard'
import Pagination from '../../components/pagination/Pagination'
import SearchMap from '../../components/maps/SearchMap'

export default {
  name: 'SearchPage',
  components: { SearchMap, ObjectCard, Pagination },
  layout: 'search',
  data() {
    return {}
  },
  computed: {},
  mounted() {
    this.list()
  },
  methods: {
    ...mapActions({
      list: 'web/search/getList',
      map: 'web/search/getPoints',
    }),
    updateMapBoundsHandler(bounds) {
      console.log('updateMapBoundsHandler', bounds)
      this.map(bounds)
    },
  },
}
</script>

<style scoped>
.search__container {
  background: #d3d3d4;
  height: calc(100vh - 64px);
}
.search__container__row {
  height: 100%;
}
.search__container__row__map {
  position: fixed;
  right: 0px;
  height: 100%;
}
</style>
