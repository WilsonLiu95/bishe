<template>
  <div class="tab-page-container">
    <mt-header title="我的信息">
      <mt-button v-if="isDiabled" slot="right" @click="modifyAccount" size="small">修改</mt-button>
      <mt-button v-if="!isDiabled" slot="right" @click="saveAccount" size="small">保存</mt-button>
    </mt-header>
    <!--学生列表-->
    <div>
      <mt-field label="院系" v-model="account.institute" placeholder="院系" disabled></mt-field>
      <mt-field label="姓名" v-model="account.name" placeholder="请输入用户名" disabled></mt-field>
      <mt-field label="工号" v-model="account.job_num" placeholder="请输入工号" disabled></mt-field>

      <mt-field label="手机号" placeholder="请输入手机号" type="tel" v-model="account.phone" :state="getState('isPhone')" :disabled=isDiabled></mt-field>
      <mt-field label="QQ" placeholder="请输入QQ号" type="tel" v-model="account.qq" :state="getState('isQQ')" :disabled=isDiabled></mt-field>
      <mt-field label="邮箱" placeholder="请输入邮箱" type="email" v-model="account.email" :state="getState('isEmail')" :disabled=isDiabled></mt-field>

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
        account: {},
      }
    },
    computed: {
      isEmail() {
        return util.is('isEmail', this.account.email)
      },
      isPhone() {
        return util.is('isMobilePhone', this.account.phone, 'zh-CN')
      },
      isQQ() {
        return /^[1-9][0-9]{4,}$/.test(this.account.qq)
      },
    },
    methods: {
      getState(value) {
        if (this.isDiabled) {
          return // 不允许更改时，不显示state
        } else {
          return this[value] ? 'success' : 'error'
        }
      },
      getAccount() {
        this.$http.get("account").then((res) => {
          this.account = res.data.data
        })
      },
      modifyAccount() {
        this.isDiabled = false // 使得表单部分属性可以修改
      },
      saveAccount() {
        if (this.isQQ && this.isEmail && this.isPhone) {
          // 保存修改，post数据到线上
          this.$http.post("account/modify", this.account).then((res) => {
            this.isDiabled = true
          })
        }else{
          return util.toast("请正确填写数据")
        }

      },
    },
    created() {
      this.getAccount();
    }

  }

</script>
<style>

</style>
