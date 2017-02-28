<template>
  <div class="details-page">
    <mt-cell title="姓名" style="background-color: #26a2ff;">
      <span style="color:#000">学号</span>
    </mt-cell>
    <div v-if="studentList.length">
      <!--学生的链接使用index是为了-->
      <mt-cell v-for="(student,index) in studentList" :title="student.student_name" :to="'/details/'+ $route.params.courseId +'/student-info/'+index"
        is-link :value="student.job_num">
        </mt-cell>
    </div>
    <div v-else style="text-align: center">
      <h2>当前并无学生选中该课程</h2>
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
            id: this.$route.params.courseId,
          }
        }).then((res) => {
          this.studentList = res.data.data
        })
      }
    }
  };

</script>
<style>

</style>
