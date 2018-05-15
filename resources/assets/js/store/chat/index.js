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
      state.rooms.map(elem => {
        if (elem.id === payload.id) {
          elem.name = payload.name
          elem.users = payload.users
        }
      })
    },
    setEmailNotification(state, payload) {
      state.rooms.map(elem => {
        if (elem.id === payload.room_id) {
          elem.pivot.email_notification = payload.emailNotification
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
    },
    axiosError(state, error) {
      window.console.warn(error)
      if (error.response) {
        // The request was made and the server responded with a status code
        // that falls out of the range of 2xx
        window.console.log(error.response.data);
        window.console.log(error.response.status);
        window.console.log(error.response.headers);
        if (error.response.status === 401) {
          // user is not authenticated, redirect to the login page
          window.location = '/login'
          // TODO: better is to redirect to a page with a message (like session has expired)
        }
      } else if (error.request) {
        // The request was made but no response was received
        // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
        // http.ClientRequest in node.js
        window.console.log(error.request);
      } else {
        // Something happened in setting up the request that triggered an Error
        window.console.log('Error', error.message);
      }
      window.console.log(error.config);
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
        .catch(err => commit('axiosError', err))
    },

    sendMessage({ commit }, payload) {
      window.axios
        .post('/api/messages', payload)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
          if (response.data.frontendVersion) {
            commit('setLatestFrontendVersion', response.data.frontendVersion)
          }
        })
        .catch(err => commit('axiosError', err))
    },

    deleteMessage({ commit }, payload) {
      window.axios
        .delete(`/api/messages/${payload}`)
        .then(response => {
          if (!(response.data && response.data === 'deleted')) {
            window.console.warn(response)
          }
        })
        .catch(err => commit('axiosError', err))
    },

    createNewRoom({ commit }, payload) {
      window.axios
        .post('api/rooms', payload)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
        })
        .catch(err => commit('axiosError', err))
    },

    updateRoomProperties({ commit }, payload) {
      window.axios
        .patch(`api/rooms/${payload.id}`, payload)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
        })
        .catch(err => commit('axiosError', err))
    },

    setEmailNotification({ commit }, payload) {
      window.axios
        .post(
          `/api/rooms/${payload.room_id}/setemailnotification`,
          payload
        )
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
          commit('setEmailNotification', payload)
        })
        .catch(err => commit('axiosError', err))
    },

    setReadingProgress({ commit }, room_id) {
      //
      window.axios
        .post(`/api/rooms/${room_id}/setreading`)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
        })
        .catch(err => commit('axiosError', err))
    },

    userIsTyping({ commit }, room_id) {
      // backend will trigger the broadcast to other room members
      window.axios
        .post(`/api/rooms/${room_id}/typing`)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
          if (response.data.frontendVersion) {
            commit('setLatestFrontendVersion', response.data.frontendVersion)
          }
        })
        .catch(err => commit('axiosError', err))
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
        .catch(err => commit('axiosError', err))
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
        .catch(err => commit('axiosError', err))
    },

    deleteRoom({commit}, payload) {
      window.axios
        .delete(`api/rooms/${payload.room_id}`)
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
        })
        .catch(err => commit('axiosError', err))
    },

    joinChatroom({ state, commit, dispatch, rootState }, payload) {
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

        // there was a change in the users list
        .listen('UsersChanged', e => {
          dispatch('loadUsers')
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
