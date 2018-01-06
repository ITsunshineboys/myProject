<template>
  <div class="header-container">
    <i class="iconfont icon-return"></i>
    <span>{{headTitle}}</span>
    <slot name="more"></slot>
  </div>
</template>

<script type="text/ecmascript-6">
  export default {
    name: 'HeaderCommon',
    props: ['headTitle'],
    data () {
      return {}
    }
  }
</script>

<style scoped>
  .header-container {
    position: relative;
    height: 64px;
    padding: 33px 14px 13px;
    text-align: center;
    background: rgba(255, 255, 255, 1);
  }

  .header-container i {
    font-size: 20px;
    color: rgba(34, 34, 34, 1);
  }

  .header-container span {
    font-size: 18px;
    color: rgba(102, 102, 102, 1);
  }

  .header-container .icon-return {
    float: left;
  }

  .header-container .icon-more {
    position: absolute;
    right: 14px;

  }

</style>
