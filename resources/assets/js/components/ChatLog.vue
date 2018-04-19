<template>
  <div class="chat-log">

    <chat-message
        v-for="(message, index) in messages"
        :key="index"
        :message="message"
        :members="members"
      ></chat-message>

    <div class="empty" v-if="!messages.length">
      Nothing here yet! Send a message!
    </div>

  </div>  
</template>


<script>
export default {
  props: ['messages', 'members'],
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
