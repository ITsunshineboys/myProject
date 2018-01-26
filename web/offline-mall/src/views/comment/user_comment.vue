<template>
  <div class="comment-container">
    <div class="top-container">
      <img :src="userIcon" alt="用户头像">
      <span>{{userName | stringSubstr}}</span>
      <span>{{commentLevel}}</span>
    </div>
    <p class="comment-date">{{commentDate}}</p>
    <p class="comment-content">{{content}}</p>
    <div class="comment-pics">
      <img class="previewer-demo-img" v-for="(item, index) in images" :src="item.src" width="100"
           @click="show(index)">
      <div>
        <previewer :list="images" ref="previewer"></previewer>
      </div>
    </div>
    <p class="reply" v-if="reply[0]">
      店家回复：{{reply[0]}}
    </p>
  </div>
</template>

<script type="text/ecmascript-6">
  import {Previewer} from 'vux'
  export default {
    name: 'userComment',
    components: {
      Previewer
    },
    props: ['userIcon', 'userName', 'commentLevel', 'commentDate', 'content', 'images', 'reply'],
    data () {
      return {
        previewerList: []
      }
    },
    created () {
      // 用户评价图片处理
//      if (typeof this.images === 'undefined') return

    },
//    updated () {
//      this.previewerList = this.images.map((item) => ({
//        src: item
//      }))
//    },
    methods: {
      // 用户评价图片放大 原组件方法
      show (index) {
        this.$refs.previewer.show(index)
      }
    },
    filters: {
      // 用户名称字符截取过滤器
      stringSubstr (value) {
//        if (typeof value === 'undefined') return
//        console.log(value, '用户名称字符截取')
        if (value.length <= 10) {
          return value
        } else {
          return value.substr(0, 10) + '...'
        }
      }
    }
  }
</script>

<style scoped>
  .comment-container {
    padding: 23px 14px 0;
    border-bottom: 1px solid #CDD3D7;
    background: #FFF;
  }

  .top-container,
  .top-container img,
  .top-container span:nth-child(2),
  .top-container span:last-child {
    vertical-align: middle;
  }

  /* 用户头像 */
  .top-container img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    margin-right: 10px;
  }

  /* 用户名 */
  .top-container span:nth-child(2) {
    font-size: 14px;
    color: rgba(102, 102, 102, 1);
    line-height: 14px;
  }

  /* 评价等级 */
  .top-container span:last-child {
    float: right;
    font-size: 14px;
    line-height: 32px;
    color: rgba(217, 173, 101, 1);
  }

  /* 评价日期 */
  .comment-date {
    margin-top: 15px;
    font-size: 12px;
    color: rgba(153, 153, 153, 1);
    line-height: 12px;
  }

  /* 评价内容 */
  .comment-content {
    margin-top: 12px;
    font-size: 14px;
    color: rgba(153, 153, 153, 1);
    line-height: 18px;
    word-break: break-all
  }

  /* 评价图片 */
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

  /* 卖家回复 */
  .reply {
    padding-top: 17px;
    padding-bottom: 17px;
    font-size: 14px;
    color: rgba(153, 153, 153, 1);
    line-height: 18px;
  }
</style>
<style>
  .pswp--supports-fs .pswp__button--fs {
    display: none !important;
  }
</style>
