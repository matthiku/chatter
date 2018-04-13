<template>
  <div class="chat-message">
    <p>{{ message.message }}
      <br>
      <small>{{ message.user.name }}</small>
    </p>
  </div>  
</template>


<script>
export default {
  props: ['message'],
}
</script>


<style>

</style>
