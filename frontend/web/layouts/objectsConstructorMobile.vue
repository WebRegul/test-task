<template>
  <v-app>
    <v-app-bar
      app
      flat
      clipped-right
      color="grey lighten-2"
    >
      <v-btn
        @click="checkStep"
        text
        class="pl-0 pr-0"
      >
        <v-icon>mdi-arrow-left</v-icon>
      </v-btn>

      <div class="d-flex flex-column ml-6">
        <v-row class="subtitle-2">Создание объявления</v-row>
        <v-row class="subtitle-2 font-weight-regular">Шаг {{$route.meta.step}} из {{menu.length}}</v-row>
      </div>

      <v-spacer></v-spacer>

      <v-app-bar-nav-icon @click="drawer = !drawer">
        <v-icon v-if="!drawer">mdi-dots-vertical</v-icon>
        <v-icon v-else>mdi-close</v-icon>
      </v-app-bar-nav-icon>


    </v-app-bar>
    <v-navigation-drawer
      :value="drawer"
      app
      clipped
      right
      hide-overlay
      color="grey lighten-2"
      class="cab-sidebar--mobile"
    >
      <div class="pa-2 d-flex align-center">
        <v-banner
          class="mr-2"
          color="primary"
        >
          Объявление опублкиовано
        </v-banner>
        <v-btn
          outlined
          color="primary"
        >
          Отменить публикацию
        </v-btn>
      </div>
      <v-divider></v-divider>
      <v-list class="pb-0">
        <v-list-item-group
          :value="activeItem"
          active-class="cab-constructor__sidebar--active"
        >
          <NuxtLink
            v-for="(item, key) in menu"
            :to="{ name: `${currentPageType}-${item.url}`, params: {id: $route.params.id} }"
            :key="key"
            :active="true"
          >
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>
                  <span class="mr-2">{{item.title}}</span>
                  <v-tooltip v-if="item.hint" right>
                    <template v-slot:activator="{ on, attrs }">
                      <v-icon
                        color="#5F5F61"
                        dark
                        v-bind="attrs"
                        v-on="on"
                      >
                        mdi-alert-circle
                      </v-icon>
                    </template>
                    <span>{{item.hint}}</span>
                  </v-tooltip>
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </NuxtLink>
        </v-list-item-group>
      </v-list>
      <div
        class="pl-4 pt-2 pb-2"
      >
        <span :class="{ 'text--disabled' : !stepParams.save}">Календарь и бронирования</span>
      </div>

      <v-divider></v-divider>
      <div class="d-flex flex-column pa-4">
        <a href="/cabinet/objects/1/preview">
          <v-icon
            left
            dark
            color="blue"
          >
            mdi-eye
          </v-icon>
          Предварительный просмотр</a>

        <a href="/cabinet/objects/1/preview" class="mt-2">
          <v-icon
            left
            dark
            color="blue"
          >
            mdi-link-variant
          </v-icon>
          Скопировать ссылку на объявление</a>
      </div>

      <template v-slot:append>
        <div class="pa-2">
          <p>Возникли проблемы с заполнением или публикацией объявления?</p>
          <a href="/team/support">Напишите нам в службу поддержки</a>
        </div>
      </template>
    </v-navigation-drawer>

  <v-main>
    <v-container>
      <Nuxt />
    </v-container>
  </v-main>

  <v-footer
    app
    height="50"
  >
    <v-row justify="space-between">
      <v-btn
        small
        text
        class="footer__btn"
        height="40"
        :disabled="$route.meta.step === 1"
        :to="back"
      >
        <div class="d-flex flex-column p-3">
          <v-icon> mdi-chevron-left </v-icon>
          <span> Назад </span>
        </div>
      </v-btn>

      <v-btn
        small
        text
        class="footer__btn"
        height="40"
      >
        <div class="d-flex flex-column p-3">
          <v-icon> mdi-content-save </v-icon>
          <span> Сохранить </span>
        </div>
      </v-btn>

      <v-btn
        v-if="!lastStep"
        small
        text
        class="footer__btn"
        height="40"
        :disabled="!stepParams.save"
        :to="menu[activeItem + 1].url"
      >
        <div class="d-flex flex-column p-3">
          <v-icon> mdi-chevron-right </v-icon>
          <span> Далее </span>
        </div>
      </v-btn>
      <v-btn
        v-else
        small
        text
        class="footer__btn"
        height="40"
      >
        <div class="d-flex flex-column p-3">
          <v-icon> mdi-checkbox-marked-circle </v-icon>
          <span> Опубликовать </span>
        </div>
      </v-btn>
    </v-row>
  </v-footer>
  <!--Модал-->
  <Dialog
    :value="backModal"
    @close="backModal = false"
  >
    <template v-slot:content>
      <h4 class="mt-5 text-center">Вернуться к списку, сохранив изменения?</h4>
      <div class="d-flex justify-space-around mt-5" >
        <v-btn color="primary" to="/cabinet/objects" class="mr-2"
        >
          <v-icon left> mdi-content-save </v-icon>
          Да
        </v-btn>
        <v-btn color="white" @click="backModal = false"
        >
          <v-icon left> mdi-close </v-icon>
          Нет
        </v-btn>
      </div>
    </template>
  </Dialog>
</v-app>

</template>

<script>
  import {mapGetters} from "vuex";
  import Dialog from "../components/base/Dialog";

  export default {
    name: "objectsConstructorMobile",
    components: { Dialog },
    data() {
      return {
        drawer: false,
        backModal: false
      }
    },
    computed: {
      ...mapGetters({
        stepParams: 'cabinet/app/stepParams',
        menu: 'cabinet/objects/menuItems',
      }),
      activeItem() {
        return this.menu.indexOf(this.menu.filter(item => this.$route.name.includes(item.url))[0]);
      },
      lastStep() {
        return this.$route.meta.step === this.menu.length;
      },
      back() {
        const back = this.activeItem !== 0 ? this.activeItem - 1 : 0;
        return this.menu[back].url
      },
      currentPageType() {
        return this.$route.path.includes('create') ? 'objects-create' :
          this.$route.path.includes('edit') ? 'objects-edit' :
            null;
      }
    },
    methods: {
      checkStep() {
        if((this.stepParams.save && this.stepParams.edit) || (!this.stepParams.save && !this.stepParams.edit)) {
          this.$router.push('/cabinet/objects')
        } else {
          this.backModal = true
        }
      }
    }
  }
</script>

<style scoped>

</style>
