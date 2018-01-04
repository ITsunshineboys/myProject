<template>
  <div class="comment-container">
    <div class="top-container">
      <img :class="headshotStyle" class="user-pic" :src="userIcon" alt="用户头像">
      <span :class="nameStyle" class="user-name">{{userName}}</span>
      <span :class="dateStyle" class="comment-date">{{commentDate}}</span>
    </div>
    <p>{{content}}</p>
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
    props: ['headshotStyle', 'nameStyle', 'dateStyle', 'userIcon', 'userName', 'commentDate', 'content']
//    data () {
//      return {                                                                                                                                             }
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
