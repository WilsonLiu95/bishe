<template>
  <div class="tab-page-container">
  <h2>测试系统配置页面</h2>
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
