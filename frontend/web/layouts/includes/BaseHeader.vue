<template>
  <v-app-bar
    dark
    app
    class="align-baseline justify-sm-center align-content-center"
  >
    <v-btn icon color="red" class="ml-5">
      <v-icon x-large>{{ logo }}</v-icon>
    </v-btn>
    <v-toolbar-title v-text="title" />
    <v-spacer />
    <small-search-form
      v-if="search"
      class="align-content-center mt-6"
      short
    ></small-search-form>
    <v-spacer />
    <v-btn filled rounded solo outlined plain raised text class="mr-md-2"
      >Войти</v-btn
    >
    <v-btn color="accent" outlined plain raised rounded text>Регистрация</v-btn>
  </v-app-bar>
</template>

<script>
import { mdiBiohazard } from '@mdi/js'
import {mapGetters} from "vuex";
import SmallSearchForm from '../../components/forms/search/SmallSearchForm'
import Registration from "../../components/cabinet/Registration";
import SmsConfirm from "../../components/cabinet/SmsConfirm";
import Login from "../../components/cabinet/Login";


export default {
  name: 'BaseHeader',
  components: { SmallSearchForm},
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
    ...mapGetters({

    }),
  },
  methods: {
    toggleVisible(type, value) {

    }
  }
}
</script>

<style scoped></style>
