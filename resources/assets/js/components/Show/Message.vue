<template>
  <span>
    <small v-for="member in members" :key="member.id"
        v-if="readingProgressBefore(member)"
        :title="member.pivot.updated_at"
      >
      {{ member.username }},
    </small>

    <div class="mb-2"
        :class="[message.user_id === user.id ? 'text-right' : '']"
      >

      <span class="border border-primary rounded shadow text-white mb-0 p-1"
        :class="[deleted ? 'bg-dark' : message.user_id === user.id ? 'bg-info' : 'bg-secondary']">

        <!-- show the actual message -->
        <span v-if="!deleting && !deleted" v-html="showLinks(message.message)"></span>

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
          <span class="text-primary">{{ message.updated_at }}</span>
      </small>

    </div>

    <small v-for="member in members" :key="member.id"
        v-if="readingProgressAfter(member)"
        :title="member.pivot.updated_at"
      >
      {{ member.username }},
    </small>
  </span>
</template>


<script>
export default {
  props: ['message', 'messages', 'index', 'members'],

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
    showLinks (text) {
      let urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig
      return text.replace(urlRegex, function(url) {
          return '<a href="' + url + '">' + url + '</a>';
      });
    },

    readingProgressBefore (member) {
      if (member.id === this.user.id) return false // don't show the current user
      // check if this member's reading progress is before this and after the previous message

      let userProgress = this.$moment(member.pivot.updated_at)
      let messageDate = this.$moment(this.message.updated_at)
      // if index is 0, we are at the very first message in this room
      if (this.index === 0) {
        if (userProgress.isBefore(messageDate))
          return true
        else
          return false
      }
      let prevMessageDate = this.$moment(this.messages[this.index-1].updated_at)
      if (userProgress.isBefore(messageDate) && userProgress.isAfter(prevMessageDate))
        return true
      return false
    },
    
    readingProgressAfter (member) {
      if (member.id === this.user.id) return false // don't show the current user
      // check if this member's reading progress is after the current (newest) message
      if (this.index + 1 < this.messages.length) return false // only on the last message

      let userProgress = this.$moment(member.pivot.updated_at)
      let messageDate = this.$moment(this.message.updated_at)
      if (userProgress.isSameOrAfter(messageDate)) 
        return true
      return false
    },

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
