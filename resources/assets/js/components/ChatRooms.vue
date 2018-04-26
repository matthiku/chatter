<template>
  <div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10 col-md-12 mw-1k">
      <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between p-0 p-sm-1 p-md-2">
            
            <span>My Chat Rooms</span>

            <!-- show online users -->
            <div>On-line:
              <a href="#" v-for="(u, idx) in onlineUsers" :key="idx"
                  v-if="user.id !== u.id"
                  class="badge badge-pill badge-info mr-2"
                  @click="launchNewRoomModal(u.id)"
                  :title="'click to start chatting with ' + u.username"
                >{{ u.username }}
              </a>
            </div>
            <span v-if="!onlineUsers">no one</span>

            <button @click="launchNewRoomModal()"
                class="btn btn-sm btn-success float-right">new chat</button>

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
                    :data-target="'#collapse-'+room.id" 
                    :aria-controls="'#collapse-'+room.id"
                  >
                    <!-- show room name -->
                    <span>
                      <span v-if="room.name" class="room-name ml-2">{{ room.name }}</span>
                      <span v-else class="small">(unnamed)</span>
                      <i v-if="room.owner_id === user.id"
                          @click="editRoom(room)"
                          title="edit room properties"
                          class="material-icons">edit</i>
                      <i v-else-if="room.id !== 0"
                          @click="leaveRoom(room)"
                          title="leave this chat room"
                          class="material-icons">cancel</i>
                    </span>

                    <!-- show room members -->
                    <span class="room-member-names">
                      <small v-for="(member, index) in room.users"
                          v-if="member.id !== user.id"
                          :key="index"
                          :title="member.id === room.owner_id ? 'Chat Owner' : member.name"
                          :class="[member.id === room.owner_id ? 'font-weight-bold' : 'font-weight-light']"
                        >{{ member.username 
                          }}<span v-if="index < room.users.length-1" class="mr-2">,</span>
                      </small>
                    </span>

                  <!-- show messages counter -->
                  <span>
                    <span class="badge badge-secondary badge-pill float-right mt-1 mr-1">{{ room.messages ? room.messages.length : 0 }}</span>
                  </span>
                </div>
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
.material-icons {
  font-size: smaller;
}
.cursor-pointer {
  cursor: pointer;
}
</style>


<script>
export default {

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
