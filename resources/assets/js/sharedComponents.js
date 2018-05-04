/**
 * central registration of all Vue components
 *
 * (C) 2018 Matthias Kuhs
 */
import Vue from 'vue'

export default function sharedComponents() {
  Vue.component('chat-log', require('./components/ChatLog.vue'))
  Vue.component('chat-room', require('./components/ChatRoom.vue'))
  Vue.component('chat-rooms', require('./components/ChatRooms.vue'))
  
  Vue.component('chat-edit-message', require('./components/Edit/ComposeMessage.vue'))
  Vue.component('chat-room-properties', require('./components/Edit/editRoomProperties.vue'))

  Vue.component('chat-show-message', require('./components/Show/Message.vue'))
  Vue.component('chat-show-emoticons', require('./components/Show/Emoticons.vue'))
  Vue.component('chat-show-page-header', require('./components/Show/PageHeader.vue'))
  Vue.component('chat-show-room-members', require('./components/Show/RoomMembers.vue'))
  Vue.component('chat-show-online-members', require('./components/Show/OnlineMembers.vue'))
  Vue.component('chat-show-reading-progress', require('./components/Show/ReadingProgress.vue'))
}
