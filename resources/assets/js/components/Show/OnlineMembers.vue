<template>
  <div>

    <i class="material-icons">group</i>
    <span class="d-none d-sm-inline">Online:</span>

    <!-- show names as clickable badges - click opens new chat -->
    <a href="#" v-for="(u, idx) in onlineUsers" :key="idx"
        v-if="user.id !== u.id"
        class="badge badge-pill badge-info mr-2"
        @click="launchNewRoomModal(u.id)"
        :title="'click to start chatting with ' + u.username"
      >{{ u.username }}
    </a>

    <!-- // show icon if no-one is online -->
    <strong v-if="onlineUsers.length < 2"><i class="material-icons">settings_ethernet</i></strong>

  </div>
</template>


<script>
export default {

  props: ['onlineUsers', 'user'],

  methods: {
    launchNewRoomModal (user_id) {
      this.$store.commit('setNewRoomMembers', [user_id])
      this.$store.commit('setDialog', {what: 'createNewRoom', option: 'single'})
    }    
  }
}
</script>
