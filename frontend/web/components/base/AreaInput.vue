<template>
  <v-textarea
    class="mt-5"
    outlined
    height="300"
    :label="title"
    :value="value"
    :counter="withCounter"
    :maxlength="maxCounts"
    :hint="hint"
    :rules="rules"
    persistent-hint
    @input="$emit('input', $event)"
  ></v-textarea>
</template>

<script>
export default {
  name: "AreaInput",
  props:{
    value: {
      type: String,
      default: ''
    },
    title: {
      type: String,
      default: ''
    },
    hint: {
      type: String,
      default: ''
    },
    withCounter: {
      type: Boolean,
      default: false
    },
    maxCounts: {
      type: Number,
      default: null
    },
  },
  data() {
    return {
      rules: [ v => (v ? v.length : null ) <= this.maxCounts || `Максимум ${this.maxCounts} символов` ],
    }
  }

}
</script>

<style scoped>

</style>
