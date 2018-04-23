export default {
  state: {
    user: {},
    users: []
  },

  mutations: {
    setUser (state, payload) {
      state.user = payload
    },
    setUsers (state, payload) {
      state.users = payload
    },
    addToOnlineUsers (state, payload) {
      // make sure a new user is also added to the generic list of users!
      if (!state.users.find(u => u.id === payload.id)) {
        state.users.push(payload)
      }
    }
  },

  actions: {
    loadUsers ({ commit }) {
      window.axios
        .get('api/users')
        .then(response => {
          if (response.data) {
            commit('setUsers', response.data)
          } else {
            window.console.warn('failed to load users data', response)
          }
        })
        .catch(err => window.console.error(err))
    }
  },

  getters: {
    user (state) {
      return state.user
    }
  }
}
