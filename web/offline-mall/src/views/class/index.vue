<template>
  <div class="class-list">
    <x-header :left-options="{backText: ''}">
      算了肯德基的开发了
      <span class="iconfont icon-home" slot="right" @click="showMore"></span>
    </x-header>

    <div v-show="showText">
      Hello World!
    </div>
  </div>
</template>

<script>
  import {XHeader} from 'vux'
  export default {
    components: {
      XHeader
    },
    data () {
      return {
        showText: false
      }
    },
    methods: {
      showMore () {
        this.showText = !this.showText
      }
    }
  }
</script>

<style scoped>

</style>
