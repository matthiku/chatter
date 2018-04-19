/**
 * central register for all Vuex store modules
 *
 * (C) 2018 Matthias Kuhs
 */
import Vue from 'vue'
import Vuex from 'vuex'

import shared from './shared'
import user from './user'
import chat from './chat'

Vue.use(Vuex)

export const store = new Vuex.Store({
  modules: {
    chat,
    user,
    shared,
  }
})
