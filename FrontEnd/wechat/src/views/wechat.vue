<template>
  <div class="wechat-page">
    <h1>微信授权</h1>
  </div>


</template>
<script>
  export default {
    name: "wechat",
    data() {
      return {
      }
    },
    created() {
      var code = location.search.split("&")[0].split("=")[1];
      // debugger
      if (code) {
        this.$http.get("wechat?code=" + code).then(function (res) {
          if (res.state == 301) {
            _const.isTeacher = res.data.isTeacher
          }
        }).catch(function (error) {
          console.log(error);
        })
      }
    },

  };

</script>
<style>
  .wechat-page {
    text-align: center;
  }
</style>
