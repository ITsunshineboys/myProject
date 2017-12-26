<template>
  <div class="class-list">
    <x-header :left-options="{backText: ''}">
      <search placeholder="请输入想要购买的商品"></search>
      <span class="iconfont icon-more" slot="right" @click="showMore"></span>
    </x-header>

    <div v-show="showText">
      Hello World!
    </div>
  </div>
</template>

<script>
  import {XHeader, Search} from 'vux'
  export default {
    components: {
      XHeader,
      Search
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
