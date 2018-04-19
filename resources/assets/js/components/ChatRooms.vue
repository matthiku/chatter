<template>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">

        <div class="card-header">Your Chat Rooms
          <a href="#" class="btn btn-sm btn-success float-right">start new chat</a>
        </div>

        <div class="card-body">
          <div class="accordion" id="chatrooms">

            <div v-for="(room, index) in rooms"
                :key="index"
                class="card">

              <div class="card-header p-0" :id="'heading-'+index">
                <h5 class="mb-0">
                  <button class="btn btn-link collapsed w-100" type="button" 
                      data-toggle="collapse" 
                      aria-expanded="true" 
                      :data-target="'#collapse-'+index" 
                      :aria-controls="'#collapse-'+index">
                    <span class="float-left">
                      {{ room.name }}
                      (<small v-for="(member, index) in room.members"
                          v-if="member.id !== user.id"
                          :key="index"
                          class="font-weight-light">{{ member.username 
                          }}<span v-if="index < room.members-1">,</span>
                      </small>)
                    </span>
                    <span class="float-right">
                      <i class="material-icons">message</i>
                      <span class="badge badge-secondary badge-pill float-right">{{ room.messages.length }}</span>
                    </span>
                  </button>
                </h5>
              </div>

              <div :id="'collapse-'+index" 
                  :aria-labelledby="'heading-'+index"
                  class="collapse"
                  data-parent="#chatrooms">

                <div class="card-body">
                  <chat-log
                      :messages="room.messages"
                      :members="room.members"
                    ></chat-log>
                </div>
              </div>

            </div> <!-- LOOP: room in rooms -->

          </div>
        </div>

      </div>
    </div>
  </div>
</template>


<script>
export default {

  computed: {
    rooms () {
      return this.$store.state.chat.rooms
    },
    user () {
      return this.$store.state.user.user
    }
  }

}
</script>
