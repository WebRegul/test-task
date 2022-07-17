<template>
  <v-card
    v-if="orientation == 'horizontal'"
    class="object__card--horizontal rounded-lg elevation-1 md-5 mr-0 ml-0"
  >
    <v-container>
      <v-row>
        <v-col class="pa-0 mr-1" style="max-width: 393px">
          <v-img
            min-width="224px"
            max-width="393px"
            max-height="300px"
            lazy-src="/1.jpg"
            src="/1.jpg"
            class="rounded-lg"
          ></v-img>
        </v-col>
        <v-col class="pb-0 align-end flex-column pa-2">
          <v-card-subtitle class="pb-0 pt-0">
            <v-row class="d-flex justify-space-between mt-1 mb-2">
              <v-card-actions
                class="align-self-start text--secondary text-caption pa-0 text-capitalize"
                >{{ value.type.title }}</v-card-actions
              >
              <v-icon> mdi-heart-outline</v-icon>
            </v-row>
          </v-card-subtitle>

          <v-card-title
            class="align-self-start text-h6 pt-0 pb-5 pl-0 pr-0"
            style="line-height: 1.3rem; font-size: 1rem !important"
          >
            {{ value.title }}
          </v-card-title>
          <v-card-text class="mt-0 mb-16 grow card__text__span">
            <v-row>
              <span class="mr-4">
                {{ maxGuests | word_case(['гость', 'гостя', 'гостей']) }}
              </span>
              <span class="mr-4">
                {{ bed | word_case(['кровать', 'коровати', 'кроватей']) }}
              </span>
              <span class="mr-4">
                {{ bedroom | word_case(['спальня', 'спальни', 'спален']) }}
              </span>
              <span class="mr-4"> {{ square }} м<sup>2</sup> </span>
            </v-row>
            <v-row class="mt-5">
              <v-chip
                v-for="item in value.options"
                v-show="item.primary"
                :key="item.id"
                outlined
                small
                :value="item.id"
                class="ma-1 ml-0"
              >
                <v-icon small left>{{ item.icon }}</v-icon>
                {{ item.title }}
              </v-chip>
            </v-row>
            <v-row>
              <ul class="var-list">
                <li>Несколько вариантов питания</li>
                <li>Горящее предложение</li>
                <li>Предоплата</li>
              </ul>
            </v-row>
          </v-card-text>
          <v-spacer></v-spacer>
          <v-card-actions
            class="object__card--horizontal__card-actions pb-0 mt-auto d-flex w-100"
          >
            <v-flex class="d-inline-flex align-center">
              <v-rating
                :value="value.rating.value"
                color="orange darken-1"
                dense
                half-increments
                readonly
                length="1"
                size="25"
              ></v-rating>
              <div class="grey--text text-caption">
                {{ value.rating.value }} ({{ value.reviews.count }})
              </div>
            </v-flex>
            <v-flex class="text--darken-4 text-right my-application pt-3">
              <span>от {{ value.price | currency }} / ночь</span>
            </v-flex>
          </v-card-actions>
        </v-col>
      </v-row>
    </v-container>
  </v-card>
  <v-card
    v-else-if="orientation == 'vertical'"
    style="width: 350px"
    class="object__card--vertical mx-auto"
    :class="{ 'object__card--map': map }"
  >
    <v-container>
      <v-img
        height="250"
        contain
        lazy-src="https://cdn.vuetifyjs.com/images/cards/cooking.png"
        src="https://cdn.vuetifyjs.com/images/cards/cooking.png"
        :class="{ 'object__card__image--map': map }"
      >
      </v-img>
      <v-card-subtitle
        class="text--secondary text-caption text-uppercase pt-1 pb-0"
        >{{ value.type.title }}</v-card-subtitle
      >
      <v-card-title
        class="text-h6 pt-0 pb-5 text-break"
        style="line-height: 1.3rem"
      >
        {{ value.title }}
      </v-card-title>
      <v-card-text>
        <v-row align="center" style="width: 90%">
          <v-rating
            :value="value.rating.value"
            color="amber"
            dense
            readonly
            size="14"
          ></v-rating>
          <div class="grey--text text-caption">
            {{ value.rating.value }} ({{ value.reviews.count }})
          </div>
          <div
            class="text--darken-2 text--secondary text-h6 text-right ml-auto"
          >
            <span>От {{ value.price | currency }}</span>
          </div>
        </v-row>
      </v-card-text>
      <v-divider class="mx-4"></v-divider>
    </v-container>
  </v-card>
</template>

<script>
import collect from 'collect.js'

export default {
  name: 'ObjectCard',
  props: {
    orientation: {
      // horizontal || vertical
      type: String,
      default: 'horizontal',
    },
    value: {
      type: Object,
      default: () => ({
        id: '1',
        title: 'Уютная однокомнатная квартира с видом на море',
        price: 4500,
        type: {
          id: 1,
          name: 'flat',
          title: 'квартира',
        },
        params: [
          { id: '1', name: 'square', title: 'Площадь', value: '40' },
          { id: '2', name: 'bed', title: 'Количество кроватей', value: '2' },
          { id: '4', name: 'bedroom', title: 'Количество спален', value: '1' },
          {
            id: '3',
            name: 'max_guests',
            title: 'Максимальное количество гостей',
            value: '4',
          },
        ],
        options: [
          {
            id: '1',
            name: 'wifi',
            title: 'Wi-Fi',
            order: 0,
            primary: true,
            icon: 'mdi-wifi',
          },
          {
            id: '2',
            name: 'pets',
            title: 'Pets friendly',
            order: 1,
            primary: true,
            icon: 'mdi-waves',
          },
          {
            id: '3',
            name: 'stir',
            title: 'Бесплатная отмена',
            order: 2,
            primary: true,
            icon: 'mdi-relation-many-to-many',
          },
        ],
        rating: {
          value: 4.75,
          detail: {},
        },
        reviews: {
          count: '8 отзывов',
          list: [],
        },
      }),
    },
    map: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    /**
     * Collection of the value
     * @return {Collection<unknown>}
     */
    val() {
      return collect(this.value)
    },
    params() {
      return collect(this.val.get('params')).keyBy('name')
    },
    square() {
      return this.params.get('square').value
    },

    bed() {
      return this.params.get('bed').value
    },
    bedroom() {
      return this.params.get('bedroom').value
    },
    maxGuests() {
      return this.params.get('max_guests').value
    },
  },
  methods: {},
}
</script>

<style>
.object__card--map .v-image .v-image__image--preload {
  filter: none !important;
}
.object__card__image--map .v-image .v-image__image--preload {
  filter: none !important;
}
.object__card--horizontal__card-actions {
  width: 100%;
}
.var-list {
  font-family: 'Ubuntu';
  font-weight: 400;
  font-size: 12px;
  line-height: 20px;
  letter-spacing: 0.004em;
  color: #00aca2;
}
.card__text__span {
  font-family: 'Ubuntu';
  font-style: normal;
  font-weight: 400;
  font-size: 12px;
  line-height: 20px;
  align-items: center;
  letter-spacing: 0.004em;
  color: #5f5f61;
}
</style>
