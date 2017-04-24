<template>
  <div class="tab-page-container">
    
    <!--系统导航栏-->
    <el-menu theme="dark" class="menu" mode="horizontal" @select="handleSelect" :router="true">
      <el-menu-item index="./home">华科毕设选题管理系统</el-menu-item>
      <el-submenu index="./home">
        <template slot="title">管理</template>
        <el-menu-item index="./student">管理学生</el-menu-item>
        <el-menu-item index="./course">管理课题</el-menu-item>
      </el-submenu>
      <el-menu-item index=".//system-config">系统配置管理</el-menu-item>
    </el-menu>
    <router-view></router-view>
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
