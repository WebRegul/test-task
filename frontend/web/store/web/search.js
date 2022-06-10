const state = function () {
  return {
    list: [
      {
        id: 1,
        title: 'Пролетая над гнездом кукушки',
        type: {
          id: 1,
          name: 'flat',
          title: 'квартира',
        },
      },
    ],
    points: [],
    city: {

    }
  }
}

const getters = {}

const actions = {
  getList({ state, commit, getters }) {
    const promise = this.$axios.get(`/search/list`)
    promise
      .then((response) => {
        console.log(response.data)
      })
      .catch((e) => {
        console.error('xxxx')
      })
    return promise
  },

  getPoints({commit}, bounds){
    const promise = this.$axios.get(`/search/map`, {
      params: {
        bounds
      }
    })
    promise
      .then((response) => {
        commit('setPoints', response.data);
      })
      .catch((e) => {
        console.error(e)
      })
    return promise
  },

  getInfo({commit}, id){

  }
}

const mutations = {
  setPoints(state, points = []){
    state.points = points;
  }
}

export default {
  state,
  actions,
  mutations,
  getters,
}
