/**
 * load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')

window.Vue = require('vue')

Vue.config.productionTip = false

// provide the moment library to all components
import moment from 'moment-timezone'
moment.tz.setDefault('UTC')
// add moment to the Vue prototype, so that we can use it in all components!
Object.defineProperty(Vue.prototype, '$moment', {
  get() {
    return this.$root.moment
  }
})

// central Vuex store
import { store } from './store'

// single component files
import sharedComponents from './sharedComponents'
sharedComponents()

import startUpActions from './startUpActions'

new window.Vue({
  el: '#app',

  store,

  data: { moment },

  created() {
    startUpActions(store)
  }
})
