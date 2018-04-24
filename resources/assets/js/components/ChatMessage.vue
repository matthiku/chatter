<template>
  <div class="mb-2"
      :class="[message.user_id === user.id ? 'text-right' : '']"
    >

    <span class="border border-primary rounded shadow text-white mb-0 p-1"
      :class="[message.user_id === user.id ? 'bg-info' : 'bg-secondary']">
      {{ message.message }}
    </span>
    <br>

    <small v-if="usersObj[message.user_id]" 
        class="mx-3"
      >{{ usersObj[message.user_id].username }}-{{ message.updated_at }}
    </small>

  </div>  
</template>


<script>
export default {
  props: ['message', 'members'],

  computed: {
    users () {
      return this.$store.state.user.users
    },
    user () {
      return this.$store.state.user.user
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
