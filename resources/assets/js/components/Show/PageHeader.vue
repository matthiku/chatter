.<template>
  <div class="card-header all-rooms-header d-flex justify-content-between p-0 p-sm-1 p-md-2">
      
      <!-- show page title and main menu -->
      <span>

        <div class="dropdown d-inline">

          <button class="btn btn-secondary btn-sm chatter-menu user-settings-button dropdown-toggle p-0"
              type="button"
              id="dropdownMenuLink"
              data-offset="10,10"
              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img v-if="user.avatar"
                class="user-avatar rounded-circle"
                width="30px"
                :src="user.avatar">
            <i class="material-icons">more_vert</i>
          </button>

          <div class="dropdown-menu py-3 px-4" aria-labelledby="dropdownMenuLink">
            <span v-if="activeRoom">
              <a class="dropdown-item rounded border border-dark bg-light mb-3" href="#" @click="closeAllChats"><i class="material-icons">close</i> Close Room</a>
              <a class="dropdown-item rounded border border-primary mb-3" href="#" @click="editRoom"><i class="material-icons">settings</i> Room settings</a>
              <div class="dropdown-divider"></div>               
            </span>
            <a class="dropdown-item rounded bg-info mb-3" href="#" @click="showSettings"><i class="material-icons">face</i> Your Profile</a>
            <a class="dropdown-item rounded bg-warning mb-3" href="#" @click="logoff"><i class="material-icons">power_settings_new</i> Logoff</a>
          </div>

        </div>

        <span @click="closeAllChats"
            class="d-none d-sm-inline all-rooms-title"
            :class="[activeRoom ? 'cursor-pointer text-primary' : '']">
          <span v-if="activeRoom"
            class="font-weight-bold">{{ rooms.length }}</span>
          <span class="d-none d-xl-inline">Chat Room<span v-if="rooms.length>1">s</span></span>
          <span class="d-xl-none">{{ appName }}</span>
        </span>              

        <span v-if="newMessagesArrived.length"
            title="click to open new message"
            @click="openNewMessage"
            class="cursor-pointer badge badge-danger"
          >{{ newMessagesArrived.length }}</span>

      </span>

      <!-- show online users -->
      <ShowOnlineMembers
          class="all-rooms-title"
          :onlineUsers="onlineUsers" :user="user">
      </ShowOnlineMembers>

      <button @click="launchNewRoomModal()"
          title="create a new chat room"
          class="btn btn-sm btn-success"
        >
        <span class="d-none d-md-inline align-text-bottom">new chat</span>
        <span class="badge badge-light align-bottom"><i class="material-icons">add</i></span>
      </button>

  </div>
</template>


<style>
.user-settings-button {
  height: 100%;
  min-width: 35px;
  font-size: 20px;
}
@media (max-width: 576px) {
  .user-settings-button {
    min-width: 43px;
  }
}
.all-rooms-header {
  line-height: 29px;
  font-size: 25px;
  min-height: 45px;
}

/* The sticky class is added to the navbar with JS when it reaches its scroll position */
.sticky {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 100;
}
/* Add some top padding to the page content to prevent sudden quick movement 
   (as the navigation bar gets a new position at the top of the page (position:fixed and top:0) */
.sticky + .chatroom-canvas {
  padding-top: 60px;
}

.all-rooms-title {
  font-size: 15px;
}
.chatter-menu.dropdown-toggle::after {
  content: none;
}
.member-avatar {
  width: 25px;
  height: 25px;
}
</style>


<script>
export default {
  props: ['activeRoom'],

  computed: {
    appName () {
      return this.$store.state.shared.appName
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
    newMessagesArrived () {
      return this.$store.state.chat.newMessagesArrived
    }
  },

  mounted () {
    window.onscroll = () => {this.cardHeaderSticky()}
  },

  methods: {
    cardHeaderSticky () {
      let headerBar = document.getElementsByClassName('all-rooms-header')[0]
      let sticky = headerBar.offsetTop
      if (window.pageYOffset > sticky) {
        headerBar.classList.add('sticky')
      } else {
        headerBar.classList.remove('sticky')
      }
    },

    showSettings () {
      $('#userSettings').modal('toggle')
    },

    launchNewRoomModal () {
      this.$store.commit('setNewRoomMembers', [])
      this.$store.commit('setDialog', {what: 'createNewRoom', option: ''})
    },

    editRoom () {
      // get the current (active) room
      let room = this.rooms.find(el => el.id === this.activeRoom)
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

    closeAllChats () {
      this.$emit('close-all-chats')
    },

    openNewMessage () {
      this.$emit('open-new-message')
    },

    logoff () {
      this.$store.dispatch('logoff')
    },
    
  }
}
</script>
