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
import UserLogin from '../components/User/Login'

Vue.use(Router)

export default new Router({
  mode: 'history',

  routes: [
    {
      path: '/',
      redirect: '/home'
    },
    {
      path: '/index.php',
      redirect: '/home'
    },
    {
      path: '/home',
      component: ChatRooms,
    },
    {
      path: '/logi',
      component: UserLogin,
    },
  ]
})