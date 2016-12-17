<template>
  <div id="app">
    <!--对应三栏tab的页面-->
    <mt-tab-container v-model="selected" :swipeable=true>
      <mt-tab-container-item id="课题">
        <classPage></classPage>
      </mt-tab-container-item>
      <mt-tab-container-item id="进度">
        <schedulePage></schedulePage>
      </mt-tab-container-item>
      <mt-tab-container-item id="我的">
        <selfPage></selfPage>
      </mt-tab-container-item>
    </mt-tab-container>

<!--地步三栏tab-->
    <mt-tabbar v-model="selected" :fixed=true>
      <mt-tab-item id="课题">
        <img slot="icon" src="./assets/class.svg"> 课题
      </mt-tab-item>
      <mt-tab-item id="进度">
        <img slot="icon" src="./assets/schedule.svg"> 进度
      </mt-tab-item>
      <mt-tab-item id="我的">
        <img slot="icon" src="./assets/account.svg"> 我的
      </mt-tab-item>
    </mt-tabbar>
  </div>
</template>

<script>

import classPage from '_views/class'
import schedulePage from '_views/schedule'
import selfPage from '_views/self'

  export default {
    name: 'app',
    data() {
      return { selected: "我的" }
    },
    components: {
      classPage,schedulePage,selfPage
    },
    computed: {
    },
    methods: {
    }
  };
</script>

<style>
html, body {
  height: 100%;
}
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}
</style>
