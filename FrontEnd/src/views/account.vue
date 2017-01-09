<template>
  <div class="tab-page-container">
    <mt-header title="我的信息">
      <mt-button v-if="isDiabled" slot="right" @click="modifyAccount" type="danger" size="small">修改</mt-button>
      <mt-button v-if="!isDiabled" slot="right" @click="saveAccount" type="primary" size="small">保存</mt-button>
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
    <!--老师列表-->
    <div v-else-if="userType == 'teacher'">
      <mt-field label="院系" v-model="account.institute" placeholder="院系" disabled></mt-field>
      <mt-field label="姓名" v-model="account.name" placeholder="请输入用户名" disabled></mt-field>
      <mt-field label="工号" v-model="account.job_num" placeholder="请输入工号" disabled></mt-field>
      <mt-field label="手机号" placeholder="请输入手机号" type="tel" v-model="account.phone" :disabled=isDiabled></mt-field>
      <mt-field label="QQ" placeholder="请输入QQ号" type="tel" v-model="account.qq" :disabled=isDiabled></mt-field>
      <mt-field label="邮箱" placeholder="请输入邮箱" type="email" v-model="account.email" :disabled=isDiabled></mt-field>
      <mt-field label="自我介绍" placeholder="介绍一下你自己吧~ 让老师了解你" type="textarea" v-model="account.intro" rows="5" :disabled=isDiabled></mt-field>
    </div>
  </div>
</template>
<script>
  import { Toast } from 'mint-ui';
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
        var that = this
        // 保存修改，post数据到线上
        this.$http.post("account/modify", this.account).then(function (res) {
          ({
            message: res.data.msg,
            iconClass: 'mintui mintui-success'
          });
          that.isDiabled = true
        })
      },
    },
    created() {
      var that = this
      this.$http.get("account").then(function (res) {
        that.account = res.data
      })
    }

  }



</script>
<style>

</style>
