<template>
  <span class="room-member-names">

    <span v-for="(member, index) in room.users"
        v-if="member.id !== user.id"
        :key="index"
        :title="member.id === room.owner_id ? `${member.name} (Chat Owner)` : member.name"
      >
      <span>

        <img v-if="member.avatar"
          class="user-avatar rounded-circle"
          width="32px"
          :src="member.avatar">

        <span v-if="!member.avatar"
          :class="{
            'bg-primary': member.id === room.owner_id,
            'bg-warning': member.typing,
            'bg-secondary' : member.id !== room.owner_id && !member.typing
          }"
          class="p-1 avatar-room-helper text-white rounded-circle mr-1"
          >{{ member.username.substr(0,1).toUpperCase() }}
        </span><span
            v-if="member.typing" class="typing">
          <span>&bull;</span><span>&bull;</span><span>&bull;</span>
        </span>

      </span>

    </span>

  </span>  
</template>


<style>
  .avatar-room-helper {
    width: 32px;
    height: 32px;
    display: inline-block;
    text-align: center;
  }
</style>


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
