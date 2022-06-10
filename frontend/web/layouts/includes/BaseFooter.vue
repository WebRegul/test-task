<template>
  <v-footer padless dark>
    <v-row justify="center" no-gutters>
      <v-col class="py-4 text-center white--text" cols="12">
        <v-btn
          v-for="link in links"
          :key="link"
          color="white"
          text
          rounded
          class="my-2"
        >
          {{ link }}
        </v-btn>
      </v-col>
      <v-btn v-for="icon in icons" :key="icon" class="mx-4 white--text" icon>
        <v-icon size="24px">{{ icon }}</v-icon>
      </v-btn>

      <v-col class="accent darken-2 py-4 text-center white--text" cols="12">
        Выше будут какие-то ссылки
      </v-col>
      <v-col class="py-4 text-center white--text" cols="12">
        {{ new Date().getFullYear() }} — <strong>ООО "Рога и Копыта"</strong>
      </v-col>
    </v-row>
  </v-footer>
</template>

<script>
export default {
  name: 'BaseFooter',
  data: () => ({
    icons: ['mdi-facebook', 'mdi-twitter', 'mdi-linkedin', 'mdi-instagram'],
    links: [
      'Главная',
      'Поиск',
      'Войти',
      'Куда-то пойти',
      'Регистрация',
      'Контакты',
    ],
  }),
}
</script>

<style scoped></style>
