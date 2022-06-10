<template>
  <v-app-bar
    fixed
    app
    elevation="1"
    class="mt-16 pt-4 align-center align-baseline d-flex align-content-center"
  >
    <v-select
      v-model="filters.houseType"
      :items="houseTypes"
      multiple
      chips
      deletable-chips
      label="Тип жилья"
      item-text="title"
      item-value="id"
      class="full_search__panel__select--house-types"
    >
      <!--      <template v-slot:selection="{ item, index }">-->
      <!--        <v-chip v-if="index < 2">-->
      <!--          <span>{{ item.title }}</span>-->
      <!--        </v-chip>-->
      <!--        <span-->
      <!--          v-if="index >= 2"-->
      <!--          class="grey&#45;&#45;text text-caption"-->
      <!--        >-->
      <!--          (и еще {{ filters.houseType.length - 2 }})-->
      <!--        </span>-->
      <!--      </template>-->
    </v-select>
    <v-chip-group v-model="filters.options" multiple class="ml-2 mb-4">
      <v-chip
        v-for="item in optionsList"
        v-show="item.primary"
        :key="item.id"
        filter
        outlined
        :value="item.id"
        >{{ item.title }}</v-chip
      >
    </v-chip-group>

    <v-dialog
      v-model="advancedFiltersShow"
      persistent
      scrollable
      max-width="700px"
    >
      <template #activator="{ on, attrs }">
        <v-btn
          class="ma-2 mb-6"
          small
          outlined
          rounded
          color="secondary"
          v-bind="attrs"
          v-on="on"
        >
          <v-badge
            :content="filterCount"
            :value="filterCount"
            color="primary"
            overlap
            offset-x="8"
            offset-y="5"
          >
            <v-icon>mdi-format-list-bulleted-square</v-icon>
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
        { id: '1', name: 'wifi', title: 'Wi-Fi', order: -1, primary: true },
        { id: '9', name: 'kam', title: 'Камин', order: 0, primary: true },
        {
          id: '2',
          name: 'cond',
          title: 'Кондиционер',
          order: 1,
          primary: true,
        },
        {
          id: '3',
          name: 'stir',
          title: 'Стиральная машина',
          order: 2,
          primary: false,
        },
        { id: '4', name: 'utug', title: 'Утюг', order: 3, primary: true },
        { id: '5', name: 'fen', title: 'Фен', order: 4, primary: true },
        { id: '6', name: 'kuh', title: 'Кухня', order: 5, primary: true },
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
  max-width: 450px;
  /*width: 25%;*/
}
.advanced_filters__v-chip.advanced_filters__house_type {
  width: 150px;
  text-align: center;
  justify-content: center;
}
</style>
