<template>
  <div class="modal fade"
      id="userSettings"
      tabindex="-1"
      role="dialog"
      aria-labelledby="userSettingsLabel" aria-hidden="true"
    >
    <div class="modal-dialog" role="document">
      <div class="modal-content p-0 p-md-2">

        <div class="modal-header bg-info text-white p-1 p-md-2">
          <h5 class="modal-title" id="userSettingsLabel">User Profile</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body p-1 p-md-2">

          <form>
          
            <label for="inputUsername">Change:</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">User name</span>
              </div>
              <input 
                  type="text"
                  id="inputUsername"
                  v-model="newUserName"
                  @keyup="checkUsername"
                  @keyup.enter="changeSettings"
                  class="form-control"
                  placeholder="Username (min. 5 characters!)"
                  aria-label="Username" aria-describedby="basic-addon1">
              <div class="username-invalid invalid-feedback">
                This username has already been taken.
              </div>
            </div>

            <hr>

            <div class="form-group">

              <img v-if="user.avatar"
                class="float-right user-avatar rounded-circle"
                width="45px"
                :src="user.avatar">

              <label for="avatarFile" class="float-left">Your avatar (icon):</label>

              <input disabled type="file" class="d-inline float-right" id="avatarFile">
            </div>


          </form>

        </div>

        <div class="modal-footer p-1 p-md-2">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button"
              class="btn btn-primary"
              :disabled="!userNameValid"
              @click="changeSettings"
            >Save changes</button>
        </div>

      </div>
    </div>
  </div>
</template>


<style scoped>
  .visible {
    display: initial;
  }
  label {
    font-size: 1.1rem;
  }
</style>


<script>
export default {
  data () {
    return {
      newUserName: '',
      userNameValid: false
    }
  },

  computed: {
    user () {
      return this.$store.state.user.user
    },
    userNames () {
      return this.$store.getters.userNames
    }
  },

  methods: {
    changeSettings () {
      this.checkUsername()
      if (!this.userNameValid) return

      this.$store.dispatch(
        'updateUserData',
        {'id': this.user.id, 'username': this.newUserName}
      )
      $('#userSettings').modal('toggle')      
    },

    checkUsername () {
      let err = document.getElementsByClassName('username-invalid')[0]
      err.classList.remove('visible')
      this.userNameValid = false
      if (this.newUserName === this.user.username) return
      if (this.newUserName.length < 5) return
      if (this.userNames.indexOf(this.newUserName) < 0) {
        this.userNameValid = true
        return
      }
      // make the error msg visible
      err.classList.add('visible')      
    }
  },

  mounted () {
    this.newUserName = this.user.username

    $('#userSettings').on('shown.bs.modal', function () {
      $('#inputUsername').trigger('focus')
    })
  },

}
</script>
