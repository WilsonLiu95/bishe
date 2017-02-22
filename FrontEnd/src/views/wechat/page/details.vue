<template>
  <div class="details-page">

    <!--表单部分-->
    <div class="part-one course-form">
      <mt-field label="课程" placeholder="课程名称" v-model="course.title" :readonly="!course.isowner || isDiabled" ></mt-field>
      <mt-field label="导师" placeholder="导师姓名" v-model="course.teacher" readonly >
        <mt-button @click="jumpTeacherInfo" style="margin: 4px">查看</mt-button>
      </mt-field>
      <mt-field label="电话" placeholder="导师电话" v-model="course.teacher_phone" readonly>

        <mt-button style="margin: 4px" @click="concact">
          联系
        </mt-button>
      </mt-field>
      <mt-field label="详情" placeholder="课题详情" type="textarea" rows="8" v-model="course.details" :readonly="!course.isowner || isDiabled" ></mt-field>
    </div>

    <!--根据用户以及课程的status展示不同的部分-->
    <div class="part-two">
      <!--已删除-->
      <div v-if="course.status == 0">
        <mt-button size="large" class="details-notify-btn" type="default">课程已被删除</mt-button>
      </div>
      <!--课程审核中-->
      <div v-else-if="course.status == 1">
        <mt-button size="large" class="details-notify-btn" type="default">课程审核中</mt-button>
      </div>
      <!--未删除-->
      <div v-else>
        <!--老师界面-->
        <div v-if="userType == 'teacher'" class="details-notify">
          <!-- 课程进度为互选中-->
          <div v-if="course.status == 2">
            <mt-cell title="进度" :value="'互选中,已有' + course.student_num+'人选定该课程'"></mt-cell>
            <mt-cell title="名单" :label="course.student_list">
              <mt-button v-if="course.isowner && course.student_num " @click="jumpStudentList">
                查看
              </mt-button>
            </mt-cell>
            <div v-if="course.isowner" style="float: right;margin-top:10px">
              <mt-button type="primary" v-if="isDiabled" @click="modifyCourse" size="normal">修改</mt-button>
              <mt-button type="primary" v-if="!isDiabled" @click="saveCourse" size="normal">保存</mt-button>
              <mt-button type="primary" v-if="course.student_num" class="details-notify-btn" @click="jumpSelectStudent" size="normal">选定学生</mt-button>
              <mt-button type="danger" class="details-notify-btn" size="normal" @click="deleteCourse">删除</mt-button>
            </div>
          </div>
          <!--课程状态为已互选-->
          <div v-if="course.status == 3">
            <mt-cell title="进度" value="已完成互选"></mt-cell>
            <mt-cell title="互选学生" :value="course.student_list"></mt-cell>
            <mt-button type="danger" class="details-notify-btn" size="large" @click="deleteStudent">退选已互选的学生</mt-button>
          </div>
        </div>

        <!-- 学生界面-->
        <div v-if="userType == 'student'" class="details-notify">
          <!-- 课程进度为互选中-->
          <div v-if="course.status == 2">
            <mt-cell title="进度" :value="'互选中,已有' + course.student_num+'人选定该课程'"></mt-cell>
            <mt-cell title="名单" :label="course.student_list"></mt-cell>
            <div v-if="course.isSelected">
              <mt-cell title="提示">已选定，请主动联系老师，完成互选</mt-cell>
              <mt-button size="large" class="details-notify-btn" type="danger" @click="cancelSelect">退选</mt-button>
            </div>

            <mt-button v-else="course.isSelected" size="large" class="details-notify-btn" type="primary" @click="select">选定</mt-button>
          </div>
          <!--课程状态为已互选-->
          <div v-if="course.status == 3">
            <mt-cell title="进度" value="已完成互选"></mt-cell>
            <mt-cell title="互选学生" :value="course.student_list"></mt-cell>
          </div>

        </div>
      </div>
    </div>

  </div>
</template>
<script>
  // 课程 状态包含 0:已删除,1:待审核,2:互选中,3:互选完成
  // schedule 状态包含 0:为选定后退选课程,1:为学生选定该课程，2:为互选成功，
  export default {
    name: "details",
    data() {
      return {
        isDiabled: true,
        userType: util.getUserType(),
        course: {}
      }
    },
    created() {
      this.getDetail();
    },
    computed: {
    },
    methods: {
      getDetail() {
        // 请求数据
        this.$http.get("detail?id=" + this.$route.params.courseId).then((res) => {
          this.course = res.data.data
        })
      },
      select() {
        this.$http.get("detail/select?id=" + this.$route.params.courseId).then((res) => {
          this.getDetail();
          util.toast({
            message: res.data.msg,
            duration: 1500
          });
        })

      },
      modifyCourse() {
        this.isDiabled = false // 使得表单部分属性可以修改
      },
      saveCourse() {
        // 保存修改，post数据到线上
        var data = {
          "id": this.$route.params.courseId,
          "title": this.course.title,
          "details": this.course.details,
        }
        this.$http.post("detail/modify", data).then((res) => {
          this.isDiabled = true
        })
      },
      deleteCourse() {
        util.box.confirm('确定删除此课程?').then(action => {
          this.$http.get("detail/delete?id=" + this.$route.params.courseId).then((res) => {
            this.getDetail()
          })
        }, action => {
          // 取消删除
          util.toast({
            message: "已取消删除操作"
          });
        })

      },
      jumpStudentList() {
        this.$router.push({
          name: "student-list",
          params: {
            courseId: this.$route.params.courseId
          }
        })
      },
      jumpSelectStudent() {
        this.$router.push({
          name: "select-student",
          params: {
            courseId: this.$route.params.courseId
          }
        })
      },
      jumpTeacherInfo() {
        // debugger
        this.$router.push({
          name: "teacher-info",
          params: {
            "0": this.$route.params[0],
            courseId: this.$route.params.courseId
          }
        })
      },
      concact() {
        location.href = "tel:" + this.course.teacher_phone
      },
      deleteStudent() {
        util.box.confirm('确定退选该学生?退选后课程将重新回到“互选中”。').then(action => {
          this.$http.get("detail/delete-student?id=" + this.$route.params.courseId).then((res) => {
            this.getDetail()
          })
        }, action => {
          // 取消删除
          util.toast({
            message: "已取消删除操作"
          });
        })
      },
      cancelSelect() {
        util.box.confirm('确定退选该学生?退选后课程将重新回到“互选中”。').then(action => {
          this.$http.get("detail/cancel-select?id=" + this.$route.params.courseId).then((res) => {
            this.getDetail()
          })
        }, action => {
          // 取消删除
          util.toast({
            message: "已取消删除操作"
          });
        })
      }
    },

  };

</script>
<style>
  .details-notify-btn {
    margin-top: 10px;
  }
</style>
