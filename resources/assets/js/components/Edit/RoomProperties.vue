/**
 * VUE.JS Component to create, edit or configure chat rooms
 * 
 * 
 * Depending on the state of 'shared.dialog', this component will be used to
 *
 * - create a new chatroom
 *
 * - edit the name and/or members of a chatroom or delete the whole room
 *
 * - for members: change the email notification settings or leave the chat
 * 
 * 
 * (C) 2018 Matthias Kuhs, Ireland + Germany
 */
<template>
  <div class="modal fade" 
      id="chatRoomProperties"
      tabindex="-1"
      role="dialog"
    >
    <div class="modal-dialog" role="document">
      <div class="modal-content p-0 p-md-2">


        <div class="modal-header bg-info text-white p-1 p-md-2">
          <h5 class="modal-title">{{ title }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>


        <div v-if="!deletingRoom" class="modal-body p-1 p-md-2">

          <!-- edit room name (only owner) -->
          <div v-if="userIsOwner" class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" id="basic-addon1">Name</span>
            </div>
            <input type="text"
                v-model="roomName"
                id="editRoomName"
                @keyup.enter="executeAction"
                class="form-control"
                placeholder="enter room name"
                aria-label="Room name">
          </div>

          <!-- email notification setting (all) -->
          <div class="input-group mb-3">
            <div class="custom-control custom-checkbox">
              <input type="checkbox"
                  v-model="emailNotification"
                  class="custom-control-input"
                  id="emailNotification">
              <label class="custom-control-label" for="emailNotification">Receive email notification on new messages?</label>
            </div>
          </div>


          <!-- Edit member list (only room owner) -->
          <div v-if="userIsOwner" class="form-group">
            <label for="selectMembers">Select Members:</label>
            
            <select v-model="members"
                multiple class="form-control" id="selectMembers">
              <option v-for="(usr, idx) in users" :key="idx"
                  v-if="user.id !== usr.id"
                  :value="usr.id">{{ usr.username }}&nbsp;({{ usr.name }})</option>
            </select>
          </div>

          <!-- Show member list -->
          <div v-else-if="room">
            <label for="membersList">Members:</label>
            <ShowRoomMembers
                :room="room"
                :user="user"
              ></ShowRoomMembers>
          </div>

        </div>



        <div v-if="deletingRoom" class="modal-body p-1 p-md-2">
          <strong>Do you really want to {{ userIsOwner ? 'delete' : 'leave'}} this chat room?</strong>
          <br>
          <br>
          <span v-if="userIsOwner">
            All messages will be deleted and this cannot be reversed.
          </span>
        </div>


        <div class="modal-footer p-1 p-md-2">

          <button v-if="deletingRoom"
              @click="deleteOrLeaveRoom" type="button" class="btn btn-danger" >{{ userIsOwner ? 'Delete' : 'Leave'}} Room</button>

          <button v-if="!deletingRoom && buttonText==='Save'"
              @click="deletingRoom = true"
              type="button" class="btn btn-sm btn-alert float-left">{{ userIsOwner ? 'Delete' : 'Leave'}} Room</button>

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
      room: null,
      title: 'undecided...',
      buttonText: 'undecided...',
      roomName: null,
      userIsOwner: false,
      emailNotification: true,
      members: [],
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

  mounted () {
    $('#chatRoomProperties').on('shown.bs.modal', function () {
      $('#editRoomName').trigger('focus')
    })
  },

  watch: {
    newRoomMembers (val) {
      this.members = val
    },
    dialog (val) {
      if (val.what === 'createNewRoom') {
        this.title = 'New Conversation'
        this.roomName = this.user.username
        this.buttonText = 'Start Chat'
        this.userIsOwner = true
        // if user clicked on a single, currently online, user
        if (val.option === 'single') {
          // directly create the new chat, no dialog needed
          this.executeAction()
        } else {
          $('#chatRoomProperties').modal('show')
        }        
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

      // set the current room accordingly
      if (val.option) {
        this.room = this.rooms.find(el => el.id === val.option)
        if (this.room) {
          this.emailNotification = this.room.pivot ? this.room.pivot.email_notification : true
          if (this.room.owner_id === this.user.id)
            this.userIsOwner = true
          else
            this.title = `Settings for chat room "${this.roomName}"`
        }
      }
    }
  },

  methods: {
    closeDialog () {
      this.$store.commit('setDialog', '')
      this.$store.commit('setNewRoomMembers', [])
      this.deletingRoom = false
      this.userIsOwner = false
      this.room = null
    },

    executeAction () {

      // Submit the email notification setting
      if (this.emailNotification !== undefined && this.room && !this.userIsOwner) {
        let obj = {
          room_id: this.room.id,
          emailNotification: this.emailNotification
        }
        this.$store.dispatch('setEmailNotification', obj)

        // if user is not room owner this is the only possible action
        this.closeDialog()
      }

      // at least one (other) member is needed (besides the current user)
      if (!this.members.length) return

      // close the modal, then create a new chat room
      let obj = {}
      obj.members = this.members
      obj.email_notification = this.emailNotification
      if (this.roomName) obj.name = this.roomName
      if (this.dialog.what === 'createNewRoom')
        this.$store.dispatch('createNewRoom', obj)
      if (this.dialog.what === 'updateRoom') {
        obj.id = this.dialog.option
        this.$store.dispatch('updateRoomProperties', obj)
      }
      this.closeDialog()
    },

    leaveRoom () {
      // allow a member to leave this room
      this.$store.dispatch('sendMessage', {
        message: `user ${this.user.username} has left this chatroom`,
        room_id: this.room.id
      })
      this.$store.dispatch('leaveRoom', this.room)
      this.closeDialog()
    },

    deleteOrLeaveRoom () {
      if (!this.userIsOwner) {
        this.leaveRoom()
        return
      }
      this.$store.dispatch('deleteRoom', {'room_id': this.room.id})
      this.closeDialog()
    }
  }
}
</script>
