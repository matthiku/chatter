/**
 * startUpActions is called from (imported into) the main.js module, and used in the Vue launch block.
 *
 * (C) 2018 Matthias Kuhs
 *
 * @param {*} store   The Vuex store object as defined in main.js
 * @param {*} router  The Vue router object
 */
export default function startUpActions(store) {
  // get the user object from the global namespace (set in layouts\app.blade.php)
  let user = JSON.parse(window.chatter_server_data.user)
  store.commit('setUser', user)

  // get all rooms from the backend, but only when user was logged in
  if (user.name && user.name !== 'guest') {
    // load the rooms for this user
    store.dispatch('loadRooms')

    // start listening to our backend broadcast channel
    window.Echo.join('chatroom')

      .here(users => {
        // getting list of all users logged into this room
        store.commit('setUsersInRoom', users)
      })

      .joining(user => this.usersInRoom.push(user))

      .leaving(
        user => (this.usersInRoom = this.usersInRoom.filter(u => u !== user))
      )
  }
}
