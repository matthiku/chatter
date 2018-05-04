<template>
  <div class="chat-log">


    <chat-show-message
        v-for="(message, index) in room.messages"
        :key="index"
        :index="index"
        :message="message"
        :messages="room.messages"
        :members="room.users"
      ></chat-show-message>


    <div
        v-if="!room.messages || (room.messages && !room.messages.length)"
        class="empty" 
      >
      Chat room empty. Send a message!
    </div>


    <chat-edit-message 
        :room="room"
        @close-all-chats="closeAllChats"
      ></chat-edit-message>


  </div>  
</template>


<style>
.empty {
  padding: 1rem;
  text-align: center;
}
</style>


<script>
export default {

  props: ['room'],

  methods: {
    closeAllChats () {
      this.$emit('close-all-chats')
    }    
  }
}
</script>

