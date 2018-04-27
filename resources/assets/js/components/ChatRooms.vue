<template>
  <div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10 col-md-12 mw-1k px-0 px-sm-1">
      <div class="card shadow-sm">

        <div class="card-header all-rooms-header d-flex justify-content-between p-0 p-sm-1 p-md-2">
            
            <span>
              <span class="d-none d-xl-flex">My Chat Rooms</span>
              <span class="d-xl-none">ChatterBox</span>
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

            <div v-for="(room, index) in rooms"
                :key="index"
                class="card mb-1 mb-sm-2">

              
              <!-- chat header
               -->
              <div class="card-header p-0 my-0" :id="'heading-'+room.id">
                <div class="d-flex justify-content-between mb-0 collapsed w-100 p-0 chatroom-header cursor-pointer"
                    data-toggle="collapse" 
                    aria-expanded="true"
                    @click="closeOthers(room.id)"
                    :data-target="'#collapse-'+room.id" 
                    :aria-controls="'#collapse-'+room.id"
                  >
                    <!-- show room name -->
                    <span>
                      <i class="ml-1 material-icons">menu</i>
                      
                      <span v-if="room.name" class="room-name ml-1">{{ room.name }}</span>
                      <span v-else class="small">(unnamed)</span>
                      <i v-if="room.owner_id === user.id"
                          @click="editRoom(room)"
                          title="edit room properties"
                          class="material-icons">edit</i>
                      <i v-else-if="room.id !== 0"
                          @click="leaveRoom(room)"
                          title="leave this chat room"
                          class="material-icons">open_in_new</i>
                    </span>

                    <!-- show room members inline on wider screens -->
                    <chat-show-room-members class="d-none d-md-inline"
                        :room="room" :user="user"
                      ></chat-show-room-members>

                  <!-- show messages counter -->
                  <span>
                    <small class="mr-2">{{ $moment(room.updated_at).fromNow() }}</small> 
                    <span class="badge badge-secondary badge-pill float-right mt-1 mr-1">{{ room.messages ? room.messages.length : 0 }}</span>
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
                  data-parent="#chatrooms">

                <div class="card-body chat-room-body p-0 p-sm-1 p-md-2 p-lg-3 p-xl-4">

                  <chat-log v-if="room.id !== 0"
                      :room="room"></chat-log>

                  <div v-else
                      class="text-center"
                    ><button class="btn btn-sm btn-outline-primary" @click="cleanUpRooms">OK</button>
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
  overflow: hidden;
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
  font-size: larger;
}
.collapsed {
  opacity: 0.6;
  font-size: small;  
}
.collapsed:hover {
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
  },

  computed: {
    rooms () {
      return this.$store.state.chat.rooms
    },
    user () {
      return this.$store.state.user.user
    },
    onlineUsers () {
      return this.$store.state.chat.onlineUsers
    }
  },

  watch: {
    rooms (val) {
      this.rooms.map(room => {

        // check if room is already connected: 
        //          check if the entity "window.Echo.connector.channels"
        //          contains an object with name 'private-chatroom.{id}'
        if (window.Echo.connector.channels[`private-chatroom.${room.id}`]) return

        // start listening to our backend broadcast channel for this Chat Room
        window.Echo.private('chatroom.' + room.id)

          .listen('MessagePosted', e => {
            if (e.message) {
              let msg = e.message
              msg.user = e.user
              room.messages.push(msg)
              // make sure the room gets in first place now
              room.updated_at = msg.updated_at
              this.$store.commit('sortRooms')
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

      // now check if all private chatrooms are still in use
      let privChannels = window.Echo.connector.channels // object with all current channels
      for (const key in privChannels) {
        if (privChannels.hasOwnProperty(key)) {
          // key should be in the form of 'private-chatroom.[id]'
          let chName = key.split('.')
          if (chName.length !== 2 || chName[0] !== 'private-chatroom') continue
          if (this.rooms.find(el => el.id === parseInt(chName[1]))) continue
          window.Echo.leave('chatroom.' + chName[1])
        }
      }
    }
  },

  methods: {
    closeOthers (roomId) {
      let elem = document.getElementById('collapse-'+roomId)
      if (elem.classList.contains('show')) {
        // the current chatroom was closed, now make sure
        // that all other headers are visible again
        this.rooms.map(rm => {
          let hedr = document.getElementById(`heading-${rm.id}`)
          hedr.parentElement.classList.remove('d-none')
        })
      } else {
        // this chatroom was just opened, so hide all others and make this the top one
        this.rooms.map(rm => {
          if (rm.id == roomId) {
            rm.updated_at = (new Date()).toISOString()
            this.$store.commit('sortRooms')
            return
          }
          let hedr = document.getElementById(`heading-${rm.id}`)
          hedr.parentElement.classList.add('d-none')
        })
      }
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

    cleanUpRooms () {
      this.$store.commit('cleanUpRooms')
    }
  }

}
</script>
