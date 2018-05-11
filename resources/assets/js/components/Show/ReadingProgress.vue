<template>
  <div :class="{'pt-1 pb-2' : avatarPresent}">

    <span v-for="member in members" :key="member.id"
        v-if="readingProgress(member)"
        :title="member.name"
      >

      <img v-if="member.avatar"
          class="member-avatar rounded-circle"
          :src="member.avatar"
          :alt="member.username">
      <span v-else
          class="px-2 py-1 member-avatar border border-dark rounded-circle"
          :class="[ member.typing ? 'bg-warning' : 'text-white bg-info' ]"
          >{{ member.username ? member.username.charAt(0).toUpperCase() : '' }}</span>

      <span 
          v-if="!simple && member.typing" class="mr-2 typing">
        <span>&bull;</span><span>&bull;</span><span>&bull;</span>
      </span>
    </span>

  </div>  
</template>


<script>
export default {
  props: ['message', 'room', 'index', 'simple'],

  data () {
    return {
      avatarPresent: false
    }
  },

  computed: {
    user () {
      return this.$store.state.user.user
    },
    messages () {
      return this.room.messages
    },
    members () {
      return this.room.users
    }
  },

  methods: {
    readingProgress (member) {
      if (member.id === this.user.id) return false // don't show the current user

      // check where this member's reading progress is

      if (!this.simple && this.index + 1 < this.messages.length) return false // only on the last message

      let userProgress = this.$moment(member.pivot.updated_at)
      let messageDate = this.$moment(this.message.updated_at)

      // check if reading progress is at the newest message
      if (!this.simple) {
        if (userProgress.isSameOrAfter(messageDate))  {
          this.avatarPresent = true
          return true
        }
        return false
      }

      // if index is 0, we are at the very first message in this room
      if (this.index === 0) {
        if (userProgress.isBefore(messageDate)) {
          this.avatarPresent = true
          return true
        }
        else
          return false
      }
      let prevMessageDate = this.$moment(this.messages[this.index-1].updated_at)
      if (userProgress.isBefore(messageDate) && userProgress.isAfter(prevMessageDate)) {
        this.avatarPresent = true
        return true
      }
      return false
    }
  }
}
</script>


<style>
.member-avatar {
  width: 25px;
  height: 25px;
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

