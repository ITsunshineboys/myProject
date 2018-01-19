<template>
    <div>
      <!--正确模态框-->
      <div v-transfer-dom>
        <x-dialog @on-hide="hide"  v-model="modalStatus.success_status" class="dialog-demo" hide-on-blur>
          <div class="modal-save-success">
            <span>{{modalStatus.dialogTitle}}</span>
          </div>
          <div @click="modalStatus.success_status=false">
            <span class="modal-save-btn">确定</span>
          </div>
        </x-dialog>
      </div>
      <!--错误模态框-->
      <div v-transfer-dom>
        <x-dialog v-model="modalStatus.error_status" class="dialog-demo" hide-on-blur>
          <div class="modal-save-error">
            <p>{{modalStatus.dialogTitle}}</p>
            <p class="small-txt">{{modalStatus.dialogContent}}</p>
          </div>
          <div @click="modalStatus.error_status=false">
            <span class="modal-save-btn">确定</span>
          </div>
        </x-dialog>
      </div>
    </div>
</template>

<script>
  import { XDialog, XSwitch, TransferDomDirective as TransferDom } from 'vux'
  export default {
    name: 'modal',
    components: {
      XDialog,
      XSwitch
    },
    directives: {
      TransferDom
    },
    data () {
      return {}
    },
    methods: {
      hide () {                 // 关闭模态框时，跳转回订单页
        this.$router.go(-1)
      }
    },
    props: {
      modalStatus: Object
    }
  }
</script>

<style lang="less" scoped>
  .dialog-demo{
  .weui-dialog{
    border-radius: 8px;
    padding-bottom: 8px;
  }
  .dialog-title {
    line-height: 30px;
    color: #666;
  }
  .modal-save-success{
    height: 110px;
    text-align: center;
    border-bottom: 1px solid #CDD3D7;
  }
  .modal-save-error{
    padding: 30px 0;
    text-align: center;
    border-bottom: 1px solid #CDD3D7;
  }
  .modal-save-btn{
    color:#222;
    display: inline-block;
    height: 50px;
    line-height: 50px;
  }
  }
  .small-txt{
    font-size: 14px;
    color: #999;
    padding: 0 15px;
  }
</style>
