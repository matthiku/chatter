/**
 * startUpActions is called from (or imported into) the main.js module
 * and used in the Vue launch block.
 *
 * (C) 2018 Matthias Kuhs
 *
 * @param {*} store   The Vuex store object as defined in main.js
 *
 */
export default function startUpActions(store) {
  // get the user object from the global namespace (as set in layouts\app.blade.php)
  let user = JSON.parse(window.chatter_server_data.user)
  store.commit('setUser', user)
  
  // get the timestamp of current frontend base file (/public/js/app.js)
  let frontendTimestamp = JSON.parse(window.chatter_server_data.frontend_timestamp)
  store.commit('setFrontendTimestamp', frontendTimestamp)
  
  // If the user is logged in, get all his Chat Rooms from the backend
  if (user.name && user.name !== 'guest') {
  
    // get central chatroom name from server
    let chatroomName = window.chatter_server_data.chatroom_name
    store.commit('setChatroomName', chatroomName)

    // load the rooms for this user
    store.dispatch('loadRooms')

    // load simple list of all users
    store.dispatch('loadUsers')

    // start listening to our backend broadcast channel
    store.dispatch('joinChatroom', user)
  }
}
