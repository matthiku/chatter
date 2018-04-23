<template>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">

        <div class="card-header">Your Chat Rooms
          <button @click="launchNewRoomModal()"
             class="btn btn-sm btn-success float-right">start new chat</button>
        </div>

        <div class="card-body">
          <div class="float-right">Who else is on-line?
            <a href="#" v-for="(u, idx) in onlineUsers" :key="idx"
                v-if="user.id !== u.id"
                class="badge badge-pill badge-info mr-2"
                @click="launchNewRoomModal(u.id)"
                title="click to chat"
              >{{ u.username }}
            </a>
            <span v-if="!onlineUsers">no one</span>
          </div>
          <hr>

          <div class="accordion" id="chatrooms">

            <div v-for="(room, index) in rooms"
                :key="index"
                class="card">

              
              <!-- chat header
               -->
              <div class="card-header p-0" :id="'heading-'+index">
                <h5 class="mb-0">
                  <button class="btn btn-link collapsed w-100" type="button" 
                      data-toggle="collapse" 
                      aria-expanded="true" 
                      :data-target="'#collapse-'+index" 
                      :aria-controls="'#collapse-'+index">

                    <span class="float-left">
                      <span v-if="room.name" class="room-name">{{ room.name }}</span>
                      <span v-else class="small">(unnamed)</span>
                      (<small v-for="(member, index) in room.users"
                          v-if="member.id !== user.id"
                          :key="index"
                          class="font-weight-light">{{ member.username 
                          }}<span v-if="index < room.users.length-1" class="mr-2">,</span>
                      </small>)
                    </span>

                    <span class="float-right"
                        @click.stop="editRoom(room)">
                      <i class="material-icons">messages</i>
                      <span class="badge badge-secondary badge-pill float-right">{{ room.messages ? room.messages.length : 0 }}</span>
                    </span>
                  </button>
                </h5>
              </div>


              <!-- show the actual chat content (the messages) 
               -->
              <div :id="'collapse-'+index" 
                  :aria-labelledby="'heading-'+index"
                  class="collapse"
                  data-parent="#chatrooms">

                <div class="card-body">
                  <chat-log :room="room"
                    ></chat-log>
                </div>
              </div>

            </div> <!-- end of LOOP: room in rooms -->

          </div>
        </div>

      </div>
    </div>

    <chat-room-properties></chat-room-properties>

  </div>
</template>


<style>
.room-name {
  font-family: 'Times New Roman', Times, serif;
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
      window.console.log('rooms changed!')

      this.rooms.map(room => {        
        // check if room is already connected: 
        //          check if the entity "window.Echo.connector.channels"
        //          contains an object with name 'private-chatroom.{id}'
        if (window.Echo.connector.channels[`private-chatroom.${room.id}`]) return

        // start listening to our backend broadcast channel dedicated to a certain Chat Room
        window.Echo.private('chatroom.' + room.id)

          .listen('MessagePosted', e => {
            if (e.message) {
              let msg = e.message
              msg.user = e.user
              if (! room.messages) room.messages = []
              room.messages.push(msg)
            } else {
              window.console.warn(e)
            }
          })
          .on('pusher:subscription_succeeded', e => {
            window.console.log(`Subscription to chatroom ${room.id} was successful`)
          })        
      })

      // now check if all private chatrooms are still valid
      let privChannels = window.Echo.connector.channels
      for (const key in privChannels) {
        if (privChannels.hasOwnProperty(key)) {
          // key should be in the form of 'private-chatroom.[id]'
          let chName = key.split('.')
          window.console.log(chName)
          if (chName.length !== 2 || isNaN(chName[1])) continue
          if (this.rooms.find(el => el.id === parseInt(chName[1]))) continue
          window.console.log('leaving chatroom', chName[1])
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
    }
  }

}
</script>
