import Vue from 'vue'
import YmapPlugin from 'vue-yandex-maps'

const settings = {
  apiKey: 'd3737ed4-ae8f-44f4-834e-1ba93c8032b6',
  lang: 'ru_RU',
  coordorder: 'latlong',
  enterprise: false,
  version: '2.1',
} // настройки плагина

Vue.use(YmapPlugin, settings)
