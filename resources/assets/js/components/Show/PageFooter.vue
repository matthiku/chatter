<template>
  <span>

      <small>
        &copy; 2018 <a href="https://github.com/matthiku/chatter">Matthias Kuhs</a>
      </small>

      <small
          v-if="latestFrontendVersion && latestFrontendVersion !== frontendTimestamp"

          class="float-right ml-2"
        >
        &bull; New version available: 
        <a href="/home">Reload!</a>
      </small>

      <small
          v-if="!latestFrontendVersion || (latestFrontendVersion && latestFrontendVersion === frontendTimestamp)"

          class="float-right"
          :title="'Last software update:' + $moment.unix(frontendTimestamp).format()"
        >
        Version: latest!
      </small>

  </span>
</template>


<script>
export default {

  computed: {
    frontendTimestamp () {
      return this.$store.state.shared.frontendTimestamp
    },

    latestFrontendVersion () {
      return this.$store.state.shared.latestFrontendVersion
    }
  }

}
</script>
