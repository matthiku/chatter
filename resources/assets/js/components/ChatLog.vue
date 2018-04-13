<template>
  <div class="chat-log">
    <h3>chat Log</h3>

    <chat-message
        v-for="(message, index) in messages"
        :key="index"
        :message="message"
      ></chat-message>

    <div class="empty" v-if="!messages.length">
      Nothing here yet!
    </div>

  </div>  
</template>


<script>
export default {
  props: ['messages'],
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
