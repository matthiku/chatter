<template>
  <div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10 col-md-12 mw-1k px-0 px-sm-1">
      <div class="card shadow-sm">

        <ShowPageHeader
            :activeRoom="activeRoom"
            @open-new-message="openNewMessage"
            @close-all-chats="closeAllChats"
          ></ShowPageHeader>

        <div class="card-body chatroom-canvas p-0 p-sm-1 p-md-2 p-lg-3 p-xl-4">

          <div class="accordion shadow" id="chatrooms">

            <ChatRoom
                v-for="(room, index) in rooms"
                v-if="activeRoom === null || activeRoom === room.id"
                :key="index"
                :room="room"
                :activeRoom="activeRoom"
                @close-all-chats="closeAllChats"
                @set-active-room="setActiveRoom"
                @user-read-all-messages="userReadAllMessages"
                class="card mb-1 mb-sm-2 every-chatrooms-card"
              ></ChatRoom>

          </div>
        </div>

      </div>

      <ShowPageFooter
          v-if="!activeRoom"
        ></ShowPageFooter>

    </div>

    <!-- modal for user settings -->
    <UserSettings></UserSettings>

    <!-- modal dialog to edit chat room properties or create a new room -->
    <EditRoomProperties></EditRoomProperties>

  </div>
</template>


<style>
body {
  height: 100%;
  /* overflow: hidden; */
}
.all-rooms-header {
  background-color: darkseagreen;  
}
.every-chatrooms-card:nth-child(even) {
  background-color: beige;
}
.mw-1k {
  max-width: 900px;
}
.chat-log {
  overflow-y: scroll;
}
.material-icons {
  font-size: 1rem;
}
.cursor-pointer {
  cursor: pointer;
}
</style>


<script>
export default {

  mounted () {
    window.addEventListener('resize', function() {
      console.log(window.innerHeight)
    });
    this.onlyOneRoom()
  },

  computed: {
    appName () {
      return this.$store.state.shared.appName
    },
    chatroomName () {
      return this.$store.state.shared.chatroomName
    },
    rooms () {
      return this.$store.state.chat.rooms
    },
    user () {
      return this.$store.state.user.user
    },
    onlineUsers () {
      return this.$store.state.chat.onlineUsers
    },
    userCreatedNewRoom () {
      return this.$store.state.chat.userCreatedNewRoom
    },
    newMessagesArrived () {
      return this.$store.state.chat.newMessagesArrived
    },
    frontendTimestamp () {
      return this.$store.state.shared.frontendTimestamp
    },
    latestFrontendVersion () {
      return this.$store.state.shared.latestFrontendVersion
    }
  },

  data () {
    return {
      firstRun: true,
      secondRun: false,
      activeRoom: null
    }
  },

  watch: {
    newMessagesArrived (val) {
      // update page title when this entity changes
      this.setPageTitle()
      // open the chatroom of the first new message if no room is currently open
      if (val.length && !this.activeRoom) {
        this.activeRoom = val[0].room.id
      }
    },

    activeRoom (val) {
      if (this.activeRoom === 0) this.activeRoom = null
      this.setPageTitle()

      // check if this user has the full reading progress for this room
      if (!val) return
      let room = this.rooms.find(el => el.id === val)
      let roomLastUpdate = room.updated_at
      let usersReadingProgress = room.users.find(el => el.id === this.user.id).pivot.updated_at
      // update the reading progress of this user for this room
      if (this.$moment(usersReadingProgress).isBefore(this.$moment(roomLastUpdate))) {
        this.$store.dispatch('setReadingProgress', val)
      }
    },

    onlineUsers () {
      this.setPageTitle()
    },

    rooms () {
      this.rooms.map(room => {

        // check if room is already connected: 
        //          check if the entity "window.Echo.connector.channels"
        //          contains an object with name 'private-{chatroomName}.chatroom.{id}'
        if (window.Echo.connector.channels[`private-${this.chatroomName}.chatroom.${room.id}`]) return

        // start listening to our backend broadcast channel for this Chat Room
        window.Echo.private(this.chatroomName + '.chatroom.' + room.id)

          .listen('MessagePosted', e => {
            if (e.message) {
              let msg = e.message
              msg.user = e.user
              room.messages.push(msg)
              // make sure the room gets in first place now
              room.updated_at = msg.updated_at
              // show warning for new messages (but not this user's own msg)
              // REMOVED: and not when the room is already open! "&& msg.room_id !== this.activeRoom"
              if (e.user.id !== this.user.id) {
                this.$store.commit('addToNewMessagesArrived', msg)
                // TODO: play a sound!
                // add background notification!
                if (! ('Notification' in window)) {
                  alert('Web Notification is not supported')
                } else {
                  Notification.requestPermission( permission => {
                    let notification = new Notification(
                      'New message from ' + msg.user.username,
                      { body: `Room: ${msg.room.name},\nText: ${msg.message}` }
                    )
                    notification.onclick = () => {
                      // TODO: open this specific room!
                      window.open(window.location.href)
                    }
                  })
                }
              }
              this.$store.commit('sortRooms') // make sure the room list is refreshed
            } else {
              window.console.warn(e)
            }
          })

          .listen('MessageUpdated', e => {
            if (e.message) {
              let msg = e.message
              msg.user = e.user
              // find and replace the old message
              let idx = room.messages.findIndex(el => el.id === msg.id)
              room.messages[idx] = msg
              // only to trigger the reactivity!
              room.messages.push(msg) 
              room.messages.pop()
            } else {
              window.console.warn(e)
            }
          })

          .listen('RoomTyping', e => {
            if (e.user) {
              room.users.forEach(usr => {
                if (usr.id === e.user.id)
                  this.$set(usr, 'typing', new Date())
              })
            } else {
              window.console.warn(e)
            }
          })

          .on('pusher:subscription_succeeded', e => {
            window.console.log(`Subscription to chatroom ${room.id} was successful`)
          })        
      })

      this.onlyOneRoom() // check if we have only one room

      // now check if all private chatrooms are still in use
      let privChannels = window.Echo.connector.channels // object with all current channels
      for (const key in privChannels) {
        if (privChannels.hasOwnProperty(key)) {
          // key should be in the format 'private-chatroom.[id]'
          let chName = key.split('.')
          if (chName.length !== 2 || chName[0] !== 'private-chatroom') continue
          if (this.rooms.find(el => el.id === parseInt(chName[1]))) continue
          window.Echo.leave('chatroom.' + chName[1])
        }
      }

      this.setPageTitle()
    }
  },

  methods: {
    setActiveRoom (val) {
      this.activeRoom = val
    },

    setPageTitle () {
      let windowTitle = '(idle)'
      if (this.onlineUsers.length > 1) {
        if (this.onlineUsers.length === 2) {
          let otherUser = this.onlineUsers.find(el => el.id !== this.user.id).username
          windowTitle = ` - ${otherUser} is online`
        }
        else
          windowTitle = ' -' + (this.onlineUsers.length-1) + ' users online'
      }
      // show name of open chat room, if any
      if (this.activeRoom) {
        windowTitle = this.rooms.find(el => el.id === this.activeRoom).name
      }
      let newMsgCount = this.newMessagesArrived.length
      // if there are new messages, show name of first chat containing new messages
      if (newMsgCount) {
        windowTitle = this.newMessagesArrived[0].room.name
        newMsgCount = `(${newMsgCount}) `
      } else {
        newMsgCount = ''
      }

      window.document.title = `${this.appName} ${newMsgCount}${windowTitle}`

      // make sure we are properly connected to the presence channel
      this.safetyCheck()
    },

    openNewMessage () {
      // show room for which a new message has arrived! (newMessagesArrived)
      let msg = this.newMessagesArrived[0]
      let elem = document.getElementById('collapse-' + msg.room_id)
      elem.classList.add('show')
      this.activeRoom = msg.room_id
      // mark this message as read
      this.userReadAllMessages(msg.room_id)
    },

    closeAllChats () {
      this.activeRoom = null
    },

    onlyOneRoom () {
      // if only one room exists, open it by default
      if (this.rooms.length === 1) {
        this.activeRoom = this.rooms[0].id
      }
      // check URL to see if a specific room was requested
      let search = window.location.search
      let roomId
      if (search) {
        let sr = search.split('room=')
        if (sr[1]) {
          roomId = parseInt(sr[1].split('&')[0])
        }
      }
      if (roomId && this.rooms.length > 1) {
        this.activeRoom = roomId
        // remove the searchstring from the URL
        window.history.pushState({}, document.title, "/home")
      }
    },

    userReadAllMessages (roomId) {
      // remove messages from this array which the user has seen (because he opened the room)
      this.$store.commit('clearRoomFromNewMessagesArrived', roomId)
    },

    safetyCheck () {
      // Safety Check - Make sure the presence channel has an active subscription!
      if (this.firstRun) { // but not on the first run, as the async action is not complete yet
        this.firstRun = false
        return
      }
      if (! window.Echo.connector.channels['presence-'+chatter_server_data.chatroom_name].subscription.subscribed) {
        if (this.secondRun) {
          window.console.warn('Presence channel not active! Re-loading page!')
          // window.location.reload()
        } else {
          window.console.warn('Presence channel not active! Re-Joining it now!')
          this.$store.dispatch('joinChatroom', this.user)
          this.secondRun = true
        }
      }
    }
  },

  updated () {
    // check if the activeRoom still exists in the list of rooms
    let foundActive = false
    this.rooms.map((el) => {
      if (el.id === this.activeRoom) foundActive = true

      // check if user created a new room, then open it
      if (el.id === this.userCreatedNewRoom) {
        // first, we need to close any other open room
        if (this.activeRoom !== null) {
          this.activeRoom = null
        }
        let elem = document.getElementById('collapse-' + el.id)
        if (elem) elem.classList.add('show')
        this.$store.commit('clearUserCreatedNewRoom')
        foundActive = true
        this.activeRoom = el.id
      }
    })
    if (!foundActive) this.activeRoom = null

    this.safetyCheck()
  }

}
</script>
