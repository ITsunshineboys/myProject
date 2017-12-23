<template>
    <div>
      <x-header :left-options="{backText: ''}">编辑收货地址</x-header>
      <div class="bg-white address-box">
        <x-input class="consignee-box" v-model="consignee" @on-change="consigneeChange" title="收货人" label-width="5rem" :placeholder="'请输入姓名'"></x-input>
        <x-input class="phone-box"  ref="phone_ref" @on-change="phoneChange" title="联系电话" name="mobile"  v-model="phoneNumber"  placeholder="请输入联系电话" keyboard="number" is-type="china-mobile" :max="11"></x-input>
        <group class="choose-address-box" label-width="5rem" label-align="left">
          <x-address title="地址选择" v-model="addressValue" raw-value :list="addressData" value-text-align="right"></x-address>
        </group>
        <group class="address-detail-box" label-width="5rem">
          <x-textarea  :title="'详细地址'" @on-change="addressChange" :placeholder="'请输入详细地址'" v-model="detailAddress" :max=30 :show-counter="false" :rows="1" autosize></x-textarea>
        </group>
      </div>
      <x-button class="save-btn" :class="{'save-btn-true':requiredStatus && phoneStatus && addressStatus}" type="primary" :text="'保存'" @click.native="btnClick" :disabled="!requiredStatus || !phoneStatus || !addressStatus"></x-button>
    </div>
</template>

<script>
  import {XHeader, Group, XInput, XAddress, XTextarea, ChinaAddressV4Data, XButton} from 'vux'
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
        this.axios.post('/order/add-line-receive-address', {
          consignee: this.consignee,
          mobile: this.phoneNumber,
          district_code: this.addressValue[2],
          region: this.detailAddress
        }, function (res) {
          console.log(res)
        })
//        console.log(this.addressValue[2])
      },
      consigneeChange (value) {   // 收货人
        let that = this
        value === '' ? that.requiredStatus = false : that.requiredStatus = true
      },
      phoneChange () {
        let that = this
        that.phoneStatus = this.$refs.phone_ref.valid
        if (that.phoneStatus === true) {
          that.phoneNumber === '' ? that.phoneStatus = false : that.phoneStatus = true
        }
      },
      addressChange () {
        let that = this
        that.detailAddress === '' ? that.addressStatus = false : that.addressStatus = true
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
  .save-btn-true{
    color:rgba(255,255,255,1)!important;
    background:rgba(34,34,34,1)!important;
  }
</style>
