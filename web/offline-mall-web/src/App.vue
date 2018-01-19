<template>
  <div id="app">
    <transition  enter-active-class="animated fadeIn">
      <keep-alive>
        <router-view></router-view>
      </keep-alive>
    </transition>
  </div>
</template>

<script>
  export default {
    name: 'app'
  }
</script>

<style lang="less">
  @import '~vux/src/styles/reset.less';
  body {
    background-color: #f6f6f6;
  }
</style>
