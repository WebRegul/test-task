<template>
  <v-app id="inspire">
    <cabinet-header
      :sidebar="drawer"
      :notification="alert.show"
      @toggle-visible="drawer = !drawer"
    />
    <v-navigation-drawer
      v-model="drawer"
      app
      clipped
      hide-overlay
      :mobile-breakpoint="sidebarBreakpoint"
      class="cab-sidebar"
      width="300"
    >
      <v-card
        color="grey lighten-4"
        outlined
        class="pt-6 pb-6"
      >
        <v-row justify="center">
          <v-avatar
            class="d-none d-md-flex mt-4"
            size="64"
          >
            <v-icon   x-large
            > mdi-account-circle  </v-icon>
          </v-avatar>
        </v-row>
        <v-row justify="center">
          <v-icon
            color="primary"
            class="mr-2"> mdi-exit-to-app </v-icon>
          <NuxtLink to="/"> Выход </NuxtLink>
        </v-row>
      </v-card>

      <v-divider></v-divider>

      <v-list>
        <v-list-item-group
          color="primary"
          v-model="activeItem"
        >
          <NuxtLink
            v-for="(item, key) in menu"
            :to="{name: item.url}"
            :key="key"
            :active="true"
          >
            <v-list-item class="v-item--active">
              <v-list-item-icon>
                <v-icon>{{ item.icon }}</v-icon>
              </v-list-item-icon>

              <v-list-item-content>
                  <v-list-item-title class="v-item--active">{{ item.title }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </NuxtLink>
        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <alert
        v-if="alert.show"
        :variant="alert.type"
        :content="alert.content"
        class="mb-0"
      />
      <v-snackbar
        :timeout="snackBar.timeout"
        :value="snackBar.show"
        :color="snackBar.type"
        @input="resetSnackBar"
        right
      >
        <strong>{{snackBar.content}}</strong>
        <template v-slot:action="{ attrs }">
          <v-btn
            color="white"
            text
            v-bind="attrs"
            @click="resetSnackBar"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </template>
      </v-snackbar>
      <v-container fluid >
        <Nuxt />
      </v-container>
    </v-main>
    <cabinet-footer />
  </v-app>
</template>


<script>
  import { mapActions, mapGetters } from "vuex";

  import Alert from "../components/base/Alert";
  import CabinetHeader from "./includes/CabinetHeader";

  import CabinetFooter from "./includes/CabinetFooter";

  export default {
    name: 'CabinetLayout',
    components: { CabinetHeader, CabinetFooter, Alert },
    data() {
      return {
        cards: ['Today', 'Yesterday'],
        drawer: null,
        sidebarBreakpoint: 961,
        links: [
          ['mdi-inbox-arrow-down', 'Пункт меню'],
          ['mdi-send', 'Пункт меню'],
          ['mdi-delete', 'Пункт меню'],
          ['mdi-alert-octagon', 'Пункт меню'],
        ],
      }
    },
    computed: {
      ...mapGetters({
        alert: 'cabinet/app/globalAlert',
        menu: 'cabinet/app/menuItems',
        snackBar: 'cabinet/app/snackBar',
      }),
      activeItem() {
        return this.menu.indexOf(this.menu.filter(item => this.$route.name.includes(item.url))[0]);
      }
    },
    methods: {
      ...mapActions({
        showAlert: 'cabinet/app/alert'
      }),
      resetSnackBar() {
        this.$store.commit('cabinet/app/setSnackBar', { show: false })
      },
    },
    mounted() {
      this.showAlert({
          show: true,
          type: 'success',
          content: 'Уведомление'
        })
    }
  }
</script>

<style scoped>

</style>
