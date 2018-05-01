<template>
  <div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10 col-md-12 mw-1k px-0 px-sm-1">
      <div class="card shadow-sm">

        <div class="card-header all-rooms-header d-flex justify-content-between p-0 p-sm-1 p-md-2">
            
            <!-- show page title and main menu -->
            <span>
              <div class="dropdown d-inline">
                <a class="chatter-menu btn btn-secondary btn-sm dropdown-toggle" 
                    href="#" role="button" 
                    id="dropdownMenuLink" 
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">more_vert</i>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                  <a class="dropdown-item" href="#">Settings (WIP)</a>
                  <a class="dropdown-item" href="#">Another action</a>
                  <a class="dropdown-item" href="#" @click="logoff">Logoff</a>
                </div>
              </div>

              <span @click="closeAllChats"
                  :class="[activeRoom ? 'cursor-pointer' : '']">
                <span class="d-none d-xl-inline">All Chat Rooms</span>
                <span class="d-xl-none">{{ appName }}</span>
                <span class="border border-light rounded-circle text-center px-1">{{ rooms.length }}</span>
              </span>              

              <span v-if="newMessagesArrived.length"
                  title="click to open new message"
                  @click="openNewMessage"
                  class="cursor-pointer badge badge-danger">{{ newMessagesArrived.length }}</span>
            </span>

            <!-- show online users -->
            <chat-show-online-members
                :onlineUsers="onlineUsers" :user="user">
            </chat-show-online-members>

            <button @click="launchNewRoomModal()"
                title="create a new chat room"
                class="btn btn-sm btn-success float-right"
              ><i class="material-icons">add</i>
              <span class="d-none d-md-inline">new chat</span>  
            </button>

        </div>

        <div class="card-body p-0 p-sm-1 p-md-2 p-lg-3 p-xl-4">

          <div class="accordion shadow" id="chatrooms">

            <div v-for="(room, index) in rooms" :key="index"
                v-if="activeRoom === null || activeRoom === room.id"
                class="card mb-1 mb-sm-2">

              
              <!-- chatRoom header
               -->
              <div class="card-header p-0 my-0" :id="'heading-'+room.id">
                <div class="d-flex justify-content-between mb-0 w-100 p-0 chatroom-header cursor-pointer"
                    @click="hideOtherRooms(room.id)"
                    aria-expanded="true"
                    :aria-controls="'#collapse-'+room.id"
                    data-toggle="collapse"
                    :data-target="'#collapse-'+room.id"
                  >
                    <!-- show room name -->
                    <span>
                      <i class="ml-1 material-icons">menu</i>
                      
                      <span v-if="room.name" class="room-name ml-1">{{ room.name }}</span>
                      <span v-else class="small">(unnamed)</span>
                      <i v-if="room.owner_id === user.id"
                          @click.stop="editRoom(room)"
                          title="edit room properties"
                          class="material-icons">edit</i>
                      <i v-else-if="room.id !== 0"
                          @click.stop="leaveRoom(room)"
                          title="leave this chat room"
                          class="material-icons">open_in_new</i>
                    </span>

                    <!-- show room members inline on wider screens -->
                    <chat-show-room-members class="d-none d-md-inline"
                        :room="room" :user="user"
                      ></chat-show-room-members>

                  <!-- show messages counter -->
                  <span>
                    <!-- <small class="mr-2">{{ $moment(room.updated_at).fromNow() }}</small> -->
                    <span v-if="newMessagesArrived.length && messagesForThisRoom(room.id)"
                        class="badge badge-danger badge-pill mt-1 mr-">{{ messagesForThisRoom(room.id) }}</span>
                    <span class="badge badge-secondary badge-pill mt-1 mr-1">{{ room.messages ? room.messages.length : 0 }}</span>
                  </span>
                </div>

                <!-- show room members on extra line on smaller screens -->
                <chat-show-room-members class="d-md-none"
                    :room="room" :user="user"
                  ></chat-show-room-members>

              </div>


              <!-- show the actual chat content (the messages) 
               -->
              <div :id="'collapse-'+room.id" 
                  :aria-labelledby="'heading-'+room.id"
                  class="collapse"
                  :class="[rooms.length===1 || activeRoom === room.id ? 'show' : '']"
                  data-parent="#chatrooms">

                <div class="card-body chat-room-body p-0 p-sm-1 p-md-2 p-lg-3 p-xl-4">

                  <chat-log v-if="room.id !== 0"
                      :room="room"></chat-log>

                  <div v-else
                      class="text-center"
                    ><button class="btn btn-sm btn-outline-primary" @click="delayedCleanUp">OK</button>
                  </div>

                </div>
              </div>


            </div> <!-- end of LOOP (room in rooms) -->

          </div>
        </div>

      </div>
    </div>

    <!-- modal dialog to edit chat room properties or create a new room -->
    <chat-room-properties></chat-room-properties>

  </div>
</template>


<style>
body {
  height: 100%;
  /* overflow: hidden; */
}
.chatter-menu.dropdown-toggle::after {
  content: none;
}
.all-rooms-header {
  background-color: darkseagreen;  
}
.mw-1k {
  max-width: 900px;
}
.room-name {
  font-family: 'Times New Roman', Times, serif;
  font-size: larger;
  color:darkred;
}
.chatroom-header {
  opacity: 0.6;
  font-size: small;  
}
.chatroom-header:hover {
  opacity: 1;
  font-size: inherit;
}
.chat-room-body {
  background-image: url("/static/paper.gif");
  background-repeat: repeat;  
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
    }
  },

  data () {
    return {
      firstRun: true,
      secondRun: false,
      activeRoom: null,
      newMessagesArrived: []
    }
  },

  watch: {
    activeRoom (val) {
      if (this.activeRoom === 0) this.activeRoom = null
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
              // show warning for new message (but not this user's own msg and not when the room is already open!)
              if (e.user.id !== this.user.id && msg.room_id !== this.activeRoom) {
                this.newMessagesArrived.push(msg)
                // TODO: play a sound!
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
    }
  },

  methods: {
    openNewMessage () {
      // show room for which a new message has arrived! (newMessagesArrived)
      let msg = this.newMessagesArrived[0]
      let elem = document.getElementById('collapse-' + msg.room_id)
      elem.classList.add('show')
      this.activeRoom = msg.room_id
      // mark this message as read
      this.userReadAllMessages(msg.room_id)
    },

    messagesForThisRoom(roomId) {
      let num = 0
      this.newMessagesArrived.map(el => {
        if (el.room_id === roomId) num += 1
      })
      return num
    },

    hideOtherRooms (roomId) {
      let elem = document.getElementById('collapse-'+roomId)
      if (elem.classList.contains('show')) {
        this.activeRoom = null
      } else {
        this.activeRoom = roomId
        if (roomId !== 0) this.cleanUpRooms()
        this.userReadAllMessages(roomId)
      }
    },

    closeAllChats () {
      this.activeRoom = null
    },

    launchNewRoomModal (user_id) {
      if (user_id)
        this.$store.commit('setNewRoomMembers', [user_id])
      else
        this.$store.commit('setNewRoomMembers', [])
      this.$store.commit('setDialog', {what: 'createNewRoom', option: ''})
    },

    editRoom (room) {
      // only for the owner...
      if (room.owner_id !== this.user.id) return
      // edit room name and memberships
      let members = []
      room.users.map(el => members.push(el.id))
      this.$store.commit('setNewRoomMembers', members)
      this.$store.commit('setDialog',
        {
          what: 'updateRoom',
          option: room.id,
          roomName: room.name
        }
      )
    },

    leaveRoom (room) {
      this.$store.dispatch('sendMessage', {
        message: `user ${this.user.username} has left this chatroom`,
        room_id: room.id
      })
      this.$store.dispatch('leaveRoom', room)
    },

    onlyOneRoom () {
      // if only one room exists, open it by default
      if (this.rooms.length === 1) {
        this.activeRoom = this.rooms[0].id
      }
    },

    cleanUpRooms () {
      // make sure 'leftover' rooms are removed; ie. rooms
      //    of which the current user is no longer a member
      this.$store.commit('cleanUpRooms')
    },

    delayedCleanUp () {
      let elem = document.getElementById('collapse-0')
      elem.classList.remove('show')
      this.cleanUpRooms()
    },

    userReadAllMessages (roomId) {
      // remove messages from this array which the user has seen (because he opened the room)
      this.newMessagesArrived = this.newMessagesArrived.filter(el => {
        return el.room_id !== roomId
      })      
    },

    logoff () {
      document.getElementById('logout-form').submit()
    }
  },

  updated () {
    let foundActive = false
    // check if the activeRoom still exists in the list of rooms
    this.rooms.map((el) => {
      if (el.id === this.activeRoom) foundActive = true

      // check if user created a new room, then open it
      if (el.id === this.userCreatedNewRoom) {
        let elem = document.getElementById('collapse-' + el.id)
        elem.classList.add('show')
        this.$store.commit('clearUserCreatedNewRoom')
        foundActive = true
        this.activeRoom = el.id
      }
    })
    if (!foundActive) this.activeRoom = null
    

    // Safety Check - Look if the presence channel has an active subscription!
    if (this.firstRun) { // but not on the first run, as the async action is not complete yet
      this.firstRun = false
      return
    }
    if (! window.Echo.connector.channels['presence-'+chatter_server_data.chatroom_name].subscription.subscribed) {
      if (this.secondRun) {
        window.location.reload()
      } else {
        window.console.warn('Presence channel not active! Re-Joining it now!')
        this.$store.dispatch('joinChatroom', this.user)
        this.secondRun = true
      }
    }
  }

}
</script>
