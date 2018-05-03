<template>
  <div class="modal fade" id="selectEmoticons" tabindex="-1" role="dialog" aria-labelledby="selectEmoticonsLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="selectEmoticonsLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="d-flex bd-highlight">
            <div class="p-2 flex-fill bd-highlight">Flex item</div>
            <div class="p-2 flex-fill bd-highlight">Flex item</div>
            <div class="p-2 flex-fill bd-highlight">Flex item</div>
            <div class="p-2 flex-fill bd-highlight">Flex item</div>
            <div class="p-2 flex-fill bd-highlight">Flex item</div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      emoticons: []
    }
  },

  mounted () {
    // create an array with all emoticon character HTML values
    let start = 128512
    for (let index = 0; index < 80; index++) {
      this.emoticons.push(start + index)
    }
  }
}
</script>

<style>

</style>
