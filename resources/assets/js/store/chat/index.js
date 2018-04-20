export default {
  state: {
    rooms: [],
    onlineUsers: []
  },

  mutations: {
    setRooms(state, payload) {
      state.rooms = payload
    },
    setUsersInRoom(state, payload) {
      state.onlineUsers = payload
    },
    addToUsersInRoom(state, payload) {
      state.onlineUsers.push(payload)
    },
    removeFromUsersInRoom(state, payload) {
      state.onlineUsers = state.onlineUsers.filter(u => u !== payload)
    }
  },

  actions: {
    loadRooms({ commit }) {
      window.axios
        .get('/api/rooms')
        .then(response => {
          if (response.data) {
            commit('setRooms', response.data)
            // state.messages =
          }
        })
        .catch(err => window.console.log(err))
    },

    sendMessage(store, payload) {
      window.axios
        .post('/api/messages', payload)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
        })
        .catch(err => window.console.log(err))
    }
  },

  getters: {
    rooms(state) {
      return state.rooms
    },
    onlineUsers(state) {
      return state.onlineUsers
    }
  }
}
