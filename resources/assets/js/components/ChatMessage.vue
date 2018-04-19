<template>
  <div class="chat-message">
    <p>{{ message.message }}
      <br>
      <small>{{ membersObj[message.user_id].username }}</small>
    </p>
  </div>  
</template>


<script>
export default {
  props: ['message', 'members'],

  computed: {
    membersObj () {
      let obj = {}
      this.members.map(elem => obj[elem.id] = elem)
      return obj
    }
  }
}
</script>


<style>

</style>
