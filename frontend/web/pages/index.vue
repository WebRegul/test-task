<template>
  <v-row justify="center" align="center">
    <v-col cols="12">
      <v-parallax
        height="900"
        dark
        class="darken-4"
        :src="require(`~/assets/images/background.jpg`)"
      >
        <v-overlay color="black" absolute dark>
          <v-row align="center" justify="center">
            <v-col class="text-center" cols="12">
              <h1 class="text-h4 font-weight-thin mb-4">
                Путешествуй, наслаждайся
              </h1>
              <h4 class="subheading">Твой любимый сервис бронирования жилья</h4>
            </v-col>
            <v-col cols="12">
              <small-search-form></small-search-form>
            </v-col>
          </v-row>
        </v-overlay>
      </v-parallax>
    </v-col>
  </v-row>
</template>

<script>
import SmallSearchForm from '../components/forms/search/SmallSearchForm'
export default {
  name: 'IndexPage',
  components: { SmallSearchForm },
  isFullPage: true,
}
</script>
