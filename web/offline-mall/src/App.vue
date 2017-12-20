<template>
  <div id="app">
    <router-view></router-view>
    <button @click="show">点击</button>
  </div>
</template>

<script>
  export default {
    name: 'app',
    data () {
      return {
        id: 5
      }
    },
    methods: {
      show () {
        let params = {
          id: 60
        }
        console.log(this)
        this.axios.post('/effect/effect-view', function (res) {
          console.log(res)
        })
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
