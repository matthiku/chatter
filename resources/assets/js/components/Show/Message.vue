<template>
  <span>

    <!-- show names of members depending on their reading progress -->
    <small v-for="member in members" :key="member.id"
        v-if="readingProgressBefore(member)"
        :title="member.pivot.updated_at"
      >
      {{ member.username }},
    </small>


    <div class="mb-2"
        :class="[message.user_id === user.id ? 'text-right' : '']"
      >

      <!-- show the actual message -->
      <span class="border border-light rounded shadow mb-0 p-1"
        :class="[deleted ? 'text-white bg-dark' : message.user_id === user.id ? 'bg-grey' : 'bg-white']">

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
          <span class="text-primary">{{ adaptiveDate(message.updated_at)  }}</span>
      </small>

    </div>

    <!-- show names of members depending on their reading progress -->
    <small v-for="member in members" :key="member.id"
        v-if="readingProgressAfter(member)"
        :title="member.pivot.updated_at"
      >
      {{ member.username }}
      <span v-if="member.typing" class="typing"><span>@</span><span>@</span><span>@</span></span>,
    </small>
  </span>
</template>


<style>
.bg-grey {
  background-color: lightgrey;
}

/* from https://codepen.io/xwildeyes/pen/KpqVzN */
@keyframes blink {
    /**
     * At the start of the animation the dot
     * has an opacity of .2
     */
    0% {
      opacity: .2;
    }
    /**
     * At 20% the dot is fully visible and
     * then fades out slowly
     */
    20% {
      opacity: 1;
    }
    /**
     * Until it reaches an opacity of .2 and
     * the animation can start again
     */
    100% {
      opacity: .2;
    }
}
.typing span {
    /**
     * Use the blink animation, which is defined above
     */
    animation-name: blink;
    /**
     * The animation should take 1.4 seconds
     */
    animation-duration: 1.4s;
    /**
     * It will repeat itself forever
     */
    animation-iteration-count: infinite;
    /**
     * This makes sure that the starting style (opacity: .2)
     * of the animation is applied before the animation starts.
     * Otherwise we would see a short flash or would have
     * to set the default styling of the dots to the same
     * as the animation. Same applies for the ending styles.
     */
    animation-fill-mode: both;
}
.typing span:nth-child(2) {
    /**
     * Starts the animation of the third dot
     * with a delay of .2s, otherwise all dots
     * would animate at the same time
     */
    animation-delay: .2s;
}
.typing span:nth-child(3) {
    /**
     * Starts the animation of the third dot
     * with a delay of .4s, otherwise all dots
     * would animate at the same time
     */
    animation-delay: .4s;
}
</style>



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

    adaptiveDate (val) {
      if (!val) return ''
      let dt = this.$moment(val)
      let now = this.$moment()
      if (dt.isSame(now, 'day')) return dt.format('H:mm')
      if (dt.isSame(now, 'week')) return dt.format('ddd H:mm')
      if (dt.isSame(now, 'year')) return dt.format('D MMM H:mm')
      return dt.format('D MM YYYY')
    },

    showLinks (text) {
      let urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig
      return text.replace(urlRegex, function(url) {
          return '<a target="new" href="' + url + '">' + url + '</a>';
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
