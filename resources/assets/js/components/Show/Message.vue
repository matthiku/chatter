<template>
  <span>

    <!-- show names of members depending on their reading progress -->
    <ShowReadingProgress
        simple="true"
        :index="index"
        :room="room"
        :message="message"
      ></ShowReadingProgress>

    <div :class="{
          'text-right' : message.user_id === user.id,
          'mb-2' : showUsername || showMessageDate
        }"
      >

      <!-- show the actual message -->
      <span class="show-message border border-light rounded shadow mb-0 p-1"
        :class="[deleted ? 'text-white bg-dark' : message.user_id === user.id ? 'bg-grey' : 'bg-white']">

        <span v-if="!deleting">

          <span v-if="message.filename">

            <img v-if="message.filetype === 'image'"
                :src="'/images/'+message.filename"
                class="cursor-pointer image-preview"
                @click="showSlideshow"
                :title="message.message"
                :alt="message.message">

            <audio v-if="message.filetype === 'audio'"
              :src="'/images/'+message.filename"
              controls></audio>

            <video v-if="message.filetype === 'video'"
              :src="'/images/'+message.filename"
              width="250" controls></video>

            <span v-if="notSupportedFileType()">
              <a :href="'/images/'+message.filename">{{ message.filename }}</a>
            </span>

          </span>

          <span v-else-if="!deleted" v-html="showLinks(message.message)"></span>

          <i v-if="message.user_id === user.id"
              @click="deleting = true"
              title="delete this message"
              class="delete-message cursor-pointer text-danger material-icons">delete</i>
        </span>

        <small v-if="deleted">(The user deleted this message {{ message.message }})</small>

        <span v-if="deleting">
          Are you sure to delete this message?
          <span class="badge badge-danger cursor-pointer" @click="deleteMessage">Yes</span>
          <span class="badge badge-secondary cursor-pointer" @click="deleting = false">Cancel</span>
        </span>

      </span>

      <!-- show message date and time -->
      <small v-if="showUsername || showMessageDate"
          class="mx-3"
          :title="$moment(message.updated_at).format('LLLL')"
        >
        <br>
        <strong>
          <span v-if="usersObj[message.user_id] && showUsername"
            >{{ usersObj[message.user_id].username }} -
          </span>

          <span v-if="showMessageDate"
              class="text-primary"
            >{{ adaptiveDate(message.updated_at)  }}
          </span>          
        </strong>

      </small>

    </div>

    <!-- show names of members depending on their reading progress -->
    <ShowReadingProgress
        :index="index"
        :room="room"
        :message="message"
      ></ShowReadingProgress>

  </span>
</template>


<style>
.image-preview {
  width: 200px;
}
@media (min-width: 768px) {
  .image-preview {
    width: 250px;
  }  
}
@media (min-width: 1200px) {
  .image-preview {
    width: 350px;
  }  
}
.bg-grey {
  background-color: lightgrey;
}
.delete-message {
  display: none;
}
.show-message:hover .delete-message {
  display: inline;
}
</style>



<script>
export default {
  props: ['message', 'room', 'index'],

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
    nextMessage () {
      if (this.index === this.room.messages.length) return null
      return this.room.messages[this.index+1]
    },
    messages () {
      return this.room.messages
    },
    members () {
      return this.room.users
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
    },
    showMessageDate () {
      if (!this.nextMessage) return true
      if (this.message.user_id !== this.nextMessage.user_id) return true
      return this.$moment( this.message.updated_at ).add( 2, 'minutes' ).isBefore( this.$moment(this.nextMessage.updated_at) )
    },
    showUsername () {
      if (!this.nextMessage) return true
      return this.message.user_id !== this.nextMessage.user_id
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

    notSupportedFileType() {
      let supported = ['image', 'audio', 'video']
      if ( supported.indexOf(this.message.filetype) >= 0 ) return false
      return true
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
    
    deleteMessage () {
      this.message.message = 'deleting...'
      this.deleting = false
      this.$store.dispatch('deleteMessage', this.message.id)
    },

    showSlideshow () {
      this.$store.commit('setDialog', {messageId: this.message.id})
      $('#imagesSlideShow').modal('show')
    }
  }
}
</script>
