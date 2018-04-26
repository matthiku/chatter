<template>
  <div class="modal fade" id="chatRoomProperties" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">


        <div class="modal-header">
          <h5 class="modal-title">{{ title }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>


        <div v-if="!deletingRoom" class="modal-body">

          <p>Room name (optional)</p>
          <div class="input-group mb-3">
            <input type="text"
                v-model="roomName"
                @keyup.enter="executeAction"
                class="form-control"
                placeholder="enter room name"
                aria-label="Room name">
          </div>

          <!-- <p>Add new members</p> 
          <div class="input-group mb-3">
            <input type="text"
                v-model="nameHint"
                class="form-control"
                placeholder="hint member's names" 
                aria-label="Member's username">
          </div>
          -->

          <div class="form-group">
            <!-- <label for="selectMembers">Select Members</label> -->
            <select v-model="members"
                multiple class="form-control" id="selectMembers">
              <option v-for="(usr, idx) in users" :key="idx"
                  v-if="user.id !== usr.id"
                  :value="usr.id">{{ usr.username }}&nbsp;({{ usr.name }})</option>
            </select>
          </div>

        </div>

        <div v-else class="modal-body">
          Do you really want to delete this chat room?
          <br>
          All messages will be deleted and this cannot be reversed.
        </div>


        <div class="modal-footer">

          <button v-if="deletingRoom"
              @click="deleteRoom" type="button" class="btn btn-danger" >Delete Room</button>

          <button v-if="!deletingRoom && buttonText==='Save'"
              @click="deletingRoom = true"
              type="button" class="btn btn-sm btn-alert float-left">Delete Room</button>

          <button 
              @click="closeDialog" type="button" 
              class="btn btn-secondary" data-dismiss="modal">Close</button>

          <button v-if="!deletingRoom"
              @click="executeAction" type="button"
              class="btn btn-primary">{{ buttonText }}</button>
        </div>
      </div>
    </div>


  </div>
</template>


<script>
export default {

  data () {
    return {
      title: 'undecided...',
      buttonText: 'undecided...',
      roomName: null,
      members: [],
      nameHint: null,
      deletingRoom: false
    }
  },

  computed: {
    user () {
      return this.$store.state.user.user
    },
    users () {
      return this.$store.state.user.users
    },
    rooms () {
      return this.$store.state.chat.rooms
    },
    newRoomMembers () {
      return this.$store.state.chat.newRoomMembers
    },
    dialog () {
      return this.$store.state.shared.dialog
    }
  },

  watch: {
    newRoomMembers (val) {
      this.members = val
    },
    dialog (val) {
      if (val.what === 'createNewRoom') {
        this.title = 'Create new Chat Room'
        this.roomName = this.user.username
        this.buttonText = 'Start Chat'
        $('#chatRoomProperties').modal('show')
      }
      if (val.what === 'updateRoom') {
        this.title = 'Edit Chat Room'
        this.buttonText = 'Save'
        this.roomName = this.dialog.roomName
        $('#chatRoomProperties').modal('show')
      }
      if (val === '') {
        $('#chatRoomProperties').modal('hide')
      }
    }
  },

  methods: {
    closeDialog () {
      this.$store.commit('setDialog', '')
      this.deletingRoom = false
    },

    executeAction () {
      // at least one (other) member is needed (besides the current user)
      if (!this.members.length) return

      // close the modal, then create a new chat room
      let obj = {}
      obj.members = this.members
      if (this.roomName) obj.name = this.roomName
      if (this.dialog.what === 'createNewRoom')
        this.$store.dispatch('createNewRoom', obj)
      if (this.dialog.what === 'updateRoom') {
        obj.id = this.dialog.option
        this.$store.dispatch('updateRoomProperties', obj)
      }
      this.$store.commit('setDialog', '')
      this.deletingRoom = false
    },

    deleteRoom () {
      let room = this.rooms.find(el => el.id === this.dialog.option)
      if (this.user.id !== room.owner_id) return
      window.console.log('deleting', room)
      this.$store.dispatch('deleteRoom', {'room_id': room.id})
      this.$store.commit('setDialog', '')
      this.deletingRoom = false
    }
  }
}
</script>
