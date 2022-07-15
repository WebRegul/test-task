<template>
  <v-footer app bottom fixed padless inset>
    <v-row justify="center" no-gutters>
      <v-col class="py-2 text-center white--text" cols="12">
        <v-btn
          v-for="link in links"
          :key="link"
          text
          rounded
          class="my-2 text-capitalize"
        >
          {{ link }}
        </v-btn>
      </v-col>
      <v-divider inset class="hr-color"></v-divider>

      <v-col class="py-1 text-center white--text" cols="12">
        <v-btn icon href="/fooRoute" target="_blank" class="mr-4">
          <img src="~assets/images/svg/facebook.svg" />
        </v-btn>
        <v-btn icon href="/fooRoute" target="_blank" class="mr-4">
          <img src="~assets/images/svg/tweeter.svg" />
        </v-btn>
        <v-btn icon href="/fooRoute" target="_blank">
          <img src="~assets/images/svg/telegram.svg" />
        </v-btn>
      </v-col>

      <v-col class="py-1 text-center" cols="12">
        {{ new Date().getFullYear() }} —
        <strong>2022 © ООО “Понаехали” </strong>
      </v-col>
    </v-row>
  </v-footer>
</template>

<script>
export default {
  name: 'MainFooter',
  data: () => ({
    icons: ['mdi-facebook', 'mdi-twitter', 'mdi-telegram'],
    links: ['О проекте', 'Руководство', 'Правила', 'Помощь'],
  }),
}
</script>

<style scoped>
.hr-color {
  height: 1px;
  background: #5f5f61;
}
</style>
