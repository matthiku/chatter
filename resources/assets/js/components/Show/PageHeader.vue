.<template>
  <div class="card-header all-rooms-header d-flex justify-content-between p-0 p-sm-1 p-md-2">
      
      <!-- show page title and main menu -->
      <span>
        <div class="dropdown d-inline">
          <a class="chatter-menu btn btn-secondary btn-sm dropdown-toggle" 
              href="#" role="button" 
              id="dropdownMenuLink" 
              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img v-if="user.avatar"
                class="user-avatar rounded-circle"
                :src="user.avatar">
            <i v-else class="material-icons">more_vert</i>
          </a>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <a class="dropdown-item" href="#" @click="showSettings">Settings</a>
            <a class="dropdown-item" href="#">Another action</a>
            <a class="dropdown-item" href="#" @click="logoff">Logoff</a>
          </div>
        </div>

        <span @click="closeAllChats"
            :class="[activeRoom ? 'cursor-pointer' : '']">
          <!-- <span class="border border-light rounded-circle text-center px-2 py-1">{{ rooms.length }}</span> -->
          <span class="font-weight-bold">{{ rooms.length }}</span>
          <span class="d-none d-xl-inline">Chat Rooms</span>
          <span class="d-xl-none">{{ appName }}</span>
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
          class="btn btn-sm btn-success"
        ><i class="material-icons">add</i>
        <span class="d-none d-md-inline">new chat</span>  
      </button>

      <!-- modal for user settings -->
      <user-settings></user-settings>

  </div>
</template>


<style>
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

  methods: {
    showSettings () {
      $('#userSettings').modal('toggle')
    },

    launchNewRoomModal () {
      this.$store.commit('setNewRoomMembers', [])
      this.$store.commit('setDialog', {what: 'createNewRoom', option: ''})
    },

    closeAllChats () {
      this.$emit('close-all-chats')
    },

    openNewMessage () {
      this.$emit('open-new-message')
    },

    logoff () {
      document.getElementById('logout-form').submit()
    },
    
  }
}
</script>
