/**
 * startUpActions is called from (or imported into) the main.js module
 * and used in the Vue launch block.
 *
 * (C) 2018 Matthias Kuhs
 *
 * @param {*} store   The Vuex store object as defined in main.js
 * @param {*} router  The Vue router object
 */
export default function startUpActions(store) {
  // get the user object from the global namespace (as set in layouts\app.blade.php)
  let user = JSON.parse(window.chatter_server_data.user)
  store.commit('setUser', user)

  // If the user is logged in, get all his Chat Rooms from the backend
  if (user.name && user.name !== 'guest') {
    // load the rooms for this user
    store.dispatch('loadRooms')

    // load simple list of all users
    store.dispatch('loadUsers')

    // start listening to our backend broadcast channel
    window.Echo.join('chatroom')

      // getting list of all online users 
      .here(users => store.commit('setOnlineUsers', users))

      // adding new online user to the list
      .joining(user => store.commit('addToOnlineUsers', user))

      // a user logged off
      .leaving(user => store.commit('removeFromOnlineUsers', user))

      // a room was added
      .listen('RoomCreated', e => {
        if (e.room) {
          // only add this room if current user is a member
          if (e.room.users.find(el => el.id === user.id))
            store.commit('addRoom', e.room)
        } else {
          window.console.warn(e)
        }
      })

      // a room was deleted
      .listen('RoomDeleted', e => {
        if (e.room) {
          store.commit('removeRoom', {id: e.room, reason: 'chatroom was deleted by owner'})
        } else {
          window.console.warn(e)
        }
      })

      // a room was updated (name, members)
      .listen('RoomUpdated', e => {
        if (e.room) {
          let rooms = store.getters.rooms
          // only update this room if current user is a member
          if (e.room.users.find(el => el.id === user.id)) {
            // check if the current user already has this room
            if (rooms.find(el => el.id === e.room.id)) {
              store.commit('updateRoom', e.room)
            } else {
              store.commit('addRoom', e.room)
            }
          } else {
            // User is no longer member, remove the room from the store
            store.commit('removeRoom', {id: e.room, reason: 'user was removed from this chatroom'})
          }
        } else {
          window.console.warn(e)
        }
      })
  }
}
