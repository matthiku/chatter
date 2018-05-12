<template>
  <div class="modal fade" id="insertFiles" tabindex="-1" role="dialog" aria-labelledby="insertFilesLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="insertFilesLabel">Insert Files</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <vue-dropzone id="upload" :options="config"></vue-dropzone>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
// DropZone to upload files
import vueDropzone from "vue2-dropzone";

export default {
  props: ['room'],
  
  data () {
    return {
      config: {
        url: `/api/messages/${this.room.id}/upload`,
        headers: {
          'X-CSRF-TOKEN' : document.head.querySelector('meta[name="csrf-token"]').content,
          'X-XSRF-TOKEN' : document.cookie.split('=')[1]
        }
      }
    }
  },

  components: {
    vueDropzone
  },

  methods: {
  }
}
</script>

<style>

</style>
