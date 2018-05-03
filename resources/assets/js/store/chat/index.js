export default {
  state: {
    rooms: [],
    onlineUsers: [],
    chatroomName: 'chatroom',
    newRoomMembers: [],
    newMessagesArrived: [],
    userCreatedNewRoom: null
  },

  /**
   * COMMITS: synchronous updates to the STORE
   */
  mutations: {
    setChatroomName(state, payload) {
      state.chatroomName = payload
    },
    setRooms(state, payload) {
      payload.sort(function(a, b) {
        a = new Date(a.updated_at)
        b = new Date(b.updated_at)
        return a > b ? -1 : a < b ? 1 : 0
      })
      state.rooms = payload
    },
    sortRooms(state) {
      state.rooms.sort(function(a, b) {
        a = new Date(a.updated_at)
        b = new Date(b.updated_at)
        return a > b ? -1 : a < b ? 1 : 0
      })
    },
    cleanUpRooms(state) {
      state.rooms = state.rooms.filter(el => el.id !== 0)
    },
    addRoom(state, payload) {
      // window.console.log('addRoom', payload)
      // first, make sure we do not have a 'deserted' room or duplicate rooms
      state.rooms = state.rooms.filter(
        el => el.id !== 0 && el.id !== payload.id
      )
      // insert the new room at the top of the list
      state.rooms.unshift(payload)
    },
    updateRoom(state, payload) {
      // window.console.log('updateRoom', payload)
      state.rooms.map(elem => {
        if (elem.id === payload.id) {
          elem.name = payload.name
          elem.users = payload.users
        }
      })
    },

    removeRoom(state, room) {
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
    setOnlineUsers(state, payload) {
      state.onlineUsers = payload
    },
    addToOnlineUsers(state, payload) {
      // not to show the same user twice
      if (!state.onlineUsers.find(el => el.id === payload.id))
        state.onlineUsers.push(payload)
    },
    removeFromOnlineUsers(state, payload) {
      state.onlineUsers = state.onlineUsers.filter(u => u !== payload)
    },
    setNewRoomMembers(state, payload) {
      state.newRoomMembers = payload
    },
    setMessagesForRoom(state, payload) {
      state.rooms.map(elem => {
        if (elem.id === payload.id) {
          elem.messages = payload.messages
          // window.console.log(elem.messages.length, 'messages added to room id', elem.id)
        }
      })
    },
    clearUserCreatedNewRoom(state) {
      state.userCreatedNewRoom = null
    },
    addToNewMessagesArrived(state, payload) {
      state.newMessagesArrived.push(payload)
    },
    clearRoomFromNewMessagesArrived(state, payload) {
      state.newMessagesArrived = state.newMessagesArrived
        .filter(el => el.room_id !== payload)
    }
  },

  /**
   * DISPATCH: asynchronous actions
   */
  actions: {
    loadRooms({ commit }) {
      window.axios
        .get('/api/rooms')
        .then(response => {
          if (response.data) {
            commit('setRooms', response.data)
          }
        })
        .catch(err => window.console.log(err))
    },

    sendMessage(context, payload) {
      window.axios
        .post('/api/messages', payload)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
        })
        .catch(err => window.console.log(err))
    },

    deleteMessage(context, payload) {
      window.axios
        .delete(`/api/messages/${payload}`)
        .then(response => {
          if (!(response.data && response.data === 'deleted')) {
            window.console.warn(response)
          }
        })
        .catch(err => window.console.log(err))
    },

    createNewRoom(context, payload) {
      window.axios
        .post('api/rooms', payload)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
        })
        .catch(err => window.console.log(err))
    },

    updateRoomProperties(context, payload) {
      window.axios
        .patch(`api/rooms/${payload.id}`, payload)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
        })
        .catch(err => window.console.log(err))
    },

    setReadingProgress(context, room_id) {
      //
      window.axios
        .post(`/api/rooms/${room_id}/setreading`)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
        })
        .catch(err => window.console.log(err))
    },

    leaveRoom({ commit }, payload) {
      window.axios
        .post(`/api/rooms/${payload.id}/leave`)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
          commit('removeRoom', {
            id: payload.id,
            reason: 'user has left this room'
          })
          window.console.log(response.data)
        })
        .catch(err => window.console.log(err))
    },

    getMessagesForRoom({ commit }, payload) {
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

    deleteRoom(x, payload) {
      window.axios
        .delete(`api/rooms/${payload.room_id}`)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
        })
        .catch(err => window.console.log(err))
    },

    joinChatroom({ state, commit, rootState }, payload) {
      // payload must be the current user object!

      // make sure we make a clean start
      window.Echo.leave(state.chatroomName)
      window.Echo.join(state.chatroomName)

        // getting list of all online users
        .here(users => commit('setOnlineUsers', users))

        // adding new online user to the list
        .joining(user => commit('addToOnlineUsers', user))

        // a user logged off
        .leaving(user => commit('removeFromOnlineUsers', user))

        // a room was added
        .listen('RoomCreated', e => {
          if (e.room) {
            // only add this room if current user is a member
            if (e.room.users.find(el => el.id === payload.id)) {
              commit('addRoom', e.room)
              if (e.room.owner_id === rootState.user.user.id)
                state.userCreatedNewRoom = e.room.id
            }
          } else {
            window.console.warn(e)
          }
        })

        // a room was deleted
        .listen('RoomDeleted', e => {
          if (e.room) {
            commit('removeRoom', {
              id: e.room,
              reason: 'chatroom was deleted by owner'
            })
          } else {
            window.console.warn(e)
          }
        })

        // a room was updated (name, members)
        .listen('RoomUpdated', e => {
          if (e.room) {
            // only update this room if current user is a member
            if (e.room.users.find(el => el.id === payload.id)) {
              // check if the current user already has this room
              if (state.rooms.find(el => el.id === e.room.id)) {
                commit('updateRoom', e.room)
              } else {
                commit('addRoom', e.room)
              }
            } else {
              // User is no longer member, remove the room from the store
              commit('removeRoom', {
                id: e.room,
                reason: 'user was removed from this chatroom'
              })
            }
          } else {
            window.console.warn(e)
          }
        })

        .on('pusher:subscription_succeeded', () => {
          window.console.log(
            `Subscription to Presence Channel "${
              state.chatroomName
            }" was successful`
          )
        })
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
