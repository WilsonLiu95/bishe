<template>
  <div class="tab-page-container">
    <mt-header title="我的信息">
      <mt-button v-if="isDiabled" slot="right" @click="modifyAccount" type="danger" size="small">修改</mt-button>
      <mt-button v-if="!isDiabled" slot="right" @click="saveAccount" type="primary" size="small">保存</mt-button>
    </mt-header>

    <!--学生列表-->
    <div v-if="userType=='student'">
      <mt-field label="学校" v-model="account.school" placeholder="学校" disabled></mt-field>
      <mt-field label="院系" v-model="account.institute" placeholder="院系" disabled></mt-field>
      <mt-field label="专业" v-model="account.major" placeholder="专业" disabled></mt-field>
      <mt-field label="专业方向" v-model="account.direction" placeholder="专业方向" disabled></mt-field>

      <mt-field label="姓名" v-model="account.name" placeholder="请输入用户名" disabled></mt-field>
      <mt-field label="学号" v-model="account.student_no" placeholder="请输入学号" disabled></mt-field>
      <mt-field label="手机号" placeholder="请输入手机号" type="tel" v-model="account.phone" :disabled=isDiabled></mt-field>
      <mt-field label="邮箱" placeholder="请输入邮箱" type="email" v-model="account.email" :disabled=isDiabled></mt-field>

      <mt-field label="自我介绍" placeholder="介绍一下你自己吧~ 让老师了解你" type="textarea" v-model="account.intro" rows="5" :disabled=isDiabled></mt-field>
    </div>
    <!--老师列表-->
    <div v-else-if="userType == 'teacher'">
      <mt-field label="学校" v-model="account.school" placeholder="学校" disabled></mt-field>
      <mt-field label="院系" v-model="account.institute" placeholder="院系" disabled></mt-field>
      <mt-field label="职称" v-model="account.level" placeholder="职称" disabled></mt-field>
      <mt-field label="专业方向" v-model="account.direction" placeholder="专业方向" disabled></mt-field>

      <mt-field label="姓名" v-model="account.name" placeholder="请输入用户名" disabled></mt-field>
      <mt-field label="工号" v-model="account.teacher_no" placeholder="请输入学号" disabled></mt-field>
      <mt-field label="手机号" placeholder="请输入手机号" type="tel" v-model="account.phone" :disabled=isDiabled></mt-field>
      <mt-field label="邮箱" placeholder="请输入邮箱" type="email" v-model="account.email" :disabled=isDiabled></mt-field>

      <mt-field label="自我介绍" placeholder="介绍一下你自己吧~ 让老师了解你" type="textarea" v-model="account.intro" rows="5" :disabled=isDiabled></mt-field>
    </div>


  </div>
</template>
<script>

  export default {
    name: "account-page",
    data() {
      return {
        isDiabled: true,
        userType: window._const.userType,
        account: {},
      }
    },
    methods: {
      modifyAccount() {
        this.isDiabled = false // 使得表单部分属性可以修改
      },
      saveAccount() {
        // 保存修改，post数据到线上
        this.isDiabled = true

      },
    },
    created(){
      if(this.userType) {
        this.account = window[this.userType].account // 获取伪造的数据
      }

    }

  }
</script>
<style>

</style>
