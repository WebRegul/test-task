<template>
  <v-app >
    <cabinet-header />
    <constructor-header :next-page="activeItem + 1" />

    <v-navigation-drawer
      app
      clipped
      hide-overlay
      color="grey lighten-2"
      width="300"
    >
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
              <v-list-item-title>{{item.title}}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          </NuxtLink>
        </v-list-item-group>
      </v-list>

      <v-container class="pt-2">
        <div
          class="pl-1 pb-2"
        >
          <span :class="{ 'text--disabled' : !stepParams.save}">Календарь и бронирования</span>
        </div>
        <v-divider></v-divider>
        <v-container class="text">
          <v-row class="mb-2">
            <v-switch
              v-model="published"
              inset
              label="Не опубликовано"
              class="mr-2"
            ></v-switch>
            <v-tooltip right>
              <template v-slot:activator="{ on, attrs }">
                <v-icon
                  color="#5F5F61"
                  dark
                  v-bind="attrs"
                  v-on="on"
                >
                  mdi-information
                </v-icon>
              </template>
              <span>Заполните параметры и сохраните объект, чтобы его опубликовать</span>
            </v-tooltip>
          </v-row>

          <v-alert
            dense
            border="left"
            type="warning"
            color="orange"
            text
          >
            Объект не опубликован и гости его не видят. Заполните информацию о вашем предложении и опубликуйте его, чтобы начать привлекать гостей
          </v-alert>
          <v-alert
            dense
            border="left"
            type="error"
            color="red"
            text
            class=""
          >
            <strong>Объявление отклонено модератором</strong>
            <br>
            Для его публикации исправьте ошибки и отправьте на повторную модерацию
          </v-alert>
        </v-container>
      </v-container>
    </v-navigation-drawer>

    <v-main>
      <v-container>
        <Nuxt />
      </v-container>
    </v-main>

    <cabinet-footer />
  </v-app>
</template>

<script>
  import {mapGetters} from "vuex";
  import CabinetHeader from "./includes/CabinetHeader";
  import CabinetFooter from "./includes/CabinetFooter";
  import ConstructorHeader from "./includes/ConstructorHeader";

  export default {
    name: "ObjectsConstructorLayout",
    components: { CabinetHeader, CabinetFooter, ConstructorHeader },
    data() {
      return {
        published: false
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
      currentPageType() {
        return this.$route.path.includes('create') ? 'objects-create' :
          this.$route.path.includes('edit') ? 'objects-edit' :
          null;
      }
    },

  }
</script>

<style scoped>

</style>
