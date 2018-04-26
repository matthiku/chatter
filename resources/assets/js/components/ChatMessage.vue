<template>
  <div class="mb-2"
      :class="[message.user_id === user.id ? 'text-right' : '']"
    >

    <span class="border border-primary rounded shadow text-white mb-0 p-1"
      :class="[deleted ? 'bg-dark' : message.user_id === user.id ? 'bg-info' : 'bg-secondary']">

      <!-- show the actual message -->
      <span v-if="!deleting && !deleted">{{ message.message }}</span>

      <small v-if="deleted">(The user deleted this message {{ message.message }})</small>

      <span v-if="deleting">
        Are you sure to delete this message?
        <span class="badge badge-danger cursor-pointer" @click="deleteMessage">Yes</span>
        <span class="badge badge-secondary cursor-pointer" @click="deleting = false">Cancel</span>
      </span>

      <i v-if="message.user_id === user.id && !deleting"
          @click="deleting = true"
          title="delete this message"
          class="text-danger cursor-pointer material-icons">delete</i>

    </span>
    <br>

    <!-- show message date and time -->
    <small v-if="usersObj[message.user_id]" 
        class="mx-3"
        :title="$moment(message.updated_at).format('LLLL')"
      ><strong>{{ usersObj[message.user_id].username }}</strong>
        -
        <span class="text-primary">{{ $moment(message.updated_at).fromNow() }}</span>
    </small>

  </div>  
</template>


<script>
export default {
  props: ['message', 'members'],

  data () {
    return {
      deleting: false
    }
  },

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
    },
    deleted () {
      if (!this.message.message || this.message.message.length !== 19) return false
      let dt = this.message.message.split(' ')
      if (dt.length !== 2) return false
      let da = dt[0].split('-')
      if (da.length !== 3) return false
      let tm = dt[1].split(':')
      if (tm.length !== 3) return false
      return true
    }
  },

  methods: {
    deleteMessage () {
      this.message.message = 'deleting...'
      this.deleting = false
      this.$store.dispatch('deleteMessage', this.message.id)
    }
  }
}
</script>


<style>

</style>
