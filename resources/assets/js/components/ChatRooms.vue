<template>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">

        <div class="card-header">Your Chat Rooms
          <button @click="launchNewRoomModal()"
             class="btn btn-sm btn-success float-right">start new chat</button>
        </div>

        <div class="card-body">
          <div class="float-right">Currently on-line:
            <a href="#" v-for="(u, idx) in onlineUsers" :key="idx"
                v-if="user.id !== u.id"
                class="badge badge-pill badge-info mr-2"
                @click="launchNewRoomModal(u.id)"
                title="click to chat"
              >{{ u.username }}
            </a>
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
                      {{ room.name }}
                      (<small v-for="(member, index) in room.users"
                          v-if="member.id !== user.id"
                          :key="index"
                          class="font-weight-light">{{ member.username 
                          }}<span v-if="index < room.users.length-1">,</span>
                      </small>)
                    </span>

                    <span class="float-right"
                        @click.stop="editRoom(room)">
                      <i class="material-icons">people</i>
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
              room.messages.push(msg)
            } else {
              window.console.warn(e)
            }
          })
          .on('pusher:subscription_succeeded', e => {
            window.console.log(`Subscription to chatroom ${room.id} was successful`)
          })
        
      })
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
      // edit room name and memberships
      let members = []
      room.users.map(el => members.push(el.id))
      this.$store.commit('setNewRoomMembers', members)
      this.$store.commit('setDialog', {what: 'updateRoom', option: room.id})
    }
  }

}
</script>
