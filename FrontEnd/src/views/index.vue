<template>
  <div class="index-page">
    <!--路由-->
    <router-view>
    </router-view>
    <!--四栏tab-->
    <mt-tabbar v-model="selected" :fixed=true>
      <mt-tab-item id="course">
        <img slot="icon" src="../assets/class.svg"> <span>课题</span>
      </mt-tab-item>
      <mt-tab-item id="schedule">
        <img slot="icon" src="../assets/schedule.svg"> <span>进度</span>
      </mt-tab-item>
      <mt-tab-item id="message">
        <img slot="icon" src="../assets/message.svg">
        <span>消息</span>
        <mt-badge class="msg-notify" size="normal" type="error">2</mt-badge>
      </mt-tab-item>
      <mt-tab-item id="account">
        <img slot="icon" src="../assets/account.svg"> <span>我的</span>
      </mt-tab-item>
    </mt-tabbar>
  </div>
</template>

<script>

  export default {
    name: "index",
    data() {
      return {
        selected: "course", // 默认课程页面
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
    },
    computed: {
    },
    methods: {
    },

  };

</script>

<style>
.tab-item {
  margin-bottom: 65px;
}
.msg-notify {
  position: absolute;
  top: 5px;
  left: 55%;
}
.message {
  position: relative;
}

</style>
