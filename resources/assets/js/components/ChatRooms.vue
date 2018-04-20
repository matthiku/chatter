<template>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">

        <div class="card-header">Your Chat Rooms
          <button data-toggle="modal" data-target="#createNewRoom"
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
                      (<small v-for="(member, index) in room.members"
                          v-if="member.id !== user.id"
                          :key="index"
                          class="font-weight-light">{{ member.username 
                          }}<span v-if="index < room.members-1">,</span>
                      </small>)
                    </span>

                    <span class="float-right">
                      <i class="material-icons">message</i>
                      <span class="badge badge-secondary badge-pill float-right">{{ room.messages.length }}</span>
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

    <create-new-room></create-new-room>

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
      // check if room is already connected: 
      //          "window.Echo.connector.channels"
      // must contain an object with name 'private-chatroom.{id}'
    }
  },

  updated () {
    this.rooms.map(room => {

      // start listening to our backend broadcast channel
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
  },

  methods: {
    launchNewRoomModal (user_id) {
      $('#createNewRoom').modal()
    }
  }

}
</script>
