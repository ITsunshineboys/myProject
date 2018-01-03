<template>
  <div class="class-list">
    <v-header :go-link="'/'" @show="isShow"></v-header>
    <div class="content">
      <div class="content-left" :class="{'pop-open': isPopOpen}">
        <div class="active" v-for="obj in 20">
          <a href="javascript: void (0);">01</a>
        </div>
      </div>
      <div class="content-right" :class="{'pop-open': isPopOpen}">
        <div class="cateList" v-for="obj in 10">
          <div class="hd">
            <span class="text">地面材料</span>
          </div>
          <ul class="list">
            <li class="cate-item" v-for="obj in 8">
              <router-link to="/">
                <div class="cate-img-wrapper">
                  <img src="http://service.cdlhzz.cn/static/image/2017/12/15/1513310448673.jpg">
                </div>
                <div class="name">木地板</div>
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import vHeader from '../../components/HeaderSearch'

  export default {
    components: {
      vHeader
    },
    data () {
      return {
        isPopOpen: false
      }
    },
    methods: {
      isShow: function (bool) {
        this.isPopOpen = bool
      }
    }
  }
</script>

<style>
  .content {
    position: absolute;
    top: 10px;
    bottom: 0;
    display: flex;
    margin-top: 50px;
    width: 100%;
    background-color: #fff;
  }

  .content-left,
  .content-right {
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
  }

  .content-left.pop-open,
  .content-right.pop-open {
    overflow-y: hidden;
  }

  .content-left {
    flex: 0 0 110px;
    border-right: 1px solid #cdd3d7;
  }

  .content-right {
    padding: 30px 18px;
  }

  .content-left div {
    margin-top: 25px;
    margin-bottom: 25px;
    padding: 5px 0;
    line-height: 16px;
    font-size: 16px;
  }

  .content-left div a {
    display: block;
    margin-left: 14px;
    padding-left: 5px;
    color: #999999;
    border-left: 3px solid transparent;
  }

  .content-left div.active a {
    color: #222222;
    border-color: #222222;
  }

  .hd {
    margin-bottom: 20px;
    height: 30px;
    line-height: 30px;
    text-align: center;
    color: #666;
  }

  .hd .text {
    position: relative;
  }

  .hd .text:after, .hd .text:before {
    position: absolute;
    content: '';
    top: 0;
    bottom: 0;
    margin: auto;
    height: 1px;
    width: 20px;
    background-color: #cdd3d7;
    -webkit-transform-origin: 50% 100% 0;
    transform-origin: 50% 100% 0;
  }

  .hd .text:before {
    left: -44px;
  }

  .hd .text:after {
    right: -44px;
  }

  .cate-item {
    display: inline-block;
    margin-right: 24px;
    margin-bottom: 30px;
    font-size: 0;
    width: 60px;
    vertical-align: top;
  }

  .cate-item:nth-child(3n) {
    margin-right: 0;
  }

  .cate-img-wrapper {
    width: 60px;
    height: 60px;
  }

  .cate-img-wrapper img {
    width: 100%;
  }

  .cate-item .name {
    font-size: 14px;
    color: #999;
    text-align: center;
  }
</style>
