<template>
  <div class="comment-container">
    <div class="top-container">
      <img :class="headshotStyle" class="user-pic" src="" alt="">
      <span :class="nameStyle" class="user-name">包子</span>
      <span :class="dateStyle" class="comment-date">2017-09-12</span>
    </div>
    <p>
      不知道你的团队如何定义前端开发，据我所知，时至今日仍然有很多团队会把前端开发归类为产品或者设计岗位，虽然身份之争多少有些无谓，但我对这种偏见还是心存芥蒂，酝酿了许久，决定写一个系列的文章，试着从工程的角度系统的介绍一下我对前端，尤其是Web前端的理解。</p>
    <!--<div>-->
      <!--<img src="" alt="">-->
      <!--<img src="" alt="">-->
      <!--<img src="" alt="">-->
      <!--<img src="" alt="">-->
      <!--<img src="" alt="">-->
    <!--</div>-->
  </div>
</template>

<script type="text/ecmascript-6">
  export default {
    name: 'comment',
    props: ['headshotStyle', 'nameStyle', 'dateStyle']
//    data () {
//      return {}
//    },
//    methods: {}
  }
</script>

<style scoped>
  .comment-container {
    padding: 14px 20px 16px;
    border-top: 2px solid #E9EDEE;
    border-bottom: 2px solid #E9EDEE;
  }

  .top-container,
  .user-pic,
  .user-name,
  .comment-date{
    vertical-align: middle;
  }

  .top-container {
    padding-bottom: 14px;
  }

  .comment-date {
    float: right;
    margin-top: 14px;
  }

  p {
    font-size:14px;
    letter-spacing: 0.5px;
    color:rgba(153,153,153,1);
    line-height:20px;
    height: 40px;
    overflow: hidden;
    /*white-space: nowrap;*/
    /*text-overflow: ellipsis;*/
  }

  /*.user-pic {*/
    /*vertical-align: middle;*/
  /*}*/

   /*{*/
    /*vertical-align: middle;*/
  /*}*/
</style>
