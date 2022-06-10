<template>
  <Dialog
    :value="visible"
    title="Вход"
    @close="$emit('close')"
    fullScreen
  >
    <template #content>
      <div class="d-flex justify-center mt-10">
        <v-form
          ref="form"
          lazy-validation
          class=" col-3"
          @submit.prevent="submit"
        >
          <phone-input v-model="loginForm.login" title="Телефон" required />
          <v-text-field
            v-model="loginForm.password"
            label="Пароль"
            required
            solo
            type="password"
          ></v-text-field>
          <a href="">Забыли пароль?</a>
          <div class="mt-8 text-center">
            <v-btn
              type="submit"
              color="primary"
              width="100%"
              :disabled="disabled"
            >
              <span v-if="!loading">Войти</span>
              <v-progress-circular
                v-else
                indeterminate
                color="white"
              ></v-progress-circular>
            </v-btn>
          </div>
          <div class="mt-4 text-center">
            Нет аккаунта? <a href="" @click.prevent="$emit('registration')">Зарегистрироваться</a>
          </div>
          <v-snackbar
            :timeout="5000"
            :value="error"
            color="red accent-2"
            @input="resetError"
          >
            <strong>{{ errorMessage }}</strong>
            <template v-slot:action="{ attrs }">
              <v-btn
                color="white"
                text
                v-bind="attrs"
                @click="error = false"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </template>
          </v-snackbar>
        </v-form>
      </div>
    </template>
  </Dialog>
</template>

<script>
import {mapActions} from "vuex";
import Dialog from "../base/Dialog";
import PhoneInput from "../base/PhoneInput";
import auth from "../../mixins/auth";

export default {
  name: "Login",
  components: { Dialog, PhoneInput },
  mixins: [ auth ],
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      loginForm: {
        login: null,
        password: null
      },
    }
  },
  methods: {
    ...mapActions({
      login: 'cabinet/app/login'
    }),
    submit() {
      this.loading = true;

      this.login(this.loginForm).then( () => {
        this.$emit('close');
        this.$router.push({ name: 'cabinet' })
      }).catch( (e) => {
        const error = e.response.data
        this.loading = false;
        this.error = true;
        this.errorMessage =  error.message ? error.message : 'Произошла неизвестная ошибка, попробуйте позже';
      })
    }
  }
}
</script>

<style scoped>

</style>
