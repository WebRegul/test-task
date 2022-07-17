<template>
  <v-app-bar
    color="beige"
    app
    height="80px"
    class="align-baseline justify-sm-center align-content-center color-header"
  >
    <v-app-bar-nav-icon class="hidden-md-and-up"></v-app-bar-nav-icon>
    <v-btn icon class="ml-3 hidden-xl-and-up hidden-sm-and-down">
      <img src="~assets/images/svg/logo.svg" />
    </v-btn>
    <v-spacer />
    <big-search-form> </big-search-form>
    <v-spacer />

    <v-btn
      filled
      solo
      outlined
      plain
      raised
      text
      width="186px"
      height="48px"
      class="mr-md-5 text-capitalize color-btn hidden-xl-and-up hidden-sm-and-down"
      >Вход</v-btn
    >
    <v-btn
      class="text-capitalize color-btn hidden-xl-and-up hidden-sm-and-down"
      outlined
      plain
      raised
      text
      width="187px"
      height="48px"
      >Регистрация</v-btn
    >
  </v-app-bar>
</template>

<script>
import { mdiBiohazard } from '@mdi/js'
import { mapGetters } from 'vuex'
import BigSearchForm from '../../components/forms/search/BigSearchForm'

export default {
  name: 'BaseHeader',
  components: { BigSearchForm },
  props: {
    search: {
      type: Boolean,
      default: false,
    },
  },
  data: () => ({
    logo: mdiBiohazard,
    title: 'Понаехали!',
  }),
  computed: {
    ...mapGetters({}),
  },
  methods: {
    toggleVisible(type, value) {},
  },
}
</script>

<style>
.color-header {
  margin-top: 100px;
}
.theme--light.v-app-bar.v-toolbar.v-sheet.color-header {
  background: #ebebec;
}
.color-btn {
  color: #00aca2 !important;
  border-radius: 16px;
}
.theme--light.v-btn.v-btn--outlined.v-btn--text {
  border-color: #00aca2;
}
</style>
