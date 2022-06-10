<template>
  <v-form ref="search" v-model="valid" lazy-validation @submit.prevent="submit">
    <v-row>
      <v-col cols="12" md="1"></v-col>
      <v-col cols="12" md="3">
        <v-tooltip slot="append" v-model="showCityHint" :top="!short" :bottom="short">
          <template #activator="{}">
            <city-select ref="citySelect" v-model="city" :dense="short" @input="onCityChange"></city-select>
          </template>
          <v-icon dense color="accent">mdi-home-search</v-icon>
          <span> Выберите пункт назначения </span>
        </v-tooltip>

      </v-col>
      <v-col cols="12" md="3">
        <v-menu
          ref="menuDateFrom"
          v-model="menuDateFrom"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="auto"
        >
          <template #activator="{ on, attrs }">
            <v-text-field
              v-model="dateFromFormatted"
              label="Дата заезда"
              :prepend-icon="short ? '' : 'mdi-calendar'"
              v-bind="attrs"
              filled
              rounded
              solo
              :dense="short"
              @blur="dateFrom = parseDate(dateFromFormatted)"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="dateFrom"
            solo
            filled
            rounded
            no-title
            @input="menuDateFrom = false"
          ></v-date-picker>
        </v-menu>
      </v-col>
      <v-col cols="12" md="3">
        <v-menu
          ref="menuDateTo"
          v-model="menuDateTo"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="auto"
        >
          <template #activator="{ on, attrs }">
            <v-text-field
              v-model="dateToFormatted"
              label="Дата выезда"
              :prepend-icon="short ? '' : 'mdi-calendar'"
              v-bind="attrs"
              filled
              rounded
              solo
              :dense="short"
              @blur="dateTo = parseDate(dateToFormatted)"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="dateTo"
            no-title
            @input="menuDateTo = false"
          ></v-date-picker>
        </v-menu>
      </v-col>
      <v-col v-if="!short" cols="12" md="1">
        <v-btn color="accent" type="submit" dark fab>
          <v-icon>mdi-magnify</v-icon>
        </v-btn>
      </v-col>
      <v-col cols="12" md="1"></v-col>
    </v-row>
  </v-form>
</template>

<script>

import { mapMutations } from "vuex";
import CitySelect from "../../controls/selects/CitySelect";
const DATE_FORMAT = 'D.MM.YYYY'

export default {
  name: 'SmallSearchForm',
  components: {CitySelect},

  props: {
    short: {
      type: Boolean,
      default: false,
    },
  },
  data: (vm) => ({
    valid: false,

    dateFrom: null,
    dateTo: null,
    city: vm.$route.params.city,

    dateFromFormatted: !vm.dateFrom ? '' : vm.formatDate(vm.dateFrom),
    dateToFormatted: !vm.dateTo ? '' : vm.formatDate(vm.dateTo),
    menuDateFrom: false,
    menuDateTo: false,

    showCityHint: false,
  }),
  computed: {
    computedDateFormatted() {
      return this.formatDate(this.date)
    },
  },

  watch: {
    dateFrom(val) {
      this.dateFromFormatted = this.formatDate(this.dateFrom)
    },
    dateTo(val) {
      this.dateToFormatted = this.formatDate(this.dateTo)
    },
  },

  methods: {
    ...mapMutations({
      'setCurrentCity' : 'web/geo/setCity'
    }),
    onCityChange(value){
      this.showCityHint = false;
      this.setCurrentCity(value);
      if(this.short){
        this.submit();
      }
    },

    submit() {
      if (!this.city) {
        this.showCityHint = true
        this.$refs.citySelect.activateMenu();
        return
      }
      const url = `/search/${this.city}/`
      this.$router.push(url)
    },
    formatDate(date) {
      if (!date) return null
      return this.$dayjs(date).format(DATE_FORMAT)
    },
    parseDate(date) {
      if (!date) return null
      return this.$dayjs(date, DATE_FORMAT).format('YYYY-MM-DD')
    },
  },
}
</script>

<style scoped></style>
