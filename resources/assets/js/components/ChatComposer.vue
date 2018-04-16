<template>
  <div class="chat-composer">

    <input
        type="text"
        id="message"
        placeholder="start typing"
        @keyup.enter="sendMessage"
        v-model="messageText"
      >

    <button 
        class="btn btn-primary"
        @click="sendMessage"
      >Send</button>

  </div>  
</template>


<script>
export default {
  props: ['user'],
  
  data () {
    return {
      messageText: ''
    }
  },

  methods: {
    sendMessage () {
      // do nothing if message text is empty
      if (!this.messageText) return

      this.$emit('messagesent', {
        message: this.messageText,
        user: this.user
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
