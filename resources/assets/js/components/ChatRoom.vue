<template>
  <span>              
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
  </span>  
</template>

<script>
export default {
  props: ['room', 'newMessagesArrived', 'activeRoom'],

  computed: {
    user () {
      return this.$store.state.user.user
    },
    rooms () {
      return this.$store.state.chat.rooms
    },
  },

  methods: {

    hideOtherRooms (roomId) {
      let elem = document.getElementById('collapse-'+roomId)
      if (elem.classList.contains('show')) {
        this.$emit('set-active-room', null)
      } else {
        this.$emit('set-active-room', roomId)
        if (roomId !== 0)
          this.$store.commit('cleanUpRooms')        
        this.$emit('user-read-all-messages', roomId)
      }
    },

    editRoom (room) {
      // edit properties of this room, but
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
      // allow a member to leave this room
      this.$store.dispatch('sendMessage', {
        message: `user ${this.user.username} has left this chatroom`,
        room_id: room.id
      })
      this.$store.dispatch('leaveRoom', room)
    },

    messagesForThisRoom(roomId) {
      let num = 0
      this.newMessagesArrived.map(el => {
        if (el.room_id === roomId) num += 1
      })
      return num
    },

    delayedCleanUp () {
      let elem = document.getElementById('collapse-0')
      elem.classList.remove('show')
      // make sure 'leftover' rooms are removed; ie. rooms
      //    of which the current user is no longer a member
      this.$store.commit('cleanUpRooms')
    },
    
  }

}
</script>

<style>

</style>