<template>
  <div class="tab-page-container">
    
    <!--系统导航栏-->
    <el-menu theme="dark" class="menu" mode="horizontal" @select="handleSelect" :router="true">
      <el-menu-item index=".">华科毕设选题管理系统</el-menu-item>
      <el-menu-item index="./manage">管理</el-menu-item>
      <el-menu-item index="./logout">退出</el-menu-item>
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
