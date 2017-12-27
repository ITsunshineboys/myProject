<template>
    <div>
      <x-header :left-options="{backText: ''}">发票信息</x-header>
      <!--发票类型-->
      <div class="bg-white invoice-header-box">
        <div class="invoice-type">
          <span class="invoice-txt">发票类型</span>
          <div class="invoice-top">
            <x-button mini class="btn-black">普通发票</x-button>
          </div>
        </div>
      </div>
      <!--发票抬头-->
      <div class="bg-white invoice-header-box">
        <div class="invoice-type">
          <span class="invoice-txt">发票抬头</span>
          <div class="invoice-top">
            <span @click="personalClick">
              <i class="iconfont" :class="{'icon-radio-checked':chooseSelect,'icon-radio':chooseDefault}"></i>
              <span style="margin-right: 30px">个人</span>
            </span>
            <span @click="unitClick">
              <i class="iconfont" :class="{'icon-radio-checked':chooseDefault,'icon-radio':chooseSelect}"></i>
              <span>单位</span>
            </span>
          </div>
          <div class="invoice-top">
            <input type="text" class="invoice-input" v-model="invoiceHeaderValue" placeholder="请输入抬头名称">
            <input type="text" v-if="chooseDefault" v-model="taxpayerValue"  class="invoice-input" style="margin-top: 15px" placeholder="请输入纳税人识别号" maxlength=18>
            <span class="small-txt" v-if="chooseDefault">纳税人识别号为18位数字加大写英文字母组成，请仔细确认</span>
          </div>
        </div>
      </div>
      <!--发票内容-->
      <div class="bg-white invoice-header-box">
        <div class="invoice-type">
          <span class="invoice-txt">发票内容</span>
          <div class="invoice-top">
            <i class="iconfont icon-radio-checked"></i>
            <span style="margin-right: 30px">明细</span>
          </div>
        </div>
      </div>
      <x-button class="save-btn" v-if=cardFlag :class="{'save-btn-true':invoiceHeaderValue}" type="button" :text="'确认'" @click.native="btnClick" :disabled="!invoiceHeaderValue"></x-button>
      <x-button class="save-btn" v-else :class="{'save-btn-true':invoiceHeaderValue && taxpayerValue}" type="button" :text="'确认'" @click.native="btnClick" :disabled="!invoiceHeaderValue || !taxpayerValue"></x-button>
      <Modal :modalStatus="modalStatus"></Modal>
    </div>
</template>

<script>
  import { XHeader, XButton, XInput, XDialog, XSwitch, TransferDomDirective as TransferDom } from 'vux'
  import Modal from '../order/modal'
  export default {
    name: 'Invoice',
    components: {
      XHeader,
      XButton,
      XInput,
      XDialog,
      XSwitch,
      Modal
    },
    directives: {
      TransferDom
    },
    data () {
      return {
        modalStatus: {},
        invoiceHeaderValue: '',
        taxpayerValue: '',
        chooseSelect: true,
        chooseDefault: false,
        cardFlag: true
      }
    },
    methods: {
      btnClick () {
        let invoicerReg = /^(?!(?:\d+)$)[\dA-Z]{18}$/
        this.cardFlag === true ? this.taxpayerValue = '' : this.taxpayerValue = this.taxpayerValue
        if (this.cardFlag) {
          this.modalStatus = {
            success_status: true,
            dialogTitle: '保存成功'
          }
          this.axios.post('/order/add-line-order-invoice', {
            invoice_type: '1',
            invoice_header_type: '2',
            invoice_header: this.invoiceHeaderValue,
            invoice_content: '明细',
            invoicer_card: this.taxpayerValue
          }, (res) => {
            console.log(res)
            if (res.code === 200) {
              sessionStorage.setItem('invoice_id', res.data.invoice_id)
            }
          })
        } else {
          if (invoicerReg.test(this.taxpayerValue)) {
            this.modalStatus = {
              success_status: true,
              dialogTitle: '保存成功'
            }
            this.axios.post('/order/add-line-order-invoice', {
              invoice_type: '1',
              invoice_header_type: '2',
              invoice_header: this.invoiceHeaderValue,
              invoice_content: '明细',
              invoicer_card: this.taxpayerValue
            }, (res) => {
              console.log(res)
              if (res.code === 200) {
                sessionStorage.setItem('invoice_id', res.data.invoice_id)
              }
            })
          } else {
            console.log(this.taxpayerValue)
            this.modalStatus = {
              error_status: true,
              dialogTitle: '纳税人识别号输入不正确',
              dialogContent: '请重新输入'
            }
          }
        }
      },
      personalClick () {         // 个人 点击
        this.chooseSelect = true
        this.chooseDefault = false
        this.cardFlag = true
      },
      unitClick () {            // 单位 点击
        this.chooseSelect = false
        this.chooseDefault = true
        this.cardFlag = false
      }
    },
    activated () {
      this.chooseSelect = true
      this.chooseDefault = false
      this.cardFlag = true
      if (sessionStorage.getItem('invoice_id')) {
        this.axios.get('/order/get-line-order-invoice-data', {
          invoice_id: sessionStorage.getItem('invoice_id')
        }, (res) => {
          this.invoiceHeaderValue = res.data.invoice_header
          this.taxpayerValue = res.data.invoicer_card
        })
      } else {
        this.invoiceHeaderValue = ''
        this.taxpayerValue = ''
      }
    }
  }
</script>

<style lang="less">
  .invoice-header-box{
    margin-top: 10px;
    padding: 15px;
  }
  .invoice-type{
    font-size: 18px;
    color: #666;

  }
  .invoice-txt{
    border-left:3px solid #222;
    padding-left: 5px;
  }
  .invoice-top{
    margin-top: 20px;
  }
  .btn-black{
    color: #fff!important;
    background-color: #222!important;
  }
  .invoice-input{
    width: 100%;
    font-size: 16px;
    color: #999;
    padding: 10px 0;
    border-radius: 6px;
    border:1px solid #E5E5E5;
    outline: 0;
    text-indent: 10px;
  }
  .save-btn{
    font-size: 18px !important;
    margin-top: 30px !important;
    width: 92.5% !important;
    text-align: center !important;
    color:rgba(255,255,255,0.25)!important;
    background:rgba(34,34,34,0.3)!important;
  }
  .save-btn-true{
    color:rgba(255,255,255,1)!important;
    background:rgba(34,34,34,1)!important;
  }
  .small-txt{
    font-size: 12px;
    color: #999;
  }
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
    line-height: 110px;
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

</style>
