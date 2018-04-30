<template>
  <div class="chat-log">

    <chat-message
        v-for="(message, index) in room.messages"
        :key="index"
        :message="message"
        :members="room.users"
      ></chat-message>

    <div class="empty" v-if="!room.messages || (room.messages && !room.messages.length)">
      Chat room empty. Send a message!
    </div>

    <chat-composer :room="room"></chat-composer>

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

  props: ['room']

}
</script>

