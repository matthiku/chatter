<template>
  <div id="insertFiles"
      class="modal fade"
      @keyup.esc="closeDialog"
      tabindex="-1" role="dialog" 
      aria-labelledby="insertFilesLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="insertFilesLabel">Insert Files</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <vue-dropzone id="uploadDropZone"
              :options="config"
              @vdropzone-complete="afterComplete"
              ref="uploadDropZone"
            ></vue-dropzone>
        </div>

      </div>
    </div>

  </div>
</template>


<script>
// DropZone to upload files
import vueDropzone from "vue2-dropzone";

export default {
  props: ['room', 'capture'],
  
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
    closeDialog() {
      $('#insertFiles').modal('hide')      
    },
    afterComplete(file) {
      console.log(file);
      // remove the just uploaded file from the dropzone
      this.$refs.uploadDropZone.removeFile(file)
      // and close the dialog
      this.closeDialog()
    }
  }
}
</script>
