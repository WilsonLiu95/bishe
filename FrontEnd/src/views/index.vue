<template>
  <div class="index-page">
    <!--对应四栏tab的页面-->
    <mt-tab-container v-model="selected" :swipeable=false class="tab-item-container">
      <mt-tab-container-item id="course" class="tab-item">
        <router-view name="course">
        </router-view>
      </mt-tab-container-item class="tab-item">
      <mt-tab-container-item id="schedule" class="tab-item">
        <router-view name="schedule">
        </router-view>
      </mt-tab-container-item>
      <mt-tab-container-item id="message" class="tab-item">
        <router-view name="message">
        </router-view>
      </mt-tab-container-item>
      <mt-tab-container-item id="account" class="tab-item">
        <router-view name="account">
        </router-view>
      </mt-tab-container-item>
    </mt-tab-container>

    <!--四栏tab-->
    <mt-tabbar v-model="selected" :fixed=true>
      <mt-tab-item id="course">
        <img slot="icon" src="../assets/class.svg"> 课题
      </mt-tab-item>
      <mt-tab-item id="schedule">
        <img slot="icon" src="../assets/schedule.svg"> 进度
      </mt-tab-item>
      <mt-tab-item id="message">
        <img slot="icon" src="../assets/message.svg"> 消息
      </mt-tab-item>
      <mt-tab-item id="account">
        <img slot="icon" src="../assets/account.svg"> 我的
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
      window._const.userType = hashArr[1]
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



</style>
