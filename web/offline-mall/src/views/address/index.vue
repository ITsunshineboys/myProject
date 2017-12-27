<template>
    <div>
      <x-header :left-options="{backText: ''}">编辑收货地址</x-header>
      <div class="bg-white address-box">
        <x-input class="consignee-box" v-model="consignee" title="收货人" label-width="5rem" :placeholder="'请输入姓名'" :max=10></x-input>
        <x-input class="phone-box" v-model="phoneNumber" title="联系电话" name="mobile"  placeholder="请输入联系电话" :max="11"></x-input>
        <group class="choose-address-box" label-width="5rem" label-align="left">
          <x-address :confirm-text="'确认'" :title="'地址选择'" v-model="addressValue" raw-value :list="addressData" value-text-align="right"></x-address>
        </group>
        <group class="address-detail-box" label-width="5rem">
          <x-textarea  :title="'详细地址'" :placeholder="'请输入详细地址'" v-model="detailAddress" :max=30 :show-counter="false" :rows="1" autosize></x-textarea>
        </group>
      </div>
      <x-button class="save-btn" :class="{'save-btn-true':consignee && phoneNumber && detailAddress}" type="primary" :text="'保存'" @click.native="btnClick" :disabled="!consignee || !phoneNumber || !detailAddress"></x-button>
      <!--确认模态框-->
      <Modal :modalStatus="modalStatus"></Modal>
    </div>
</template>

<script>
  import {XHeader, Group, XInput, XAddress, XTextarea, ChinaAddressV4Data, XButton, XDialog, XSwitch, TransferDomDirective as TransferDom} from 'vux'
  import Modal from '../order/modal'
  export default {
    name: 'Address',
    components: {
      XHeader,
      Group,
      XInput,
      XAddress,
      XTextarea,
      XButton,
      XDialog,
      XSwitch,
      Modal
    },
    directives: {
      TransferDom
    },
    activated () {
      if (sessionStorage.getItem('address_id')) {
        this.axios.get('/order/get-line-receive-address', {
          address_id: sessionStorage.getItem('address_id')
        }, (res) => {
          console.log(res)
          this.consignee = res.data.consignee
          this.phoneNumber = res.data.mobile
          this.addressValue = res.data.district.split(',')
          this.detailAddress = res.data.region
        })
      } else {
        this.consignee = ''
        this.phoneNumber = ''
        this.addressValue = ['四川省', '成都市', '锦江区']
        this.detailAddress = ''
      }
    },
    data () {
      return {
        modalStatus: {},
        requiredStatus: false,
        phoneStatus: false,
        addressStatus: false,
        consignee: '',
        phoneNumber: '',
        addressValue: ['四川省', '成都市', '锦江区'],
        detailAddress: '',
        addressData: ChinaAddressV4Data
      }
    },
    methods: {
      btnClick () {
        let phoneReg = /^1[3|4|5|7|8][0-9]{9}$/
        if (phoneReg.test(this.phoneNumber)) {
          this.modalStatus = {
            success_status: true,
            dialogTitle: '保存成功'
          }
          this.axios.post('/order/add-line-receive-address', {
            consignee: this.consignee,
            mobile: this.phoneNumber,
            district_code: this.addressValue[2],
            region: this.detailAddress
          }, (res) => {
            console.log(res)
            if (res.code === 200) {
              sessionStorage.setItem('address_id', res.data.address_id)
            }
          })
        } else {
          this.modalStatus = {
            error_status: true,
            dialogTitle: '手机号输入不正确',
            dialogContent: '请重新输入'
          }
        }
      },
      hide () {                 // 关闭模态框时，跳转回订单页
        this.$router.go(-1)
      }
    }
  }
</script>

<style lang="less">
  @import '~vux/src/styles/close';
  .bg-white,
  .choose-address-box .vux-cell-value{
    color: #666;
  }
  .address-box{
    margin-top: 10px;
  }

  .consignee-box .weui-label,
  .phone-box .weui-label{
    margin-right: 10px;
    border-right: 1px solid #CDD3D7;
  }

  .address-detail-box .weui-label{
    margin-right: 10px;
    border-right: 1px solid #CDD3D7;
  }

  .choose-address-box .weui-cells,
  .address-detail-box .weui-cells{
    margin-top: 0;
  }

  .address-detail-box .weui-cells:before{
    border: 0;
  }

  .address-detail-box .weui-textarea,
  .choose-address-box .vux-cell-value{
    font-size: 14px;
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
  .vux-popup-header-left,
  .vux-popup-header-right{
    color:#222!important;
  }
</style>
