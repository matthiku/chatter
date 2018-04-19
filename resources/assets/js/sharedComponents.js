/**
 * central registration of all Vue components
 *
 * (C) 2018 Matthias Kuhs
 */
import Vue from 'vue'

export default function sharedComponents() {
  Vue.component('chat-log', require('./components/ChatLog.vue'))
  Vue.component('chat-rooms', require('./components/ChatRooms.vue'))
  Vue.component('chat-header', require('./components/ChatHeader.vue'))
  Vue.component('chat-message', require('./components/ChatMessage.vue'))
  Vue.component('chat-composer', require('./components/ChatComposer.vue'))
}