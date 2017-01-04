<template>
<h1>微信授权</h1>
</div>


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
        this.$http.get("/login?code=" + code).then(function (res) {
          console.log(res)
        }).catch(function (error) {
          console.log(error);
        })
      }
    },

  };

</script>
