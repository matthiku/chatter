export default {

  state: {
    user: {}
  },


  mutations: {
    setUser(state, payload) {
      state.user = payload
    }
  },


  actions: {

  },


  getters: {
    user (state) {
      return state.user
    }
  }

}
