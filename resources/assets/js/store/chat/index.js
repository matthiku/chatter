export default {
  state: {
    rooms: [],
    onlineUsers: [],
    newRoomMembers: []
  },

  /**
   * COMMITS: synchronous state updates
   */
  mutations: {
    setRooms (state, payload) {
      state.rooms = payload
    },
    cleanUpRooms (state) {
      state.rooms = state.rooms.filter(el => el.id !== 0)
    },
    addRoom (state, payload) {
      // window.console.log('addRoom', payload)
      // first, make sure we do not have a 'deserted' room
      state.rooms = state.rooms.filter(el => el.id !== 0)
      state.rooms.push(payload)
    },
    updateRoom (state, payload) {
      // window.console.log('updateRoom', payload)
      state.rooms.map(elem => {
        if (elem.id === payload.id) {
          elem.name = payload.name
          elem.users = payload.users
        }
      })
    },
    
    removeRoom (state, room) {
      state.rooms.map(elem => {
        if (elem.id === room.id) {
          elem.name = room.reason
          elem.owner_id = 0
          elem.id = 0
          elem.users = []
          elem.messages = []
        }
      })
    },
    setOnlineUsers (state, payload) {
      state.onlineUsers = payload
    },
    addToOnlineUsers (state, payload) {
      state.onlineUsers.push(payload)
    },
    removeFromOnlineUsers (state, payload) {
      state.onlineUsers = state.onlineUsers.filter(u => u !== payload)
    },
    setNewRoomMembers (state, payload) {
      state.newRoomMembers = payload
    },
    setMessagesForRoom (state, payload) {
      state.rooms.map(elem => {
        if (elem.id === payload.id) {
          elem.messages = payload.messages
          // window.console.log(elem.messages.length, 'messages added to room id', elem.id)
        }
      })
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

    deleteMessage (context, payload) {
      window.axios
        .delete(`/api/messages/${payload}`)
        .then(response => {
          if (!(response.data && response.data === 'deleted')) {
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
    },

    updateRoomProperties (context, payload) {
      window.axios
        .patch(`api/rooms/${payload.id}`, payload)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
        })
        .catch(err => window.console.log(err))
    },

    leaveRoom ({commit}, payload) {
      window.axios
        .post(`/api/rooms/${payload.id}/leave`)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
          commit('removeRoom', {id: payload.id, reason: 'user has left this room'})
          window.console.log(response.data)
        })
        .catch(err => window.console.log(err))
    },

    getMessagesForRoom ({commit}, payload) {
      window.axios
        .get(`api/rooms/${payload.id}`)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          } else {
            commit('setMessagesForRoom', response.data)
          }
        })
        .catch(err => window.console.log(err))
    },

    deleteRoom (x, payload) {
      window.axios
        .delete(`api/rooms/${payload.room_id}`)
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
