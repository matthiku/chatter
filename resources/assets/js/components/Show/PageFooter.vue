<template>
  <nav
      class="navbar fixed-bottom py-0 py-sm-1 py-md-2 navbar-light" style="background-color: #e3f2fd;"
    >

      <small>
        &copy; 2018 <a href="https://github.com/matthiku/chatter">M.Kuhs</a>
      </small>

      <small v-if="user.id === 1"
          class="float-right ml-2"
        >
        <a href="/logs" target="new">Logs</a>
      </small>

      <small
          v-if="latestFrontendVersion && latestFrontendVersion !== frontendTimestamp"
          class="float-right ml-2"
        >
        &bull; New version available: 
        <a href="/home" class="btn btn-sm btn-info" >Reload!</a>
      </small>

      <small
          v-if="!latestFrontendVersion || (latestFrontendVersion && latestFrontendVersion === frontendTimestamp)"
          class="float-right"
          :title="'Last software update: ' + $moment.unix(frontendTimestamp).format('YYYY-MM-DD H:mm')"
        >
        App is up2date!
      </small>

  </nav>
</template>


<script>
export default {

  computed: {
    user () {
      return this.$store.state.user.user
    },

    frontendTimestamp () {
      return this.$store.state.shared.frontendTimestamp
    },

    latestFrontendVersion () {
      return this.$store.state.shared.latestFrontendVersion
    }
  }

}
</script>
