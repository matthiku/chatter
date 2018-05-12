<template>
  <div class="modal fade" id="selectEmoticons" tabindex="-1" role="dialog" aria-labelledby="selectEmoticonsLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="selectEmoticonsLabel">Insert Emoticon</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div v-for="n in 8" :key="n" class="d-flex">
            <div v-for="m in 10" :key="m"
                @click="insert(10 * (n-1) + m)"
                class="m-1 py-sm-1 text-center flex-fill border border-info bg-light cursor-pointer rounded"
                v-html="'&' + `#${emoticons[10 * (n-1) + m]};`">
            </div>
          </div>
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
    let start = 128511
    for (let idx = 0; idx < 81; idx++) {
      this.emoticons.push(start + idx)
    }
  },

  methods: {
    insert (index) {
      this.$emit('insert-emo', this.emoticons[index])
      $('#selectEmoticons').modal('toggle')
    }
  }
}
</script>

<style>

</style>
