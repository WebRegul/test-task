export default function ({ $axios }, inject) {
  const axios = $axios.create({
    // baseURL: $config.baseURL,
    headers: {
      common: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        // "sec-fetch-mode": "cors",
        // "sec-fetch-site": "same-origin",
      },
    },
  })

  inject('axios', axios)
}
