<template>
  <div>
    <div>
      <mt-field label="院系" v-model="account.institute" placeholder="院系" readonly></mt-field>

      <mt-field label="姓名" v-model="account.name" placeholder="请输入用户名" readonly></mt-field>
      <mt-field label="学号" v-model="account.job_num" placeholder="请输入学号" readonly></mt-field>
      <mt-field label="手机号" placeholder="请输入手机号" type="tel" v-model="account.phone" readonly>
        <mt-button class="inline-filed-btn" size="small" @click="concact">
          联系
        </mt-button>
      </mt-field>

      <mt-field label="QQ" placeholder="请输入QQ号" type="tel" v-model="account.qq" readonly></mt-field>
      <mt-field label="邮箱" placeholder="请输入邮箱" type="email" v-model="account.email" readonly></mt-field>

      <mt-field label="自我介绍" placeholder="介绍一下你自己吧~ 让老师了解你" type="textarea" v-model="account.intro" rows="5" readonly></mt-field>
    </div>
  </div>
</template>
<script>
  export default {
    name: "student-info",
    data() {
      return {
        account: {},
      }
    },

    methods: {
      getAccount() {
        // 请求数据
        this.$http.get("account/info", {
          params: {
            id: this.$route.params.courseId,
            type: "student",
            index: this.$route.params.index
          }
        }).then((res) => {
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
<style  scoped>
  .mint-cell-wrapper button {
    margin: 7px 0 0 0;
  }
</style>
