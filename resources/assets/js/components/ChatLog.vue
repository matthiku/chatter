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

    <div class="empty" v-if="!room.messages || (room.messages && !room.messages.length)">
      Chat room empty. Send a message!
    </div>

    <chat-edit-message :room="room"></chat-edit-message>

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

