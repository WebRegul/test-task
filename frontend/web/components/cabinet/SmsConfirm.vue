<template>
  <Dialog
    :value="visible"
    @close="$emit('close')"
    fullScreen
  >
    <template #content>
      <div class="d-flex flex-column align-center mt-16">
        <h3>На ваш номер был выслан смс-код для подтвержения регистрации</h3>
        <v-otp-input
          :color="errorMessage ? 'error' : null "
          length="4"
          class="col-3"
          v-model="code"
          @finish="verify"
        ></v-otp-input>

        <div class="d-flex flex-column align-center">
          <v-btn
            text
            class="text-capitalize text-decoration-underline"
            @click="resendSmsCode"
            :disabled="!!timer.id"
          >Запросить код повторно
          </v-btn>
          <span v-if="timer.id">можно через {{timer.value}} сек</span>
        </div>
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
    </template>
  </Dialog>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Dialog from "../base/Dialog";
import auth from "../../mixins/auth";

export default {
  name: "SmsConfirm",
  components: { Dialog },
  mixins: [ auth ],
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      code: null,
      timer: {
        initValue: 60,
        value: 60,
        id: null,
      },
    }
  },
  computed: {
    ...mapGetters({
      userModel: 'cabinet/app/userModel',
    }),
  },
  methods: {
    ...mapActions({
      smsCodeSend: 'cabinet/app/smsCodeSend',
      smsCodeVerify: 'cabinet/app/smsCodeVerify',
    }),
    timerStart() {
      this.timer.id = setInterval(() => {
        if (this.timer.value > 0) {
          this.timer.value = this.timer.value - 1;
        }else{
          clearInterval(this.timer.id);
          this.timer.id = null;
          this.timer.value = 10;
        }
      }, 1000);

    },
    verify() {
      this.smsCodeVerify({
        code: this.code,
        user_id: this.userModel
      }).then( () => {
        this.$emit('close');
        this.$router.push({ name: 'cabinet' })
      }).catch( (e) => {
        const error = e.response.data
        this.error = true;
        this.errorMessage =  error.message ? error.message : 'Произошла неизвестная ошибка, попробуйте позже';
      });
      this.code = null;
    },
    resendSmsCode() {
      this.smsCodeSend(this.userModel);
      this.timerStart();
    }

  },
  watch: {
    visible(data) {
      if(data) this.resendSmsCode();
    }
  }
}
</script>

<style scoped>

</style>
