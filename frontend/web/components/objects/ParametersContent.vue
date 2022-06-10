<template>
  <div>
    <v-container>
      <h2>Основные параметры</h2>
      <div>
        <v-row>
          <v-col cols="4"
                 class="d-flex mt-2"
                 v-for="(item,key) in parametersForm.baseParams"
                 :key="key"
          >
            <v-text-field
              v-model="item.value"
              hide-details
              single-line
              outlined
              max="5"
              min="1"
              type="number"
              class="col-2 mr-2 text-center"
            />
            <span class="mr-2 align-self-center">{{item.name}}</span>
            <v-tooltip v-if="item.hint" right>
              <template v-slot:activator="{ on, attrs }">
                <v-icon
                  color="primary"
                  dark
                  v-bind="attrs"
                  v-on="on"
                >
                  mdi-information
                </v-icon>
              </template>
              <span>{{item.hint}}</span>
            </v-tooltip>
          </v-col>
        </v-row>
        <v-row >
          <v-select
            class="col-4 ml-3"
            outlined
            :items="parametersForm.baseParamsAdditional.list"
            v-model="parametersForm.baseParams.value"
          ></v-select>
        </v-row>
      </div>
      <!--Стоимость-->
      <h2 class="mt-7">Стоимость</h2>
      {{parametersForm.prices}}

      <div class="mt-7">
        <v-btn
          class="ma-2"
          outlined
          color="primary"
          @click="priceModal = true"
          v-if="parametersForm.prices ? !parametersForm.prices.length : null"
        >
          Добавить стоимость
        </v-btn>
        <v-container class="" v-else>
          <v-row justify="end">
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  @click="priceModal = true"
                >
                  <v-icon>mdi-plus-circle</v-icon>
                </v-btn>
              </template>
              <span>Удалить вариант цены</span>
            </v-tooltip>

          </v-row>
          <v-row
            v-for="(item, key) in parametersForm.prices"
            :key="key"
          >
            <span class="mt-4 mr-4"> <b>{{item.value}}</b> руб./ночь </span>
            <v-text-field
              v-if="item.nutririon"
              class="col-2 mr-4"
              outlined
              disabled
              v-model="item.nutrition"
            >
            </v-text-field>
            <span class="mt-4 mr-8">гостей до <b>{{item.max_guests}}</b> человек</span>

            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  v-bind="attrs"
                  v-on="on"
                  class="mt-2"
                  @click="parametersForm.price.splice(key, 1)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Удалить вариант цены</span>
            </v-tooltip>
          </v-row>
        </v-container>
      </div>
      <!--Удобства-->
      <h2 class="mt-7">Удобства</h2>
      <v-row class="mt-2">
        <v-col
          cols="6"
          v-for="(param, key) in parametersForm.comfort"
          :key="key"
        >
          <h3 class="mr-2">
            {{param.name}}
          </h3>
          <div
            class="d-flex"
            v-for="(item,key) in param.list"
            :key="key"
          >
            <v-checkbox
              :label="item.name"
              :value="item.value"
              class="mr-2"
            ></v-checkbox>
            <v-tooltip v-if="item.hint" right>
              <template v-slot:activator="{ on, attrs }">
                <v-icon
                  color="primary"
                  dark
                  v-bind="attrs"
                  v-on="on"
                >
                  mdi-help-circle
                </v-icon>
              </template>
              <span>{{item.hint}}</span>
            </v-tooltip>
          </div>
        </v-col>
      </v-row>
    </v-container>
    <!--Модал-->
    <Dialog
      :value="priceModal"
      title="Добавление варианта стоимости"
      @close="priceModal = false"
    >
      <template v-slot:content>
        <v-container>
          <v-row class="mt-4">
            <v-text-field
              outlined
              v-model="priceForm.value"
              :rules="priceForm.valueRules"
              class="col-6 mr-3"
            ></v-text-field>
            <span class="mt-4">руб./ночь</span>
          </v-row>
          <v-row class="mt-2">
            <v-select
              outlined
              :items="food"
              v-model="priceForm.foodParam"
              class="col-6 mr-3"
            ></v-select>
          </v-row>
          <v-row class="mt-2">
            <div class="d-flex align-center col-6 pl-0 pr-0 pt-0">
              <span>гостей, до</span>
              <v-spacer></v-spacer>
              <v-text-field
                v-model="priceForm.guests"
                hide-details
                single-line
                outlined
                max="5"
                min="0"
                type="number"
                class="col-4"
              />
            </div>
          </v-row>
          <v-row justify="center" class="mt-5">
            <v-btn
              outlined
              color="primary"
              class="d-flex justify-center"
              @click="addPrice"
            >
              <v-icon left>mdi-check-bold</v-icon>
              Добавить
            </v-btn>
          </v-row>

        </v-container>
      </template>
    </Dialog>
  </div>
</template>

<script>
import {mapGetters} from "vuex";
import Dialog from "../base/Dialog";

  export default {
    name: "ParametersContent",
    components: { Dialog },
    data() {
      return {
        priceModal: false,
        priceForm: {
          value: 0,
          nutrition: '',
          max_guests: 0,
          valueRules: [v => (v <= 999999 && v >= 100) || 'Введена некорректная стоимость']
        },
        food: ['Завтрак включен', 'Без питания'],
        parametersForm: {
          baseParams: [
            { name: 'Всего этажей', value: 1 },
            { name: 'Комнат', value: 1 },
            { name: 'Спальных мест', value: 1, hint: 'Текст подсказки' },
            { name: 'Этаж', value: 1 },
            { name: 'Площадь', value: 1 },
            { name: 'Кроватей', value: 1 },
          ],

          baseParamsAdditional: {
            list: ['Вид из окна'],
            value: null
          },

          prices: [],

          comfort: [
            {
              name: 'Основные',
              list: [
                { name: 'Телевизор', value: 1 },
                { name: 'Рабочая зона', value: 2 },
                { name: 'Кабельное ТВ', value: 3 },
                { name: 'Балкон, лоджия', value: 4 },
              ],
              value: ''
            },
            {
              name: 'Кухня',
              list: [
                { name: 'Кухня', value: 1 },
                { name: 'Общая кухня на этаже', value: 2 },
                { name: 'Кухонные принадлежности', value: 3 },
                { name: 'Стиральная машина', value: 4 },
                { name: 'Вода, кофе/чай', value: 5 },
              ],
              value: ''
            },
            {
              name: 'Гигиена',
              list: [
                { name: 'Душ', value: 1 },
                { name: 'Ванна', value: 2 },
                { name: 'Полотенца', value: 3 },
                { name: 'Фен', value: 4 },
              ],
              value: ''
            },
            {
              name: 'Эксклюзив',
              list: [
                { name: 'Камин', value: 1 },
                { name: 'Электрический камин', value: 2 },
                { name: 'Джакузи', value: 3, hint: 'Текст подсказок' },
                { name: 'Личный сейф', value: 4, hint: 'Текст подсказок' },
                { name: 'Звукоизоляция', value: 5, hint: 'Текст подсказок' },
              ],
              value: ''
            },
          ]
        }
      }
    },
    computed: {
      ...mapGetters({
        objectItem: 'cabinet/objects/objectItem',
      }),
    },
    methods: {
      addPrice() {
        this.parametersForm.prices.push(this.priceForm);
        this.priceModal = false;
      }
    }

  }
</script>

<style scoped>

</style>
