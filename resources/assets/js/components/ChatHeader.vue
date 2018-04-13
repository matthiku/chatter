<template>
  <div class="card-header">

    Chatroom

    <!-- <span class="badge badge-primary float-right">{{ usersInRoom.length }}</span> -->

    <span v-for="(user, index) in usersInRoom"
      :key="index"
      class="badge badge-pill badge-info float-right">{{ user.name }}</span>

  </div>
</template>


<script>
export default {
  props: ['usersInRoom']
}
</script>


<style>

</style>
