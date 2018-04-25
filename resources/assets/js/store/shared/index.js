export default {
  state: {
    appName: 'ChatterBox',
    chatroomName: 'chatroom',
    dialog: '',
    action: null
  },

  mutations: {
    setDialog (state, payload) {
      state.dialog = payload
    },
    setAction (state, payload) {
      state.action = payload
    },
    setChatroomName (state, payload) {
      state.chatroomName = payload
    },
    addRoom (state, payload) {
      state.action = { type: 'roomAdded', what: payload }
    }
  }
}
