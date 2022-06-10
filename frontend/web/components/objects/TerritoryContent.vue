<template>
  <div>
    <h2 class="mt-7">Удобства на территории</h2>
    <v-row class="mt-2">
      <v-col
        cols="6"
        v-for="(param, key) in comfort"
        :key="key"
      >
        <h3 class="mr-2">
          {{param.name}}
        </h3>
        <div
          class="d-flex"
          v-for="(item,key) in param.list"
          :key="key"
        >
          <v-checkbox
            :label="item.name"
            :value="item.value"
            class="mr-2"
          ></v-checkbox>
          <v-tooltip v-if="item.hint" right>
            <template v-slot:activator="{ on, attrs }">
              <v-icon
                color="primary"
                dark
                v-bind="attrs"
                v-on="on"
              >
                mdi-help-circle
              </v-icon>
            </template>
            <span>{{item.hint}}</span>
          </v-tooltip>
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
  export default {
    name: "TerritoryContent",
    data() {
      return {
        comfort: [
          {
            name: 'Удобства и услуги',
            list: [
              { name: 'Сейф', value: 1 },
              { name: 'Парковка', value: 2 },
              { name: 'Обмен валют', value: 3 },
              { name: 'Услуга «звонок-будильник»', value: 4, hint: 'Текст подсказки' },
              { name: 'Заказ еды и напитков в номер', value: 5 },
              { name: 'Прачечная', value: 6 },
            ],
            value: ''
          },
          {
            name: 'Досуг',
            list: [
              { name: 'Сауна или баня', value: 1 },
              { name: 'Ресторан или кафе', value: 2 },
              { name: 'Детская площадка', value: 3 },
              { name: 'Зона барбекю/мангал', value: 4 },
              { name: 'Фитнес центр', value: 5 },
            ],
            value: ''
          },
        ]
      }
    }
  }
</script>

<style scoped>

</style>
