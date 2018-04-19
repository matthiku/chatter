export default {
  state: {
    room: null,
    rooms: [],
    usersInRoom: [],
    message: null,
    messages: []
  },

  mutations: {
    setRoom(state, payload) {
      state.room = payload
    },
    setRooms(state, payload) {
      state.rooms = payload
    },
    setUsersInRoom(state, payload) {
      state.usersInRoom = payload
    },
    setMessage(state, payload) {
      state.message = payload
    },
    setMessages(state, payload) {
      state.messages = payload
    }
  },

  actions: {
    loadMessages({ commit }) {
      window.axios.get('/api/messages')
        .then(response => {
          if (response.data) {
            commit('setMessages', response.data)
            // state.messages =
          }
        })
    },

    loadRooms({ commit }) {
      window.axios.get('/api/rooms')
        .then(response => {
          if (response.data) {
            commit('setRooms', response.data)
            // state.messages =
          }
        })
    }
  },

  getters: {
    room(state) {
      return state.room
    },
    rooms(state) {
      return state.rooms
    },
    usersInRoom(state) {
      return state.usersInRoom
    },
    message(state) {
      return state.message
    },
    messages(state) {
      return state.messages
    }
  }
}
