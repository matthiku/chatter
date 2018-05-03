<template>
  <div class="chat-composer">

    <div class="input-group input-group-sm rounded">
      <input
          type="text"
          :id="'message-room-id-' + room.id"
          ref="messageInput"
          class="rounded-left message-input-field"
          tabindex="room.id"
          placeholder="write your message"
          @click="markMessagesAsRead"
          @keyup.enter="sendMessage"
          v-model="messageText"
        >

      <div class="input-group-append">
        <button class="btn btn-secondary" type="button"
            title="insert emoticon"
            @click="insertEmoticon"
          ><i class="material-icons">insert_emoticon</i></button>

        <button class="btn btn-secondary" type="button"
            title="send photo or documents (coming soon!)"
          ><i class="material-icons">attach_file</i></button>

        <button class="btn btn-secondary" type="button"
            title="record a message (coming soon!)"
          ><i class="material-icons">mic</i></button>

        <button class="btn btn-primary rounded-right" type="button" @click="sendMessage">Send</button>
      </div>
    </div>

    <chat-show-emoticons></chat-show-emoticons>

  </div>  
</template>


<script>
export default {

  props: ['room'],
  
  data () {
    return {
      messageText: ''
    }
  },

  computed: {
    action () {
      return this.$store.state.shared.action
    },
    user () {
      return this.$store.state.user.user
    },
  },

  mounted () {
    // check if this room was just added
    if (this.action && this.action.type && this.action.type === 'roomAdded' && this.room.id === this.action.what.id) {
      // if a new room was added, we want to open the new drawer element
      let elem = window.document.getElementById('collapse-' + this.room.id)
      if (elem) elem.classList.add('show')
      // and put the cursor into the input field
      this.$refs.messageInput.focus()
      // remove the action
      this.$store.commit('setAction', null)
    }
  },

  methods: {
    insertEmoticon () {
      $('#selectEmoticons').modal('toggle')
    },

    sendMessage () {
      // do nothing if message text is empty
      if (!this.messageText) return

      this.$store.dispatch('sendMessage', {
        message: this.messageText,
        room_id: this.room.id
      })
      this.messageText = ''
    },

    markMessagesAsRead () {
      let roomLastUpdate = this.room.updated_at
      let usersReadingProgress = this.room.users.find(el => el.id === this.user.id).pivot.updated_at
      // update the reading progress of this user for this room
      if (this.$moment(usersReadingProgress).isBefore(this.$moment(roomLastUpdate))) {
        this.$store.dispatch('setReadingProgress', this.room.id)
      }
      this.$store.commit('clearRoomFromNewMessagesArrived', this.room.id)
    }
  }
}
</script>


<style>
.chat-composer {
  display: flex;
}
.chat-composer input {
  flex: 1 auto;
}
.chat-composer button {
  border-radius: 0;
}
</style>
