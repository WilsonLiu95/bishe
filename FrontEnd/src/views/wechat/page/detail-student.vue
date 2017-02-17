<template>
  <div >
    <mt-header title="学生信息">
    </mt-header>
    <!--学生列表-->
    <div v-if="userType=='student'">
      <mt-field label="院系" v-model="account.institute" placeholder="院系" disabled></mt-field>

      <mt-field label="姓名" v-model="account.name" placeholder="请输入用户名" disabled></mt-field>
      <mt-field label="学号" v-model="account.job_num" placeholder="请输入学号" disabled></mt-field>
      <mt-field label="手机号" placeholder="请输入手机号" type="tel" v-model="account.phone" :disabled=isDiabled></mt-field>
      <mt-field label="QQ" placeholder="请输入QQ号" type="tel" v-model="account.qq" :disabled=isDiabled></mt-field>
      <mt-field label="邮箱" placeholder="请输入邮箱" type="email" v-model="account.email" :disabled=isDiabled></mt-field>

      <mt-field label="自我介绍" placeholder="介绍一下你自己吧~ 让老师了解你" type="textarea" v-model="account.intro" rows="5" :disabled=isDiabled></mt-field>


    </div>

  </div>
</template>
<script>
  export default {
    name: "detail-student",
    data() {
      return {

        userType: window.util.getUserType(),
        detail: {},
      }
    },

    methods: {
      getDetail(){

      }
    },
    created() {
    }

  }



</script>
<style>

</style>
