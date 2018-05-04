<template>
  <span class="room-member-names">

    <small v-for="(member, index) in room.users"
        v-if="member.id !== user.id"
        :key="index"
        :title="member.id === room.owner_id ? 'Chat Owner' : member.name"
        :class="[member.id === room.owner_id ? 'font-weight-bold' : 'font-weight-light']"
      >{{ member.username 
        }}<small v-if="isTyping()">typing...</small>
      <span v-if="index < room.users.length-1" class="mr-2">,</span>
    </small>

  </span>  
</template>


<script>
export default {
  props: ['room', 'user'],

  methods: {
    isTyping () {
      // check if user has a typing date and if it's less than 9 seconds old
      if (!this.user.typing) return false
      return true
      if ( Math.floor((new Date() - this.user.typing)) < 9000 ) return true
      return false
    }
  }
}
</script>
