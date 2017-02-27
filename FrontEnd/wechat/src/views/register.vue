<template>
  <div class="register-page">
    <h5 style="text-align: center;">请如实填写个人信息，填写后不允许修改</h5>
    <div>
      <mt-field label="姓名" placeholder="请输入姓名" :state="data.name ? 'success' : 'error'" v-model="data.name"></mt-field>
      <mt-field label="学号" placeholder="请输入学号" :state="data.job_num ? 'success' : 'error'" v-model="data.job_num"></mt-field>
      <mt-field label="电话" placeholder="请输入电话" :state="isPhone ? 'success' : 'error'" v-model="data.phone"></mt-field>
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
          name: "测试账号",
          job_num: "44",
          phone: "18571635614",
        }
      }
    },
    created(){
      this.$http.get('register/is-login') // 判断是否已经登录，登录过则自动跳转
    },
    computed: {
      isPhone() {
        return util.is('isMobilePhone', this.data.phone, 'zh-CN')
      },
    },
    methods: {
      register() {
        if (this.data.name && this.data.job_num && this.isPhone) {
          this.$http.post("register", this.data)
        }else{
          util.toast("数据错误，请正确填写")
        }

      },

    },

  };

</script>
