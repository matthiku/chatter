export default {
  state: {
    rooms: [],
    onlineUsers: [],
    newRoomMembers: []
  },

  mutations: {
    setRooms (state, payload) {
      state.rooms = payload
    },
    addRoom (state, payload) {
      state.rooms.push(payload)
    },
    setUsersInRoom (state, payload) {
      state.onlineUsers = payload
    },
    addToUsersInRoom (state, payload) {
      state.onlineUsers.push(payload)
    },
    removeFromUsersInRoom (state, payload) {
      state.onlineUsers = state.onlineUsers.filter(u => u !== payload)
    },
    setNewRoomMembers (state, payload) {
      state.newRoomMembers = payload
    }
  },

  /**
   * DISPATCH: asynchronous actions
   */
  actions: {
    loadRooms ({ commit }) {
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

    sendMessage (context, payload) {
      window.axios
        .post('/api/messages', payload)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
        })
        .catch(err => window.console.log(err))
    },

    createNewRoom (context, payload) {
      window.axios
        .post('api/rooms', payload)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
        })
        .catch(err => window.console.log(err))
    }
  },

  getters: {
    rooms (state) {
      return state.rooms
    },
    onlineUsers (state) {
      return state.onlineUsers
    }
  }
}
