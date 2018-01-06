<template>
  <div class="comment-container">
    <div class="top-container">
      <img class="user-pic" :src="userIcon" alt="用户头像">
      <span class="user-name">{{userName}}</span>
      <span class="comment-level">{{commentLevel}}</span>
    </div>
    <p class="comment-date">{{commentDate}}</p>
    <p class="comment-content">{{content}}</p>
    <div class="comment-pics">
      <img v-for="item in images" :src="item" alt="">
    </div>
    <p class="reply" v-if="reply[0]">
      店家回复：{{reply[0]}}
    </p>
  </div>
</template>

<script type="text/ecmascript-6">
  export default {
    name: 'userComment',
    props: ['userIcon', 'userName', 'commentLevel', 'commentDate', 'content', 'images', 'reply'],
    data () {
      return {}
    }
  }
</script>

<style scoped>
  .comment-container {
    padding: 23px 14px 0;
    border-bottom: 1px solid #CDD3D7;
  }

  .top-container,
  .user-pic,
  .user-name,
  .comment-level {
    vertical-align: middle;
  }

  .comment-container {
    background: #FFF;
  }

  .user-pic {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    margin-right: 10px;
  }

  .user-name {
    font-size: 14px;
    color: rgba(102, 102, 102, 1);
    line-height: 14px;
  }

  .comment-level {
    float: right;
    font-size: 14px;
    line-height: 32px;
    color: rgba(217, 173, 101, 1);
  }

  .comment-date {
    margin-top: 15px;
    font-size: 12px;
    color: rgba(153, 153, 153, 1);
    line-height: 12px;
  }

  .comment-content {
    margin-top: 12px;
    font-size: 14px;
    color: rgba(153, 153, 153, 1);
    line-height: 18px;
  }

  .comment-pics {
    padding-top: 12px;
    padding-bottom: 16px;
    border-bottom: 1px solid #E9EDEE;
  }

  .comment-pics img {
    width: 109px;
    height: 109px;
    margin-right: 10px;
  }

  .comment-pics img:nth-child(3) {
    margin-right: 0;
  }

  .reply {
    padding-top: 17px;
    padding-bottom: 17px;
    font-size: 14px;
    color: rgba(153, 153, 153, 1);
    line-height: 18px;
  }

</style>
