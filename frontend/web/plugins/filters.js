import Vue from 'vue'

import dayjs from 'dayjs'
import { wordCase } from '../utils/helpers'

const DEFAULT_FORMAT = 'D.MM.YYYY'
Vue.filter('formatDate', (value, format = DEFAULT_FORMAT) => {
  return dayjs(value).format(format)
})

const DEFAULT_CURRENCY = 'RUB'
const DEFAULT_LOCALE = 'ru-RU'
Vue.filter(
  'currency',
  function (value, digits = 0, currency = DEFAULT_CURRENCY) {
    const options = {
      style: 'currency',
      currency,
      minimumFractionDigits: digits,
    }
    try {
      return new Intl.NumberFormat(DEFAULT_LOCALE, options).format(value)
    } catch (e) {
      // eslint-disable-next-line no-console
      console.error('Format currency filter error.', e)
      return value
    }
  }
)

Vue.filter('word_case', (value, words = [], withNum = true) => {
  const resNum = withNum ? `${value} ` : ''
  return `${resNum}${wordCase(value, words)}`
})
