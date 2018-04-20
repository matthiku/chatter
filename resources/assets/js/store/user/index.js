export default {
  state: {
    user: {},
    users: []
  },

  mutations: {
    setUser(state, payload) {
      state.user = payload
    },
    setUsers(state, payload) {
      state.users = payload
    }
  },

  actions: {
    loadUsers({ commit }) {
      window.axios.get('api/users').then(response => {
        if (response.data) {
          commit('setUsers', response.data)
        } else {
          window.console.warn('failed to load users data', response)
        }
      }).catch(err => window.console.error(err))
    }
  },

  getters: {
    user(state) {
      return state.user
    }
  }
}
