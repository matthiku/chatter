/**
 * Register and configure all Vue frontend routes
 * and their respective single component files
 *
 * (C) 2018 Matthias Kuhs
 */
import Vue from 'vue'
import Router from 'vue-router'

// import AuthGuard from './auth-guard'

import ChatRooms from '../components/ChatRooms'

Vue.use(Router)

export default new Router({
  mode: 'history',

  routes: [
    {
      path: '/',
      component: ChatRooms
    },
    {
      path: '/index.php',
      component: ChatRooms
    },
    {
      path: '/home',
      component: ChatRooms,
    },
  ]
})