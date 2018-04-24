<template>
  <div class="chat-composer">

    <div class="input-group input-group-sm rounded">
      <input
          type="text"
          :id="'message-room-id-' + room.id"
          class="rounded-left message-input-field"
          tabindex="room.id"
          placeholder="write your message"
          @keyup.enter="sendMessage"
          v-model="messageText"
        >

      <div class="input-group-append">
        <button class="btn btn-secondary" type="button"
            title="send photo or documents"
          ><i class="material-icons">attach_file</i></button>

        <button class="btn btn-secondary" type="button"
            title="record a message"
          ><i class="material-icons">mic</i></button>

        <button class="btn btn-primary rounded-right" type="button" @click="sendMessage">Send</button>
      </div>
    </div>

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

  methods: {
    sendMessage () {
      // do nothing if message text is empty
      if (!this.messageText) return

      this.$store.dispatch('sendMessage', {
        message: this.messageText,
        room_id: this.room.id
      })
      this.messageText = ''
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
