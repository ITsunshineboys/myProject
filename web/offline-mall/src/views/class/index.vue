<template>
  <div class="class-list">
    <header-search pop="true" search="true"></header-search>
    <div class="content">
      <div class="content-left" :class="{'pop-open': isPopOpen}">
        <div v-for="obj in classData" @click="selectClass(obj.id)" :class="{'active': obj.id === isSelectClass}">
          <a href="javascript: void (0);">{{obj.title}}</a>
        </div>
      </div>
      <div class="content-right" :class="{'pop-open': isPopOpen}">
        <div class="cateList" v-for="obj in secondClass">
          <div class="hd">
            <span class="text">{{obj.title}}</span>
          </div>
          <ul class="list">
            <li class="cate-item" v-for="item in obj.children">
              <router-link :to="'/goods-list/' + item.id">
                <div class="cate-img-wrapper">
                  <img :src="item.icon">
                </div>
                <div class="name">{{item.title}}</div>
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import HeaderSearch from '@/components/HeaderSearch'

  export default {
    components: {
      HeaderSearch
    },
    data () {
      return {
        isPopOpen: false,
        isSelectClass: null,
        classData: [],
        secondClass: []
      }
    },
    created () {
      this.isSelectClass = this.$route.params.id
      this.axios.get('/mall/categories-with-children', '', response => {
        console.log(response)
        let data = response.data.categories
        this.classData = data
        for (let obj of data) {
          if (obj.id === this.$route.params.id) {
            this.secondClass = obj.children
          }
        }
      })
    },
    methods: {
      isShow (bool) {
        this.isPopOpen = bool
      },
      selectClass (id) {
        this.isSelectClass = id
        for (let obj of this.classData) {
          if (id === obj.id) {
            this.secondClass = obj.children
          }
        }
      }
    }
  }
</script>

<style>
  .content {
    position: absolute;
    top: 56px;
    bottom: 0;
    display: flex;
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
    /*margin-right: 24px;*/
    margin-right: 10%;
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

  @media screen and (max-width: 320px)  {
    /*.cate-item {
      margin-right: 10%
    }*/

    .cate-item:nth-child(3n) {
      margin-right: 10%;
    }
  }
</style>
