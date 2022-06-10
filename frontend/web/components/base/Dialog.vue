<template>
  <v-dialog
    :value="value"
    persistent
    width="500"
    :fullscreen="fullScreen"
  >
    <v-card>
      <v-card-title class="white--text grey">
        {{title}}
        <v-spacer></v-spacer>
        <v-btn
          icon
          dark
          @click="$emit('close')"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-card-text>
        <slot name="content"></slot>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    name: "Dialog",
    props: {
      value: {
        type: Boolean,
        default: false,
      },
      title: {
        type: String,
        default: ''
      },
      fullScreen: {
        type: Boolean,
        default: false,
      },
    }
  }
</script>

<style scoped>

</style>
