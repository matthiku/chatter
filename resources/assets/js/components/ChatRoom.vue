<template>
  <span>              
    <!-- chatRoom header
      -->
    <div class="card-header px-1 pt-1 pb-1 p-sm-1 p-md-2 my-0" :id="'heading-'+room.id">
      <div class="d-flex justify-content-between mb-0 w-100 p-0 cursor-pointer"
          :class="[room.id === activeRoom ? '' : 'chatroom-header']"
          @click="hideOtherRooms(room.id)"
          aria-expanded="true"
          :aria-controls="'#collapse-'+room.id"
          data-toggle="collapse"
          :data-target="'#collapse-'+room.id"
        >
          <!-- show room name -->
          <span class="room-props-and-name">
            <i  title="room settings dialog"
                v-if="room.id !== 0 && room.id !== activeRoom"
                @click.stop="editRoom(room)"
                class="material-icons">settings</i>
            
            <span v-if="room.name" class="room-name ml-1">{{ roomName }}</span>
            
          </span>

          <!-- show room members inline on wider screens -->
          <ShowRoomMembers class="d-none d-md-inline"
              :room="room" :user="user"
            ></ShowRoomMembers>

        <!-- show messages counter -->
        <span class="nowrap overflow-hidden">
          <small class="mr-1 mr-sm-2">{{ $moment(room.updated_at).fromNow() }}</small>
          <span v-if="unreadMessages + arrivedMessages"
              class="badge badge-danger badge-pill">{{ unreadMessages }}</span>
          <span class="badge badge-secondary badge-pill mr-1">{{ room.messages ? room.messages.length : 0 }}</span>
        </span>
      </div>

      <!-- show room members on extra line on smaller screens -->
      <ShowRoomMembers class="d-md-none d-block d-flex flex-nowrap justify-content-center overflow-hidden"
          :room="room" :user="user"
        ></ShowRoomMembers>

    </div>


    <!-- show the actual chat content (the messages) 
      -->
    <div :id="'collapse-'+room.id" 
        :aria-labelledby="'heading-'+room.id"
        class="collapse"
        :class="[rooms.length===1 || activeRoom === room.id ? 'show' : '']"
        data-parent="#chatrooms">

      <div 
          @keyup.esc="closeAllChats"
          tabindex="-1"
          class="card-body chat-room-body p-0 p-sm-1 p-md-2 p-lg-3 p-xl-4">

        <ChatLog 
            v-if="room.id !== 0"
            @close-all-chats="closeAllChats"
            :room="room">
        </ChatLog>

        <div v-else
            class="text-center"
          ><button class="btn btn-sm btn-outline-primary" @click="delayedCleanUp">OK</button>
        </div>

      </div>
    </div>
  </span>  
</template>


<style>
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
.room-props-and-name {
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;  
}
.room-name {
  font-family: 'Times New Roman', Times, serif;
  font-size: larger;
  color:darkred;
}
.nowrap {
  white-space: nowrap;
}
</style>


<script>
export default {
  props: ['room', 'activeRoom'],

  computed: {
    user () {
      return this.$store.state.user.user
    },
    roomName () {
      if (this.room.users.length !== 2 && this.room.name)
        return this.room.name
      if (this.room.users.length === 2 || !this.room.name)
        return this.room.users.find(el => el.id !== this.user.id).username
      return '(unnamed)'
    },
    rooms () {
      return this.$store.state.chat.rooms
    },
    unreadMessages () {
      const usersReadingProgress = this.room.users.find(usr => usr.id === this.user.id)
      if (!usersReadingProgress) return 0
      let lastReadMessageIdx = this.room.messages.findIndex(
        msg => msg.updated_at > usersReadingProgress.pivot.updated_at
      )
      if (lastReadMessageIdx < 0) return 0
      // console.log(usersReadingProgress.pivot.updated_at, lastReadMessageIdx)
      return this.room.messages.length - lastReadMessageIdx
    },
    arrivedMessages () {
      let num = 0
      this.newMessagesArrived.forEach(el => {
          if (el.room_id === this.room.id) num += 1
      })
      return num      
    },
    newMessagesArrived () {
      return this.$store.state.chat.newMessagesArrived
    }
  },

  mounted () {
    // check if user's last typing dates are outdated
    setTimeout(() => {
      this.checkTypingState()
    }, 9000);
  },

  methods: {
    checkTypingState () {
      this.room.users.forEach(usr => {
        if (usr.typing) {
          let diff = Math.floor((new Date() - usr.typing))
          if ( diff > 9000 ) usr.typing = false
        }
      });      
      setTimeout(() => {
        this.checkTypingState()
      }, 9000);
    },

    hideOtherRooms (roomId) {
      let elem = document.getElementById('collapse-'+roomId)
      // are we closing or opening this room?
      if (elem.classList.contains('show')) {
        this.$emit('set-active-room', null)
      } else {
        this.$emit('set-active-room', roomId)
        if (roomId === 0) return

        this.$store.commit('cleanUpRooms')        
        this.$emit('user-read-all-messages', roomId)
        // make sure the input field is visible (scroll to bottom of the messages)
        setTimeout(() => {          
          elem = document.getElementById('message-room-id-' + roomId)
          elem.focus()
          elem.scrollIntoView({behavior: 'smooth'})
        }, 800);
      }
    },

    editRoom (room) {
      // edit properties of or settings for this room
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

    delayedCleanUp () {
      let elem = document.getElementById('collapse-0')
      elem.classList.remove('show')
      // make sure 'leftover' rooms are removed; ie. rooms
      //    of which the current user is no longer a member
      this.$store.commit('cleanUpRooms')
    },

    closeAllChats () {
      this.$emit('close-all-chats')
    }
    
  }

}
</script>
