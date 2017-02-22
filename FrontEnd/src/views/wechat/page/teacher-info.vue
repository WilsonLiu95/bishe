<template>
  <div>
    <!--<mt-header title="导师信息">    </mt-header>-->
    <!--学生列表-->
    <div>
      <mt-field label="院系" v-model="account.institute" placeholder="院系" readonly></mt-field>
      <mt-field label="姓名" v-model="account.name" placeholder="请输入用户名" readonly></mt-field>
      <mt-field label="工号" v-model="account.job_num" placeholder="请输入工号" readonly></mt-field>
      <mt-field label="手机号" placeholder="请输入手机号" type="tel" v-model="account.phone" readonly>
        <mt-button style="margin: 4px" @click="concact">
          联系
        </mt-button>
      </mt-field>
      <mt-field label="QQ" placeholder="请输入QQ号" type="tel" v-model="account.qq" readonly></mt-field>
      <mt-field label="邮箱" placeholder="请输入邮箱" type="email" v-model="account.email" readonly></mt-field>
      <mt-field label="自我介绍" placeholder="介绍一下你自己吧~ 让老师了解你" type="textarea" v-model="account.intro" readonly rows="5"></mt-field>
    </div>
  </div>
</template>
<script>
  export default {
    name: "teacher-info",
    data() {
      return {
        userType: window.util.getUserType(),
        account: {},
      }
    },

    methods: {
      getAccount() {
        // 请求数据
        this.$http.get("detail/teacher-info?id=" + this.$route.params.courseId).then((res) => {
          this.account = res.data.data
        })
      },
      concact() {
        location.href = "tel:" + this.account.phone
      }
    },
    created() {
      this.getAccount();
    }

  }

</script>
<style>

</style>
