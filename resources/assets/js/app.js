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
Vue.component('chat-message', require('./components/ChatMessage.vue'));
Vue.component('chat-composer', require('./components/ChatComposer.vue'));

const app = new Vue({

  el: '#app',


  data: {
    messages: [],
    user: {}
  },


  methods: {
    addMessage (payload) {
      // add to local list of messages
      this.messages.push(payload);
      // push to backend
      axios.post('/chatter/public/messages', {message: payload.message})
      .then(response => {
        if (response.data) {
          this.messages = response.data;
        }          
      });
    }
  },


  created () {
    // get the user object from the global namespace (set in layouts\app.blade.php)
    this.user = JSON.parse(chatter_server_data.user);

    // get all messages from the backend
    axios.get('/chatter/public/messages')
    .then(response => {
      if (response.data) {
        this.messages = response.data;
      }
    });
  }

});
