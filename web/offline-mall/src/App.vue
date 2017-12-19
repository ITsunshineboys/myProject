<template>
  <div id="app">
    <router-view></router-view>
    <button @click="show">点击</button>
  </div>
</template>

<script>
  import http from './api'

  export default {
    name: 'app',
    data () {
      return {
        id: 5
      }
    },
    methods: {
      async show () {
        let params = {
          id: 48
        }
        const res = await http.post('/effect/effect-view', params)
        console.log(res.data)
      }
    }
  }
</script>

<style lang="less">
  @import '~vux/src/styles/reset.less';

  body {
    background-color: #fbf9fe;
  }
</style>
