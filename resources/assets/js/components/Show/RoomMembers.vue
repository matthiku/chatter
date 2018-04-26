<template>
  <span class="room-member-names">

    <small v-for="(member, index) in room.users"
        v-if="member.id !== user.id"
        :key="index"
        :title="member.id === room.owner_id ? 'Chat Owner' : member.name"
        :class="[member.id === room.owner_id ? 'font-weight-bold' : 'font-weight-light']"
      >{{ member.username 
        }}<span v-if="index < room.users.length-1" class="mr-2">,</span>
    </small>

  </span>  
</template>


<script>
export default {
  props: ['room', 'user']
}
</script>

<style>

</style>
