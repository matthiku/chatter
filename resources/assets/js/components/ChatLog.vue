<template>
  <div class="chat-log">

    <chat-message
        v-for="(message, index) in room.messages"
        :key="index"
        :message="message"
        :members="room.members"
      ></chat-message>

    <div class="empty" v-if="!room.messages.length">
      Nothing here yet! Send a message!
    </div>

    <chat-composer :room="room"></chat-composer>

  </div>  
</template>


<script>
export default {
  props: ['room'],
}
</script>


<style>
.chat-log .chat-message:nth-child(even) {
  background-color: #ccc;
}
.empty {
  padding: 1rem;
  text-align: center;
}
</style>
