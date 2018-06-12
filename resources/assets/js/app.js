/**
 * Load all of this project's JavaScript dependencies which
 * includes Vue and other libraries.
 */

require('./bootstrap')

var Vue = require('vue')

import router from './router'

Vue.config.productionTip = false
Vue.config.devtools = true

// Provide the moment library to all components
import moment from 'moment-timezone'
moment.tz.setDefault('UTC')
// add moment to the Vue prototype, so that we can use it in all components!
Object.defineProperty(Vue.prototype, '$moment', {
  get() {
    return this.$root.moment
  }
})

// Central Vuex store
import { store } from './store'

// Register all Single File Components
import sharedComponents from './sharedComponents'
sharedComponents()

// Load values from the document into the store
import startUpActions from './startUpActions'

new Vue({
  el: '#app',

  router,

  store,

  data: { moment },

  created() {
    startUpActions(store)
  }
})
