/**
 * load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')

window.Vue = require('vue')

// central Vuex store
import { store } from './store'

/**
 * create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 */

import sharedComponents from './sharedComponents'
sharedComponents()

import startUpActions from './startUpActions'

new window.Vue({
  el: '#app',

  store,

  data: {
  },

  methods: {
  },

  created() {
    startUpActions(store)
  }
})
