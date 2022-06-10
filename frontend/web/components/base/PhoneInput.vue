<template>
  <div class="d-flex">
    <v-select
      class="col-4 mr-2"
      :items="maskItems"
      item-text="name"
      item-value="value"
      v-model="maskType"
      solo
    >
      <template v-slot:selection="{ item }">
        <img class="select__icon"  alt="ru" :src="item.icon">
        <span>{{ item.name }}</span>
      </template>
      <template v-slot:item="{ item }">
        <img class="select__icon"  alt="ru" :src="item.icon">
        <span>{{ item.name }}</span>
      </template>
    </v-select>
    <v-text-field
      :value="value"
      @input="inputHandler"
      :label="title"
      solo
      v-mask="maskType"
      :required="required"
    ></v-text-field>
  </div>
</template>

<script>
export default {
  name: "PhoneInput",
  props: {
    value: {
      type: String,
      default: ''
    },
    title: {
      type: String,
      default: ''
    },
    required: {
      type: Boolean,
      default: false
    },
  },
  data() {
    return {
      maskItems: [
        { name: 'RU', value: '7 (###) ###-##-##', icon: require(`~/assets/images/media/ru.png`)},
        { name: 'BL', value: '375 (##) ###-##-##', icon: require(`~/assets/images/media/bl.png`)},
      ],
      maskType : '7 (###) ###-##-##',
    }
  },
  methods: {
    inputHandler(value){
      this.$emit('input', value );
      this.$emit('numberValue', value.replace(/[^0-9]/g, '') );
    },
  }
}
</script>

<style scoped>

</style>
