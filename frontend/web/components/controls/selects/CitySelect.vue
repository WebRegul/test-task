<template>
  <v-autocomplete
    ref="selectCity"
    :value="value"
    clearable
    filled
    rounded
    solo
    :dense="dense"
    label="Куда едем?"
    :items="items"
    @change="onCitySelectHandler"
  >
    <template v-slot:item="data">
      <v-chip
        color="accent" small outlined
        class="mr-3 text--disabled darken-2"
      >
        <span class="accent-1 text-subtitle-2 text--disabled">{{ data.item.country }}</span>
      </v-chip>
      <v-list-item-content v-text="data.item.text"></v-list-item-content>
    </template>
  </v-autocomplete>
</template>

<script>
import {mapActions} from "vuex";

export default {
  name: "CitySelect",
  props:{
    dense:{
      type: Boolean,
      default: false
    },
    value:{
      type: String,
      default: ''
    }
  },
  data(){
    return{
      cities: [
        { url: 'moscow', title: 'Москва', count: 10000, country: { alfa2: 'RU'} },
        { url: 'rostov-on-don', title: 'Ростов-на-Дону', count: 10000, country: { alfa2: 'RU'} },
        { url: 'sochi', title: 'Сочи', count: 10000, country: { alfa2: 'RU'} },
      ],

    }
  },
  computed:{
    items(){
      return this.cities.map((item) => {
        return { value: item.url, text: item.title, count: 0, country: item?.country?.alfa2 }
      });
    }
  },
  mounted() {
    // this.getCities().then((response) => {
    //   this.cities = response.data;
    // });
  },
  methods:{
    ...mapActions({
        getCities: 'web/geo/cities'
    }),
    onCitySelectHandler(value) {
      this.$emit('input', value)
    },

    activateMenu(){
      this.$refs.selectCity.focus()
      this.$refs.selectCity.activateMenu()
      this.$refs.selectCity.isMenuActive = true
    }
  }
}
</script>

<style scoped>

</style>
