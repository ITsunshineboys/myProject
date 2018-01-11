<template>
  <div class="comment-container">
    <div class="top-container">
      <img :src="userIcon" alt="用户头像">
      <span class="user-name">{{userName}}</span>
      <span class="comment-date">{{commentDate}}</span>
    </div>
    <p>{{content}}</p>
  </div>
</template>

<script type="text/ecmascript-6">
  export default {
    name: 'comment',
    props: ['userIcon', 'userName', 'commentDate', 'content']
  }
</script>

<style scoped>
  .comment-container {
    padding: 14px 20px 16px;
    border-top: 2px solid #E9EDEE;
    border-bottom: 2px solid #E9EDEE;
  }

  .top-container,
  .top-container img,
  .user-name,
  .comment-date {
    vertical-align: middle;
  }

  .top-container {
    padding-bottom: 14px;
    line-height: 50px;
  }

  .top-container img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 15px;
  }

  .user-name {
    font-size: 16px;
    color: rgba(153, 153, 153, 1);
  }

  .comment-date {
    float: right;
    margin-top: 4px;
    font-size: 14px;
    color: rgba(153, 153, 153, 1);
  }

  p {
    font-size: 14px;
    letter-spacing: 0.5px;
    color: rgba(153, 153, 153, 1);
    line-height: 20px;
    height: 40px;
    overflow: hidden;
  }
</style>
