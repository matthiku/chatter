<template>
  <div class="modal fade" id="createNewRoom" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create new Chat</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Add new members</p>
          <div class="input-group mb-3">
            <input type="text"
                v-model="nameHint"
                class="form-control"
                placeholder="hint member's names" 
                aria-label="Member's username">
          </div>
          <div class="form-group">
            <label for="selectMembers">Select Members</label>
            <select v-model="members"
                multiple class="form-control" id="selectMembers">
              <option v-for="(usr, idx) in users" :key="idx"
                  v-if="user.id !== usr.id"
                  :value="usr.id">{{ usr.username }}&nbsp;({{ usr.name }})</option>
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button"
              @click="createNewRoom()"
              class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
export default {

  data () {
    return {
      roomName: null,
      members: [],
      nameHint: null
    }
  },

  computed: {
    user () {
      return this.$store.state.user.user
    },
    users () {
      return this.$store.state.user.users
    },
    newRoomMembers () {
      return this.$store.state.chat.newRoomMembers
    },
  },

  watch: {
    newRoomMembers (val) {
      this.members = val
    }
  },

  methods: {
    createNewRoom (user) {
      // at least one (other) member is needed (besides the current user)
      if (!this.members.length) return
      let obj = {}
      obj.members = this.members
      if (this.roomName) obj.name = this.roomName
      this.$store.dispatch('createNewRoom', obj)
    }
  }
}
</script>
