<template>
  <div class="chat-message">
    <p>{{ message.message }}
      <br>
      <small v-if="usersObj[message.user_id]">{{ usersObj[message.user_id].username }}</small>
    </p>
  </div>  
</template>


<script>
export default {
  props: ['message', 'members'],

  computed: {
    users () {
      return this.$store.state.user.users
    },
    usersObj () {
      let obj = {}
      this.users.map(elem => obj[elem.id] = elem)
      return obj
    }
  }
}
</script>


<style>

</style>
