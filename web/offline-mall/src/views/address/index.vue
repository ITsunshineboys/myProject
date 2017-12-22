<template>
    <div>
      <x-header :left-options="{backText: ''}">编辑收货地址</x-header>
      <div class="bg-white address-box">
        <x-input class="consignee-box" on-change="consignee" title="收货人" label-width="5rem" :placeholder="'请输入姓名'" v-model="consignee"></x-input>
        <x-input class="phone-box" title="联系电话" :placeholder="'请输入联系电话'" mask="999 9999 9999" v-model="contactNumber" :max="13" is-type="china-mobile"></x-input>
        <group class="choose-address-box" label-width="5rem" label-align="left">
          <x-address title="地址选择" v-model="addressValue" raw-value :list="addressData" value-text-align="right"></x-address>
        </group>
        <group class="address-detail-box" label-width="5rem">
          <x-textarea  :title="'详细地址'" :placeholder="'请输入详细地址'" :max=30 :show-counter="false" :rows="1" autosize></x-textarea>
        </group>
      </div>
      <x-button class="save-btn" type="primary" >保存</x-button>
    </div>
</template>

<script>
  import {XHeader, Group, XInput, XAddress, XTextarea, ChinaAddressData, XButton} from 'vux'
//  import { XAddress, ChinaAddressData } from 'vux'
  export default {
    name: 'Address',
    components: {
      XHeader,
      Group,
      XInput,
      XAddress,
      XTextarea,
      XButton
    },
    data () {
      return {
        consignee: '',
        contactNumber: '',
        addressData: ChinaAddressData,
        addressValue: ['四川省', '成都市', '锦江区']
      }
    }
  }
</script>

<style>
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

  /*.address-detail-box */
</style>
