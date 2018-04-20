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

    <div v-if="user.id === room.owner_id"
        class="float-right mt-3">
      <button @click="deleteRoom"
          title="delete this whole chat with all messages"
          type="button" class="btn btn-sm btn-outline-danger">Delete Chat</button>
    </div>

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
  },

  methods: {
    deleteRoom () {
      if (this.user.id !== this.room.owner_id) return
      window.console.log('deleting', this.room)
      this.$store.dispatch('deleteRoom', {'room_id': this.room.id})
    }
  }
}
</script>

