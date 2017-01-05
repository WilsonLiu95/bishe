<template>
  <div class="tab-page-container">

    <el-menu theme="dark"  class="el-menu-demo" mode="horizontal" @select="handleSelect" :router="true">
      <el-menu-item index=".">华科毕设管理系统</el-menu-item>
      <el-menu-item index="./deal">统计</el-menu-item>
      <el-menu-item index="./export">导出</el-menu-item>
    </el-menu>
    <router-view></router-view>
    <!--<div class="line"></div>-->

  </div>
</template>
<script>

  export default {
    name: "admin-page",
    data() {
      return {
        active: "export"
      }
    },
    methods: {
      handleSelect(key, keyPath) {
        console.log(key, keyPath);
      },
    }
  }

</script>
<style>

</style>
