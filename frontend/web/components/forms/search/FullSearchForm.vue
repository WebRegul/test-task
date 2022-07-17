<template>
  <v-app-bar
    fixed
    app
    height="90px"
    elevation="1"
    class="mt-16 pt-4 align-center align-baseline d-flex align-content-center"
  >
    <v-select
      v-model="filters.houseType"
      :items="houseTypes"
      dense
      solo
      deletable-chips
      label="Тип жилья"
      item-text="title"
      item-value="id"
      height="54px"
      class="mr-5 mt-2 full_search__panel__select--house-types"
    >
    </v-select>
    <v-select
      :items="states"
      dense
      solo
      deletable-chips
      label="Тип аренды"
      item-text="title"
      item-value="id"
      height="54px"
      class="mr-5 mt-2 full_search__panel__select--house-types"
    >
    </v-select>
    <v-select
      :items="prise"
      label="Цена"
      dense
      solo
      deletable-chips
      item-text="title"
      item-value="id"
      height="54px"
      class="mt-2 full_search__panel__select--house-types"
    ></v-select>
    <v-divider inset vertical class="mr-4 ml-4 vertical_header"></v-divider>
    <v-chip-group
      v-model="filters.options"
      multiple
      active-class="primary_chip--text"
      class="ml-2 mb-4"
    >
      <v-chip
        v-for="item in optionsList"
        v-show="item.primary"
        :key="item.id"
        outlined
        :value="item.id"
        class="chip_search hidden-xl-and-up hidden-md-and-down v-text-field"
        >{{ item.title }}</v-chip
      >
      <!-- <v-chip
        v-for="item in optionsList"
        v-show="item.original"
        :key="item.id"
        outlined
        :value="item.id"
        class="chip_search hidden-xl-and-down hidden-lg-and-up"
        >{{ item.title }}</v-chip
      > -->
    </v-chip-group>
    <v-divider
      inset
      vertical
      class="mr-4 ml-4 vertical_header hidden-xl-and-up hidden-md-and-down"
    ></v-divider>
    <v-dialog
      v-model="advancedFiltersShow"
      persistent
      scrollable
      max-width="700px"
    >
      <template #activator="{ on, attrs }">
        <v-btn
          class="ma-2 mb-6 pr-2 text-capitalize btn_filter"
          small
          outlined
          width="156px"
          height="56px"
          color="secondary"
          v-bind="attrs"
          v-on="on"
        >
          Все фильтры

          <v-badge
            :content="filterCount"
            :value="filterCount"
            color="#33BDB5"
            overlap
            offset-x="8"
            offset-y="5"
          >
            <img src="~assets/images/svg/three.svg" class="pl-2 pr-2" />
          </v-badge>
        </v-btn>
      </template>
      <v-card>
        <v-toolbar dark color="secondary">
          <v-card-title>
            <span class="text-h5">Фильтры</span>
          </v-card-title>
        </v-toolbar>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-card-title>Тип жилья</v-card-title>
                <v-chip-group
                  v-model="filters.houseType"
                  column
                  multiple
                  class="ml-2 mb-4"
                >
                  <v-chip
                    v-for="item in houseTypes"
                    :key="item.id"
                    color="primary"
                    class="advanced_filters__v-chip advanced_filters__house_type"
                    outlined
                    label
                    x-large
                    filter
                    :value="item.id"
                  >
                    {{ item.title }}
                    <v-icon right>{{ item.icon }}</v-icon>
                  </v-chip>
                </v-chip-group>
              </v-col>
            </v-row>
            <v-divider></v-divider>
            <v-row>
              <v-col cols="12">
                <v-card-title>Желаемая стоимость за сутки</v-card-title>
              </v-col>
            </v-row>
            <v-divider></v-divider>
            <v-row>
              <v-card-title>Удобства и опции</v-card-title>
              <v-col cols="12" md="12" class="pb-12">
                <v-row>
                  <v-flex
                    v-for="item in optionsList"
                    :key="item.id"
                    md6
                    shrink
                    grow
                  >
                    <v-checkbox
                      v-model="filters.options"
                      :value="item.id"
                      :label="item.title"
                      color="secondary"
                      hide-details
                    ></v-checkbox>
                  </v-flex>
                </v-row>
              </v-col>
            </v-row>
            <v-divider></v-divider>
            <v-row>
              <v-card-title>Правила проживания</v-card-title>
              <v-col cols="12" md="12">
                <v-row>
                  <v-flex v-for="item in rules" :key="item.id" md6 shrink grow>
                    <v-checkbox
                      v-model="filters.rules"
                      :value="item.id"
                      :label="item.title"
                      color="secondary"
                      hide-details
                    ></v-checkbox>
                  </v-flex>
                </v-row>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions class="elevation-1">
          <v-btn
            color="secondary darken-1"
            text
            @click="advancedFiltersShow = false"
          >
            Сбросить фильтры
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn
            color="primary darken-1"
            text
            outlined
            @click="advancedFiltersShow = false"
          >
            Смотреть предложения
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-app-bar>
</template>

<script>
export default {
  name: 'FullSearchForm',
  data() {
    return {
      states: ['Поднаем', 'Подаренда', 'Наем'],
      prise: ['300', '400', '500'],
      houseTypes: [
        { id: '1', name: 'flat', title: 'Квартира', icon: 'mdi-pentagram' },
        {
          id: '2',
          name: 'room',
          title: 'Комната',
          icon: 'mdi-tag-arrow-right-outline',
        },
        { id: '33', name: 'dom', title: 'Дом', icon: 'mdi-home-outline' },
        { id: '44', name: 'hostel', title: 'Хостел', icon: 'mdi-sofa-outline' },
      ],
      optionsList: [
        {
          id: '1',
          name: 'cancellation',
          title: 'Бесплатная отмена',
          order: -1,
          primary: true,
          original: true,
        },
        {
          id: '9',
          name: 'moment',
          title: 'Мгновенное подтверждение',
          order: 0,
          primary: true,
          original: true,
        },
        {
          id: '2',
          name: 'host',
          title: 'Суперхозяин',
          order: 1,
          primary: true,
          original: true,
        },
        {
          id: '3',
          name: 'burning',
          title: 'Горящее предложение',
          order: 2,
          primary: true,
          original: true,
        },
        {
          id: '4',
          name: 'still',
          title: 'Что-то ещё',
          order: 3,
          primary: true,
          original: false,
        },
        {
          id: '5',
          name: 'something',
          title: 'И ещё что-нибудь',
          order: 4,
          primary: true,
          original: false,
        },
        {
          id: '6',
          name: 'some',
          title: 'И ещё что-то длинное длинное',
          order: 5,
          primary: true,
          original: false,
        },
        { id: '7', name: 'tele', title: 'Телевизор', order: 6, primary: false },
        { id: '8', name: 'dza', title: 'Джакузи', order: 7, primary: false },
      ],
      rules: [
        {
          id: '1',
          name: 'smoking',
          title: 'Можно курить',
          order: 0,
          primary: true,
        },
        {
          id: '2',
          name: 'smoking',
          title: 'Можно курить на балконе',
          order: 2,
          primary: true,
        },
        {
          id: '3',
          name: 'cats',
          title: 'Можно с животными',
          order: 3,
          primary: true,
        },
        {
          id: '4',
          name: 'buhlo',
          title: 'Можно бухать',
          order: 4,
          primary: true,
        },
      ],
      filters: {
        houseType: [],
        options: [],
        rules: [],
      },
      advancedFiltersShow: false,
    }
  },
  computed: {
    filterCount() {
      return this.filters.options.length + this.filters.houseType.length
    },
  },
  methods: {
    compareOptionValue(val) {
      console.log(val)
      return val
    },
  },
}
</script>

<style scoped>
.full_search__panel__select--house-types {
  max-width: 200px;
  border-radius: 16px;
  /*width: 25%;*/
}
.chip_search {
  height: 56px;
  border: 1px solid #d7d7d8;
  border-radius: 12px;
}
.v-chip.v-chip--outlined.v-chip.v-chip.chip_search {
  background-color: #fffbf9 !important;
}
.primary_chip--text {
  border: 1px solid #33bdb5;
  border-radius: 12px;
}

.v-chip:before {
  background-color: #41c5bd;
}
.btn_filter {
  background: #e6f7f6;
  border: 1px solid #d7d7d8;
  border-radius: 16px;
}
.v-chip-group .v-chip {
  margin: 2px 8px 4px 0;
}
.advanced_filters__v-chip.advanced_filters__house_type {
  width: 150px;
  text-align: center;
  justify-content: center;
}

.vertical_header {
  width: 28px;
  height: 48px;
  border: 1px solid #afafb0;
}
.v-text-field {
  font-family: 'Ubuntu';
  font-weight: 400;
  font-size: 16px;
  line-height: 24px;
  letter-spacing: 0.005em;
  color: #37373a;
}
</style>
