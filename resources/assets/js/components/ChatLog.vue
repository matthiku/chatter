<template>
  <div class="chat-log">


    <ShowMessage
        v-for="(message, index) in room.messages"
        :key="index"
        :index="index"
        :message="message"
        :room="room"
      ></ShowMessage>


    <div
        v-if="!room.messages || (room.messages && !room.messages.length)"
        class="empty" 
      >
      Chat room empty. Send a message!
    </div>


    <EditComposeMessage 
        :room="room"
        @close-all-chats="closeAllChats"
      ></EditComposeMessage>


    <ShowSlideshow
        :messages="room.messages"
      ></ShowSlideshow>

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

