export default {

  state : {
    appName: 'ChatterBox',
    dialog: ''
  },

  mutations : {
    setDialog (state, payload) {
      state.dialog = payload
    }
  }
}