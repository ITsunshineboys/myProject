<template>
  <div>
    <msg class="success-box" :title="'支付成功'">
      <p class="success-title" slot="description">我们会尽快发货，请您耐心等待</p>
      <p class="success-back" slot="description">{{number}}秒后自动跳转商城首页</p>
    </msg>
  </div>
</template>

<script>
  import {Msg} from 'vux'

  export default {
    name: 'PaySuccess',
    components: {
      Msg
    },
    data () {
      return {
        number: 5
      }
    },
    mounted () {
      const timeInterval = setInterval(() => {
        this.number--
        console.log(this.number)
        if (this.number < 1) {
          clearInterval(timeInterval)
          this.$router.push({path: 'order'})
        }
      }, 1000)
    }
  }
</script>

<style>
  .success-box {
    margin-top: 100px;
  }
  .success-box .weui-msg__title{
    color: #666;
  }
  .success-box .weui-icon-success {
    color: #222222 !important;
  }

  .success-box .weui-icon_msg {
    font-size: 48px;
  }

  .success-title {
    margin-top: 50px;
    color: #666;
    font-size: 18px;
  }

  .success-back {
    margin-top: 20px;
    color: #999;
    font-size: 14px;
  }
</style>
