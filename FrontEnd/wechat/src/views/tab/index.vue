<template>
  <div class="index-page">
    <!--路由-->
    <router-view class="second-router">
    </router-view>
    <!--四栏tab-->
    <mt-tabbar v-model="selected" :fixed=true>
      <mt-tab-item id="course">
        <img slot="icon" :src="assets.class"> <span>课题</span>
      </mt-tab-item>
      <mt-tab-item id="schedule">
        <img slot="icon" :src="assets.schedule"> <span>进度</span>
      </mt-tab-item>
      <mt-tab-item id="message">
        <img slot="icon" :src="assets.message">
        <span>消息</span>
        <mt-badge v-if="unreadMsgNum" class="msg-notify" size="normal" type="error">{{unreadMsgNum}}</mt-badge>
      </mt-tab-item>
      <mt-tab-item id="account">
        <img slot="icon" :src="assets.account"> <span>我的</span>
      </mt-tab-item>
    </mt-tabbar>
  </div>
</template>

<script>
  export default {
    name: "index",
    data() {
      return {
        // 引入资源
        assets: {
          class: require("assets/class.svg"),
          schedule: require("assets/schedule.svg"),
          message: require("assets/message.svg"),
          account: require("assets/account.svg"),
        },
        selected: "course", // 默认课程页面
        isGetNotify: false,
        unreadMsgNum: 1,
      }
    },
    watch: {
      selected: function (selected) {
        this.$router.push("./" + selected) // 改变hash是为了重载该tab的组件，同时其他组件由于没有匹配路由规则被销毁
      }
    },
    created() {
      // 创建的时候监听路由变化，以编程方式响应跳转到相应的页面
      var hashArr = location.hash.split("/")
      this.selected = hashArr[2]
      this.getUnreadMsgNum()
    },
    computed: {
    },
    methods: {
      getUnreadMsgNum(){

        this.$http.get('message/unread-number').then((res)=>{
          this.unreadMsgNum = res.data.data
        })
      }
    },

  };

</script>

<style scoped>
  .tab-page-container {
    padding-bottom: 50px;
  }

  .msg-notify {
    position: absolute;
    top: 5px;
  }
</style>
