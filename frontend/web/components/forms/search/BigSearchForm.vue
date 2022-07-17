<template>
  <v-card class="border_radius_header">
    <v-container class="py-0 mr-15">
      <v-form ref="search">
        <v-row class="pt-3 pb-3">
          <v-spacer></v-spacer>
          <v-btn icon @click="$refs.search.focus()">
            <v-icon>mdi-magnify</v-icon>
          </v-btn>
          <v-text-field
            ref="search"
            v-model="search"
            full-width
            hide-details
            label="Ростов-на-Дону"
            single-line
          ></v-text-field>
          <v-divider vertical class="vertical_header"></v-divider>
          <v-text-field
            ref="search"
            v-model="search"
            full-width
            hide-details
            label="26.04 - 01.05"
            single-line
            class="ml-16"
          ></v-text-field>
          <v-divider vertical class="vertical_header"></v-divider>
          <v-text-field
            ref="search"
            v-model="search"
            full-width
            hide-details
            label="3 гостя, 1 питомец"
            single-line
            class="ml-16"
          ></v-text-field>
        </v-row>
      </v-form>
    </v-container>
  </v-card>
</template>

<script>
export default {
  name: 'BigSearchForm',
  components: {},

  data: () => ({}),
  computed: {},
}
</script>

<style scoped>
.vertical_header {
  border: 1px solid #afafb0;
}
.border_radius_header {
  border-radius: 12px;
}
</style>
