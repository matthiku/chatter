.<template>
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
</template>


<script>
export default {
  props: ['activeRoom', 'newMessagesArrived'],

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
  },

  methods: {

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
