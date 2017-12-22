<template>
    <div class="bg-white address-box">
      <x-header :left-options="{backText: ''}">编辑收货地址</x-header>
      <div class="consignee-box">
        <x-input title="收货人" label-width="5em" :placeholder="'请输入姓名'" v-model="consignee"></x-input>
      </div>
      <x-input title="联系电话" :placeholder="'请输入联系电话'" mask="999 9999 9999" v-model="contactNumber" :max="13" is-type="china-mobile"></x-input>
      <group label-width="5em" label-align="left">
        <x-address title="地址选择" v-model="addressValue" raw-value :list="addressData" value-text-align="left"></x-address>
      </group>
      <group>
        <x-textarea :title="'详细地址'" :placeholder="'请输入详细地址'" :max=30 :show-counter="false" :rows="1" autosize></x-textarea>
      </group>
    </div>
</template>

<script>
  import {XHeader, Group, XInput, XAddress, XTextarea, ChinaAddressData} from 'vux'
//  import { XAddress, ChinaAddressData } from 'vux'
  export default {
    name: 'Address',
    components: {
      XHeader,
      Group,
      XInput,
      XAddress,
      XTextarea
    },
    data () {
      return {
        consignee: '',
        contactNumber: '',
        addressData: ChinaAddressData,
        addressValue: ['广东省', '深圳市', '南山区']
      }
    }
  }
</script>

<style scoped>
  .address-box{
    margin-top: 10px;
  }
</style>
