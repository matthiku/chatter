export default {
  state: {
    appName: 'ChatterBox',
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
    addRoom (state, payload) {
      state.action = { type: 'roomAdded', what: payload }
    }
  }
}
