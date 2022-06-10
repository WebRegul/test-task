<template>
  <v-app-bar
    app
    flat
    clipped-left
    color="grey lighten-2"
    class="cab-constructor__header"
    height="90"
  >
    <v-row class="mt-4 pl-2 pb-4">
      <v-col cols="2" class="pl-0 pr-0">
        <v-btn
          small
          color="white"
          @click="checkStep"
        >
          <v-icon
            left
            dark
          >
            mdi-arrow-left-circle
          </v-icon>
          Вернуться к списку
        </v-btn>
      </v-col>
      <v-col cols="10" class="right d-flex flex-column">
        <div class="d-flex justify-end">
          <a href="/cabinet/objects/1/preview" class="mr-4">
            <v-icon
              left
              dark
              color="blue"
            >
              mdi-link-variant
            </v-icon>
            Скопировать ссылку на объявление</a>
          <v-btn
            small
            class="mr-4"
            color="white"
          >
            <v-icon
              left
              dark
            >
              mdi-content-save
            </v-icon>
            Сохранить
          </v-btn>
          <v-btn
            v-if="!lastStep"
            small
            class="mr-4"
            color="primary"
            :to="menu[nextPage].url"
          >
            Далее

          </v-btn>
          <v-btn
            v-else
            small
            color="primary"
          >
            Сохранить и опубликовать
            <v-icon
              right
              color="white"
            >
              mdi-check-bold
            </v-icon>
          </v-btn>
        </div>
        <div class="pb-2">
          <a href="/cabinet/objects/1/preview" class="mr-4">
            <v-icon
              left
              dark
              color="blue"
            >
              mdi-eye
            </v-icon>
            Предварительный просмотр</a>
        </div>

      </v-col>
    </v-row>

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
  </v-app-bar>
</template>

<script>
  import {mapGetters} from "vuex";
  import Dialog from "../../components/base/Dialog";

  export default {
    name: "ConstructorHeader",
    components: { Dialog },
    props: {
      nextPage: {
        type: Number,
      }
    },
    data() {
      return {
        backModal: false
      }
    },
    computed: {
      ...mapGetters({
        stepParams: 'cabinet/app/stepParams',
        menu: 'cabinet/objects/menuItems',
      }),
      lastStep() {
        return this.$route.meta.step === this.menu.length;
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
