<template>
  <div class="register-page">
    <h5 style="text-align: center;">请如实填写个人信息，填写后不允许修改</h5>
<div>
  <mt-field label="姓名" placeholder="请输入姓名" v-model="data.name"></mt-field>
  <mt-field label="学号" placeholder="请输入学号" v-model="data.job_num"></mt-field>
  <mt-field label="电话" placeholder="请输入电话" v-model="data.phone"></mt-field>
  <mt-button size="large" type="primary" @click="register">确认</mt-button>
</div>


</div>
</template>
<script>
  export default {
    name: "register",
    data() {
      return {
        data: {
          name: "Prof. Norval Hansen Sr.",
          job_num: "576",
          phone: "12211",
        }
      }
    },
    watch: {

    },
    computed: {
    },
    methods: {
      register() {
        this.$http.post("register", this.$data.data).then(function (res) {
          // debugger
          console.log(res)
        }).catch(function (error) {
          debugger
          console.log(error);
        })
      }
    },

  };

</script>
