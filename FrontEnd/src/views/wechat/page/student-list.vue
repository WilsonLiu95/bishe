<template>
  <div class="details-page">
    <h1>student-list</h1>
    <div>
      <mt-cell v-for="student in studentList" :title="student.student_name" to="'/teacher/detail/student/'+student.id" is-link :value="student.job_num">
      </mt-cell>
    </div>
  </div>
</template>
<script>
  // 课程 状态包含 0:已删除,1:待审核,2:互选中,3:互选完成
  // schedule 状态包含 0:为选定后退选课程,1:为学生选定该课程，2:为互选成功，
  export default {
    name: "student-list",
    data() {
      return {
        userType: util.getUserType(),
        studentList: {},
      }
    },
    created() {
      this.getStudentList()
    },
    computed: {
    },
    methods: {
      getStudentList() {
        this.$http.get("detail/student-list", {
          params: {
            id: util.hashArr(3),
          }
        }).then((res) => {
          this.studentList = res.data
        })
      }
    }
  };

</script>
<style>

</style>
