<template>
  <div>

    <div>
      <mt-cell label="选中一名学生后将自动退选其他学生，课程即互选完成。"></mt-cell>
      <div v-if="isHasOne">
        <mt-radio title="学生列表" v-model="finalStudent" :options="studentList">
        </mt-radio>
        <mt-button type="primary" size="large" @click="confirm" class="confirm">
          确认
        </mt-button>
      </div>
      <div v-else style="text-align: center">
        <h2>当前并无学生选中该课程</h2>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    name: "select-student",
    data() {
      return {
        finalStudent: '',
        studentList: [],
        isHasOne: false
      }
    },

    methods: {
      getStudentList() {
        this.$http.get("detail/student-list", {
          params: {
            id: this.$route.params.courseId,
          }
        }).then((res) => {
          // 校验，若无学生则为空
          if (res.data.data.length == 0) {
            return
          }
          var list = []
          res.data.data.forEach((item, index) => {
            list[index] = {
              label: item.job_num + " " + item.student_name,
              value: item.student_id
            }
          })
          this.finalStudent = list[0].value
          this.studentList = list
          this.isHasOne = true;
        })
      },
      confirm() {
        util.box.confirm("确定选中该名学生，退选其他学生？").then(action => {
          this.$http.get("detail/select-student", {
            params: {
              id: this.$route.params.courseId,
              student_id: this.finalStudent
            }
          }).then(res => {
            setTimeout(() => {
              this.$router.push({
                name: "details",
                params: {
                  "0": "teacher",
                  courseId: this.$route.params.courseId
                }
              })
            }, 1600)

          })

        }, action => {
          util.toast("您已取消操作")
        })
      }

    },
    created() {
      this.getStudentList()

    }

  }

</script>
<style scoped>
  .confirm {
    margin-top: 40px;
  }
</style>
