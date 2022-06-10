<template>
  <div>
    <v-row>
      <text-input
        v-model="title"
        title="Название объявления"
        hint="Опишите кратко ваше объявление. Например: Уютная трехкомнатная квартира в центре"
        with-counter
        :max-counts="100"
      />
    </v-row>
    <v-row class="d-flex align-items-center">
      <v-col class="mr-2 pl-0 pr-0">
        <v-select
          label="Тип жилья"
          class="w-50 mt-3 mr-3"
          :items="params ? params.building_types : null"
          item-text="title"
          item-value="id"
          outlined
        ></v-select>
      </v-col>

      <v-col class="pl-0 pr-0 d-flex">
        <div class="mr-3 align-self-center">Вид аренды*</div>
        <v-radio-group
          v-model="form.houseType.value"
          mandatory
          row
          class="align-self-center"
        >
          <v-radio
            v-for="(item, key ) in form.houseType.list"
            :key="key"
            :label="item.title"
            :value="item.value"
          ></v-radio>
        </v-radio-group>
      </v-col>
    </v-row>

    <v-row>
      <span class="mr-2 align-self-center">Кол-во звезд:</span>
      <v-text-field
        v-model="object.stars"
        hide-details
        single-line
        outlined
        max="5"
        min="1"
        type="number"
        class="col-1"
      />
    </v-row>
    <v-row class="mt-6">
      <area-input
        v-model="object.description"
        title="Описание"
        hint="Опишите детально ваше предложение. Расскажите, чем вы можете быть интересны гостям"
        with-counter
        :max-counts="5000"
      />
    </v-row>
    <h3 class="mt-4">Адрес</h3>
    <v-row>
      <v-col cols="6">
        <v-select
          :items="form.clock"
          label="Страна"
          outlined
          class="mt-3 mr-3"
        ></v-select>

        <v-row>
          <v-col cols="6">
            <v-text-field
              outlined

              label="Корпус"
              required
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              outlined

              label="Строение"
              required
            ></v-text-field>
          </v-col>
        </v-row>
      </v-col>
      <v-col cols="6">
        <v-sheet
          class="d-flex"
          color="teal lighten-3"
          height="150"
        >  </v-sheet>
      </v-col>
    </v-row>
    <h2>Правила размещения</h2>
    <v-container>
      <v-row >
        <!--Заезд-->
        <v-col cols="4">
          <div class="d-flex">
            <v-banner
              outlined
              class="banner align-self-center mr-3"
            >Заезд</v-banner>
            <div class="d-flex flex-column">
              <div class="d-flex align-center">
                <v-banner
                  class="banner--sm -3"
                  outlined
                >C</v-banner>
                <v-select
                  :items="form.clock"
                  label=""
                  outlined
                  class="w-50 mt-7"
                ></v-select>
              </div>
              <div class="d-flex align-center">
                <v-banner
                  class="banner--sm"
                  outlined
                >До</v-banner>
                <v-select
                  :items="form.clock"
                  label=""
                  outlined
                  class="w-50 mt-7"
                ></v-select>
              </div>
            </div>
          </div>
        </v-col>
        <!--Выезд-->
        <v-col cols="4">
          <div class="d-flex">
            <v-banner
              outlined
              class="banner align-self-center mr-3"
            >Выезд</v-banner>
            <div class="d-flex flex-column">
              <div class="d-flex align-center">
                <v-banner
                  class="banner--sm -3"
                  outlined
                >C</v-banner>
                <v-select
                  :items="form.clock"
                  label=""
                  outlined
                  class="w-50 mt-7"
                ></v-select>
              </div>
              <div class="d-flex align-center">
                <v-banner
                  class="banner--sm"
                  outlined
                >До</v-banner>
                <v-select
                  :items="form.clock"
                  label=""
                  outlined
                  class="w-50 mt-7"
                ></v-select>
              </div>
            </div>
          </div>
        </v-col>
      </v-row>
      <!--Правиила-->
      <v-row>
        <v-col
          cols="4"
          class="d-flex align-center mb-0 pb-0"
          v-for="(item, key) in form.placementRules.params"
          :key="key"
        >
          <v-switch
            v-model="item.value"
            inset
            :label="item.name"
            class="mr-1"
          ></v-switch>
          <v-tooltip right>
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
            <span>Заполните параметры и сохраните объект, чтобы его опубликовать</span>
          </v-tooltip>
        </v-col>
      </v-row>
      <!--Категории-->
      <v-row class="mt-7">
        <v-col
          cols="4"
          v-for="(param, key) in form.placementRules.conditions"
          :key="key"
        >

          <v-row>
            <h3 class="mr-2">
              {{param.name}}
            </h3>
            <v-tooltip right>
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
              <span>Заполните параметры и сохраните объект, чтобы его опубликовать</span>
            </v-tooltip>
          </v-row>
          <v-row>
            <v-radio-group v-model="param.value">
              <v-radio
                v-for="(item,key) in param.list"
                :key="key"
                :label="item.name"
                :value="item.value"
              ></v-radio>
            </v-radio-group>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
  import {mapGetters} from "vuex";
  import TextInput from "../base/TextInput";
  import AreaInput from "../base/AreaInput";

  export default {
    name: "BasicContent",
    components: { TextInput, AreaInput },
    props: {
      value: {
        type: Object,
        default: () => {}
      }
    },
    data() {
      return {
        form: {
          title: 'Название объявления',
          name: {
            hint: 'Опишите кратко ваше объявление. Например: Уютная трехкомнатная квартира в центре',
            counter: 100,
            rules: [v => (v ? v.length : null ) <= this.name.counter || `Максимум ${this.name.counter} символов`],
          },
          description: {
            value: '',
            title: 'Описание',
            hint: 'Опишите детально ваше предложение. Расскажите, чем вы можете быть интересны гостям',
            counter: 5000,
            rules: [v => (v ? v.length : null) <= this.description.counter || `Максимум ${this.description.counter} символов`],
          },
          clock: [
            '01.00',
            '02.00',
            '03.00',
            '04.00',
            '05.00',
            '06.00',
          ],
          apartmentTypes: [
            'Квартира',
            'Отель',
          ],
          houseType: {
            value: null,
            list: [
              {
                title: 'Целиком',
                value: 'full'
              },
              {
                title: 'Комната',
                value: 'room'
              },
              {
                title: 'Спальное место',
                value: 'bed'
              }
            ]
          },
          stars: 1,
          placementRules: {
            checkIn: {
              start: 12,
              end: 23,
            },
            exit: {
              start: 12,
              end: 23,
            },
            params: [
              { name: 'Гибкое время заезда/выезда', value: false},
              { name: 'Самостоятельное заселение', value: false},
              { name: 'Круглосуточная стойка регистрации', value: false},
              { name: 'Мгновенное бронирование', value: false},
              { name: 'Бесплатная отмена бронирования', value: false},
            ],
            conditions: [
              {
                name: 'Проведение вечеринок/мероприятий',
                list: [
                  { name: 'Проведение вечеринок/мероприятий строго запрещено', value: 1 },
                  { name: 'Проведение вечеринок/мероприятий по согласованию', value: 2 },
                  { name: 'Разрешено проведение вечеринок/мероприятий', value: 3 },
                ],
                value: ''
              },
              {
                name: 'Курение',
                list: [
                  { name: 'Проведение вечеринок/мероприятий строго запрещено', value: 1 },
                  { name: 'Проведение вечеринок/мероприятий по согласованию', value: 2 },
                  { name: 'Разрешено проведение вечеринок/мероприятий', value: 3 },
                ],
                value: ''
              },
              {
                name: 'Электронные сигареты',
                list: [
                  { name: 'Проведение вечеринок/мероприятий строго запрещено', value: 1 },
                  { name: 'Проведение вечеринок/мероприятий по согласованию', value: 2 },
                  { name: 'Разрешено проведение вечеринок/мероприятий', value: 3 },
                ],
                value: ''
              },
              {
                name: 'Дети',
                list: [
                  { name: 'Проведение вечеринок/мероприятий строго запрещено', value: 1 },
                  { name: 'Проведение вечеринок/мероприятий по согласованию', value: 2 },
                  { name: 'Разрешено проведение вечеринок/мероприятий', value: 3 },
                  { name: 'Разрешено проведение вечеринок/мероприятий', value: 4 },
                  { name: 'Разрешено проведение вечеринок/мероприятий', value: 5 },
                ],
                value: ''
              },
              {
                name: 'Животные',
                list: [
                  { name: 'Проведение вечеринок/мероприятий строго запрещено', value: 1 },
                  { name: 'Проведение вечеринок/мероприятий по согласованию', value: 2 },
                  { name: 'Разрешено проведение вечеринок/мероприятий', value: 3 },
                ],
                value: ''
              },
            ]
          }
        }
      }
    },
    computed: {
      ...mapGetters({
        params: 'cabinet/objects/params',
      }),
      /**
       * название объекта
       */
      title : {
        get(){
          return this.value?.title;
        },
        set(value){
          this.$emit('change', {...this.value, ... {title : value}});
        }
      },
      object: {
        get() {
          // return {...this.value}
          // return { ...this.form, ...this.value}
          return this.value;
        },
        set(value) {
          console.log(value)
          debugger
          // this.$emit('change', value);

        }
      }
    }

  }
</script>

<style scoped>

</style>
