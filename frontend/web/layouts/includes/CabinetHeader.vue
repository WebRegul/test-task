<template>
  <v-app-bar
    app
    clipped-left
  >
    <div>
      <v-app-bar-nav-icon class="d-md-none d-flex" @click="$emit('toggle-visible')">
        <v-icon v-if="!sidebar">mdi-menu</v-icon>
        <v-icon v-else>mdi-close</v-icon>
      </v-app-bar-nav-icon>

      <v-avatar
        color="primary"
        size="42"
        class="d-none d-md-flex"
      >
        <span class="white--text text-h7">logo</span>
      </v-avatar>
    </div>

    <v-spacer></v-spacer>

    <div>
      <div v-if="auth">
        <NuxtLink :to="item.url" v-for="(item, key) in userItems" :key="key" class="mr-6">
          <span v-if="item.title">{{ item.title }}</span>
          <v-icon v-if="item.icon">{{item.icon}}</v-icon>
        </NuxtLink>
        <NuxtLink to="/">
          <v-badge
            :value="notification"
            bordered
            top
            dot
            offset-x="10"
            offset-y="10"
          >
            <v-avatar
              size="35"
            >
              <v-icon large> mdi-account-circle </v-icon>
            </v-avatar>
          </v-badge>
        </NuxtLink>
      </div>

      <div v-else>
        <a @click.prevent="registrModal = true" href="" class="mr-2">Зарегистрироваться</a>
        <v-btn
          rounded
          color="primary"
          @click="loginModal = true"
        >Войти
        </v-btn>
      </div>
    </div>

  </v-app-bar>
</template>

<script>
  import {mapGetters} from "vuex";

  export default {
    name: "CabinetHeader",
    props: {
      sidebar: {
        type: Boolean,
        default: true
      },
      notification: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        userItems: [
          {title: '', url: '/', icon:'mdi-magnify'},
          {title: '', url: 'travel', icon:'mdi-cards-heart'},
          {title: 'Разместить объявление', url: 'create.basic'},
        ]
      }
    },
    computed: {
      ...mapGetters({
        role: 'cabinet/app/role',
      }),
      auth() {
        return this.role === 'client'
      }
    }
  }
</script>

<style scoped>

</style>
