<template>
  <div class="object__item d-flex">
    <NuxtLink :to="{name: 'objects-edit-basic', params: { id: object.id }}">
      <div class="object__img" v-if="object.img">
        <v-icon size="250">mdi-image-size-select-actual</v-icon>
      </div>
      <img v-else class="object__img" src="https://englishonlinetests.com/wp-content/uploads/2021/02/scale_1200.jpg" :title="object.title" alt="">
    </NuxtLink>

    <div  class="object__content">
      <div class="object__header">
        <span class="object__status">
          В работе
        </span>
      </div>
      <div class="object__info">
        <NuxtLink :to="{name: 'objects-edit-basic', params: { id: object.id }}">{{object.title}}</NuxtLink>
        <h5>{{ object.address }}</h5>
        <div class="object__estimate">
          <div class="left">
            <v-icon>mdi-star</v-icon>
            <span class="mr-2">{{ object.rating.value }}</span>
            <span>( {{ object.reviews.count  | word_case(['отзыв', 'отзыва', 'отзывов']) }}  ) </span>
          </div>
          <div class="object__price--md">
            <span v-if="object.price.type === 'fixed'"> <b>{{object.price.value | currency }}</b>  / за ночь </span>
            <span v-else>от <b>{{object.price.value | currency }}</b>  / за ночь </span>
          </div>
        </div>
      </div>
      <div class="object__bottom">
        <div class="object__data">Дата последнего редактирования: {{ object.updated_at | formatDate }}</div>
        <div class="object__price">
          <span v-if="object.price.type === 'fixed'"> <b>{{object.price.value | currency }}</b>  / за ночь </span>
          <span v-else>от <b>{{object.price.value | currency }}</b>  / за ночь </span>
        </div>
      </div>

      <div class="object__actions">
        <v-btn class="btn" color="primary">
          Опубликовать
        </v-btn>
        <v-menu
          bottom
          left
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              dark
              icon
              v-bind="attrs"
              v-on="on"
              color="black"
            >
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>

          <v-list>
            <v-list-item
              v-for="(item, i) in menu"
              :key="i"
            >
              <a v-if="item.action" @click.prevent="save(object.url)" >{{ item.title }}</a>
              <NuxtLink v-else :to="{name: item.url, params: { id: object.id }}" :target="$device.isDesktop ? item.target : null">{{ item.title }}</NuxtLink>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ObjectItem",
  props: {
    object: {
      type: Object,
      default: () => {}
    },
  },
  data() {
    return {
      menu: [
        {title: 'Редактировать', url: 'objects-edit-basic'},
        {title: 'Просмотр объявления', url: 'objects-preview', target: '_blank'},
        {title: 'Скопировать ссылку на объявление', action: 'save'},
      ]
    }
  },
  methods: {
    save(url) {
      navigator.clipboard.writeText(url);
      this.$store.commit('cabinet/app/setSnackBar', {
        show: true,
        content: 'Ссылка скопирована',
        type: 'success'
      })
    }
  }
}
</script>

<style scoped>

</style>
