<template>
  <div class="chat-log">

    <chat-message
        v-for="(message, index) in room.messages"
        :key="index"
        :message="message"
        :members="room.users"
      ></chat-message>

    <div class="empty" v-if="room.messages && !room.messages.length">
      Nothing here yet! Send a message!
    </div>

    <chat-composer :room="room"></chat-composer>

  </div>  
</template>


<style>
.chat-log .chat-message:nth-child(even) {
  background-color: #ccc;
}
.empty {
  padding: 1rem;
  text-align: center;
}
</style>


<script>
export default {

  props: ['room'],

  computed: {
    user () {
      return this.$store.state.user.user
    },
  }
}
</script>

