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
      if (code) {
        this.$http.get("wechat?code=" + code).then(function (res) {
          console.log(res)
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
