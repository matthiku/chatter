/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('chat-log', require('./components/ChatLog.vue'));
Vue.component('chat-header', require('./components/ChatHeader.vue'));
Vue.component('chat-message', require('./components/ChatMessage.vue'));
Vue.component('chat-composer', require('./components/ChatComposer.vue'));

const app = new Vue({

  el: '#app',


  data: {
    messages: [],
    user: {},
    usersInRoom: []
  },


  methods: {
    addMessage (payload) {
      // persist new message to backend DB
      axios.post('/messages', {message: payload.message})
      .then(response => {
        if (!response.data) {
          console.warn(response);
        }
      });
    }
  },


  created () {
    // get the user object from the global namespace (set in layouts\app.blade.php)
    this.user = chatter_server_data.user

    // get all messages from the backend, but only when user was logged in
    if (this.user.name !== 'guest') {
      axios.get('/messages')
      .then(response => {
        if (response.data) {
          this.messages = response.data;
        }
      });
      
      // start listening to our backend broadcast channel
      Echo.join('chatroom')
      
      .here((users) => {
        // getting list of all users logged into this room
        this.usersInRoom = users
      })
      
      .joining(user => this.usersInRoom.push(user))
      
      .leaving(user => this.usersInRoom = this.usersInRoom.filter(u => u !== user))
      
      .listen('MessagePosted', (e) => {
        if (e.message) {
          let msg = e.message;
          msg.user = e.user;
          this.messages.push(msg);
        } else {
          console.warn(e);
        }
      });
    }
  }

});
