import colors from 'vuetify/es5/util/colors'

export default {
  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    titleTemplate: '%s - priehali',
    title: 'priehali',
    htmlAttrs: {
      lang: 'ru',
    },
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: '' },
      { name: 'format-detection', content: 'telephone=no' },
    ],
    link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }],
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: ['@/assets/main.scss'],

  axios: {
    baseURL: process.env.API_HOST + '/' + process.env.API_VERSION,
    credentials: true,
  },

  publicRuntimeConfig: {
    axios: {
      baseURL: process.env.API_HOST + '/' + process.env.API_VERSION,
    },
  },

  /**
   * конфиг SSR. Используется только во время серверного рендера, оверайдит предыдущие.
   */
  privateRuntimeConfig: {
    axios: {
      baseURL:
        (process.env.API_HOST_LOCAL || process.env.API_HOST) +
        '/' +
        process.env.API_VERSION,
    },
  },

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [
    { src: '~/plugins/ymap.js', mode: 'client' },
    '~plugins/filters.js',
    '~plugins/v-mask.js',
  ],

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
    // https://go.nuxtjs.dev/eslint
    '@nuxtjs/eslint-module',
    // https://go.nuxtjs.dev/vuetify
    '@nuxtjs/vuetify',
    '@nuxtjs/eslint-module',
    '@nuxtjs/router-extras'
  ],

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: ['@nuxtjs/axios', '@nuxtjs/dayjs', '@nuxtjs/device'],
  // devModules: ['@nuxtjs/eslint-module'],

  router: {
    middleware: ['geo']
  },

  dayjs: {
    locales: ['ru'],
    defaultLocale: 'ru',
    // defaultTimeZone: 'Asia/Tokyo',
    // plugins: [ 'utc', // import 'dayjs/plugin/utc' 'timezone' // import 'dayjs/plugin/timezone' ] // Your Day.js plugin
    plugins: ['customParseFormat'],
  },
  // Vuetify module configuration: https://go.nuxtjs.dev/config-vuetify
  vuetify: {
    customVariables: ['~/assets/variables.scss'],
    defaultAssets: {
      // font: true,
      // icons: 'md'
    },
    icons: {
      iconfont: 'mdiSvg',
      // iconfont: 'md',
      // iconfont: 'md',
    },
    theme: {
      dark: false,
      themes: {
        dark: {
          primary: colors.blue.darken2,
          accent: colors.amber.darken3,
          secondary: colors.grey.darken3,
          info: colors.teal.lighten1,
          warning: colors.amber.base,
          error: colors.deepOrange.accent4,
          success: colors.green.accent3,
        },
        light: {
          primary: colors.blue.darken2,
          accent: colors.amber.darken3,
          secondary: colors.grey.darken3,
          info: colors.teal.lighten1,
          warning: colors.amber.base,
          error: colors.deepOrange.accent4,
          success: colors.green.accent3,
        },
      },
    },
  },

  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {},
}
