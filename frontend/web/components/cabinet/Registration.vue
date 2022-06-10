<template>
  <Dialog
    :value="visible"
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
          <v-text-field
            v-model="registrForm.name"
            label="Имя"
            required
            solo
          ></v-text-field>
          <v-text-field
            v-model="registrForm.surname"
            label="Фамилия"
            required
            solo
          ></v-text-field>
          <phone-input v-model="registrForm.login" title="Телефон" required />
          <v-text-field
            v-model="registrForm.password"
            label="Пароль"
            required
            solo
            type="password"
          ></v-text-field>
          <v-checkbox
            v-model="registrForm.offert"
            color="primary"
            :label="offertLabel"
          ></v-checkbox>
          <div class="mt-8 text-center">
            <v-btn
              type="submit"
              color="primary"
              width="100%"
              :disabled="disabled || !registrForm.offert"
            >
              Зарегистрироваться
            </v-btn>
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
  name: "Registration",
  components: { Dialog , PhoneInput },
  mixins: [ auth ],
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      offertLabel: 'Я ознакомился и подтверждаю принятие условий',
      registrForm: {
        name: null,
        surname: null,
        login: null,
        password: null,
        offert: false
      },
    }
  },
  methods: {
    ...mapActions({
      registration: 'cabinet/app/registration'
    }),
    submit() {
      this.disabled = true;

      this.registration(this.registrForm).then( () => {
        this.disabled = false;
      }).catch( e => {
        const error = e.response.data
        this.disabled = false;
        this.error = true;
        this.errorMessage =  error.message ? error.message : 'Произошла неизвестная ошибка, попробуйте позже';
      })
    }
  }
}
</script>

<style scoped>

</style>
