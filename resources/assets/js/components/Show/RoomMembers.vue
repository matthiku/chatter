<template>
  <span class="room-member-names">

    <span v-for="(member, index) in room.users"
        v-if="member.id !== user.id"
        :key="index"
        :title="member.id === room.owner_id ? 'Chat Owner' : member.name"
        :class="[member.id === room.owner_id ? 'font-weight-bold' : 'font-weight-light']"
        class="mr-1"
      >
      <span
          class="badge badge-pill"
          :class="[member.typing ? 'badge-primary' : 'badge-secondary']"
        >
        {{ member.username }}
      </span>

    </span>

  </span>  
</template>


<script>
export default {
  props: ['room', 'user'],

  methods: {
    isTyping (member) {
      // check if user has a typing date and if it's less than 9 seconds old
      let userTyping = this.usersTyping.find(u => u.id === member.id)
      if (!this.userTyping) return false
      return true
      if ( Math.floor((new Date() - this.userTyping.typing)) < 9000 ) return true
      return false
    }
  }
}
</script>
