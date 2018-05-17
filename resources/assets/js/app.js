/**
 * load all of this project's JavaScript dependencies which
 * includes Vue and other libraries.
 */

require('./bootstrap')

var Vue = require('vue')

import router from './router'

Vue.config.productionTip = false
Vue.config.devtools = true

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

new Vue({
  el: '#app',

  router,

  store,

  data: { moment },

  created() {
    startUpActions(store)
  }
})
