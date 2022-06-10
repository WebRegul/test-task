<template>
  <v-container fluid pa-0 class="search__container mt-3">
    <v-row class="search__container__row">
      <v-col cols="12" md="6" lg="5" xl="4" class="pr-md-1">
        <v-list>
          <v-list-item v-for="i in 10"
                       :key="i"
                       class="pa-0">
            <object-card></object-card>
          </v-list-item>
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
import SearchMap from '../../components/maps/SearchMap'

export default {
  name: 'SearchPage',
  components: { SearchMap, ObjectCard },
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
      map: 'web/search/getPoints'
    }),
    updateMapBoundsHandler(bounds){
      console.log('updateMapBoundsHandler', bounds);
      this.map(bounds);
    }
  },
}
</script>

<style scoped>
.search__container {
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
