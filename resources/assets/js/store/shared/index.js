export default {
  state: {
    appName: 'ChatterBox',
    chatroomName: 'chatroom',
    dialog: '',
    action: null,
    frontendTimestamp: null,
    latestFrontendVersion: null
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
    },
    setFrontendTimestamp (state, payload) {
      state.frontendTimestamp = payload
    },
    setLatestFrontendVersion (state, payload) {
      state.latestFrontendVersion = payload
    }
  },

  actions: {
    getLatestFrontendVersion ({commit, dispatch, rootState}) {
      window.axios
        .get('/api/getlatestfrontendversion')
        .then(response => {
          if (!response.data) {
            window.console.warn(response)
          }
          if (response.data.frontendVersion) {
            commit('setLatestFrontendVersion', response.data.frontendVersion)
            // setTimeout to repeat this
            setTimeout(() => {
              dispatch('getLatestFrontendVersion')
            }, 60000)
          }
        })
        .catch(err => rootState.commit('axiosError', err))
    }
  }
}
