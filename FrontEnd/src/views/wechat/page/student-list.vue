<template>
  <div class="details-page">
    <div>
      <mt-cell  title="姓名"  style="background-color: #26a2ff;">
        <span style="color:#000">学号</span>
      </mt-cell>
      <!--学生的链接使用index是为了-->
      <mt-cell v-for="(student,index) in studentList" :title="student.student_name" :to="'/teacher/detail/student/'+index" is-link :value="student.job_num">
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
