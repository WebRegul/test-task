const state = function () {
  return{
    cities: [
      { id: 'moscow', title: 'Москва'},
      { id: 'rostov', title: 'Ростов'}
    ],
    city: {
      coords: {
        coordinates: [47.228942, 39.717791]
      }
    },
  }
}

const getters = {
  cityCoords: state => {
    return state.city?.coords?.coordinates || [47.228942, 39.717791];
  }
}

const actions = {
  cities({ state, commit, getters }) {
    const promise = this.$axios.get(`/geo/cities`)
    promise.then((response) => {
        // commit('setCities', response.data);
      }).catch((e) => {
        console.error(e)
      })
    return promise
  },
}

const mutations = {

  setCities(state, list){
    state.cities = list;
  },

  /**
   *
   * @param state
   * @param city
   */
  setCity(state, id){
    state.city = state.cities.find((el) => el.id === id);
  }
}

export default {
  state,
  actions,
  mutations,
  getters,
}
