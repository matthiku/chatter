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
    },

    updateUserData ({ commit }, payload) {
      window.axios
        .patch(`/api/users/${payload.id}`, payload)
        .then(response => {
          if (response.data) {
            commit('setUser', response.data)
            // write the new username into the navbar
            if (response.data.username) {
              $('#show-username').text(response.data.username)
            }
          } else {
            window.console.warn('failed to update user record, response data:', response)
          }
        })
        .catch(err => window.console.error(err))
    }
  },

  getters: {
    user (state) {
      return state.user
    },
    userNames (state) {
      let names = []
      state.users.forEach(u => names.push(u.username))
      return names
    }
  }
}
