<template>
  <div class="details-page">
    <!--第一部分 start 基本课程信息-->
    <div class="detail-section">
      <mt-field label="课程" placeholder="课程名称" v-model="course.title" :disabled="!course.isowner || isDiabled"></mt-field>
      <mt-field label="导师" placeholder="导师姓名" v-model="course.teacher" disabled>
        <mt-button @click="jumpTeacherInfo" size="small" class="inline-filed-btn">查看</mt-button>
      </mt-field>
      <mt-field label="电话" placeholder="导师电话" v-model="course.teacher_phone" disabled>
        <mt-button class="inline-filed-btn" size="small" @click="concact">
          联系
        </mt-button>
      </mt-field>
      <mt-field label="详情" placeholder="课题详情" type="textarea" rows="8" v-model="course.details" :disabled="!course.isowner || isDiabled"></mt-field>
    </div>
    <!--第一部分 end 基本课程信息-->
    <!--第二部分 start 审核结果 与 当前进度-->
    <div v-if="course.isowner || course.isadmin" class="detail-section">
      <!--管理员和课题的主人都可以看到审核情况-->
      <!--不允许修改-->
      <div v-if="isDiabledCheck">
        <mt-cell title="审核进度">{{["待审核","未通过审核","已通过"][course.check_status]}}</mt-cell>
        <mt-field label="审核意见" placeholder="有待完善" type="textarea" rows="4" v-model="course.check_advice" disabled></mt-field>
      </div>
      <!--允许修改-->
      <div v-else>
        <mt-cell title="通过结果" v-if="!isDiabledCheck && course.isadmin">
          <mt-switch v-model="check.is_pass">{{check.is_pass?'通过':'不通过'}}</mt-switch>
        </mt-cell>
        <mt-field label="审核意见" placeholder="有待完善" type="textarea" rows="4" v-if="!isDiabledCheck && course.isadmin" v-model="check.check_advice"></mt-field>
      </div>
      <div class="div-schedule" v-if="course.status ==2 || course.status ==3">
        <mt-cell title="进度" :value="course.status ==2?('互选中,已有' + course.student_num+'人选定该课程'): '完成互选'"></mt-cell>
        <mt-cell title="名单" :label="course.student_list">
          <mt-button v-if="course.isowner && course.student_num " size="small" @click="jumpStudentList">
            查看
          </mt-button>
        </mt-cell>
      </div>

    </div>
    <!--第二部分 end 审核结果-->

    <!--第三部分 start根据用户以及课程的status展示不同的部分-->
    <div class="detail-section">
      <!--已删除-->
      <div v-if="course.status == 0">
        <mt-button size="large" type="default">课程已被删除</mt-button>
      </div>
      <!--课程审核中-->
      <div v-if="course.status == 1">
        <mt-button v-if="!course.isowner && !course.isadmin" size="large">课程审核中</mt-button>
        <div v-if="!course.isowner && course.isadmin">
          <mt-button v-if="isDiabledCheck" @click="isDiabledCheck=false" size="large" type="primary">开始审核</mt-button>
          <mt-button v-if="!isDiabledCheck" @click="submitCheck" size="large" type="primary">提交审核</mt-button>
        </div>


      </div>
      <!--互选与已完成-->
      <div v-if="course.status == 2 && userType == 'student'">
        <div v-if="course.isSelected">
          <mt-cell title="提示">已选定，请主动联系老师，完成互选</mt-cell>
          <mt-button size="large" type="danger" @click="cancelSelect">退选</mt-button>
        </div>
        <mt-button v-else="course.isSelected" size="large" type="primary" @click="select">选定</mt-button>
      </div>
    </div>
    <!--第三部分 end 根据用户以及课程的status展示不同的部分-->
    <!--第四部分 start 操作课程的按键组 如果是课程主人-->
    <div class="detail-section group-btn-right" v-if="course.isowner && course.status != 0">
      <mt-button type="primary" v-if="isDiabled" @click="modifyCourse" size="normal">修改</mt-button>
      <mt-button type="primary" v-if="!isDiabled" @click="saveCourse" size="normal">保存</mt-button>

      <!--同时是课题主人和管理员 才显示的审核的按钮-->
      <mt-button v-if="isDiabledCheck && course.status == 1 &&  course.isowner && course.isadmin" @click="isDiabledCheck=false" size="normal"
        type="primary">开始审核</mt-button>
        <mt-button v-if="!isDiabledCheck && course.status == 1 && course.isowner && course.isadmin" @click="submitCheck" size="normal"
          type="danger">提交审核</mt-button>

          <mt-button type="primary" v-if="course.student_num && course.status == 2" @click="jumpSelectStudent" size="normal">选定</mt-button>
          <mt-button type="danger" v-if="course.status == 3" size="normal" @click="deleteStudent">退选</mt-button>
          <mt-button type="danger" size="normal" @click="deleteCourse">删除</mt-button>
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
        isDiabled: true, // 不能修改
        isAbledCheck: false,
        isDiabledCheck: true, // true表示 初始不能进行
        userType: util.getUserType(),
        check: {
          is_pass: false,
          check_advice: '',
        },
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
        this.$http.get("detail/select-course?id=" + this.$route.params.courseId).then((res) => {
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
        this.$http.post("detail/modify-course", data).then((res) => {
          this.isDiabled = true
          this.getDetail();
        })
      },
      deleteCourse() {
        util.box.confirm('确定删除此课程?').then(action => {
          this.$http.get("detail/delete-course?id=" + this.$route.params.courseId).then((res) => {
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
          this.$http.get("detail/cancel-select-course?id=" + this.$route.params.courseId).then((res) => {
            this.getDetail()
          })
        }, action => {
          // 取消删除
          util.toast({
            message: "已取消删除操作"
          });
        })
      },
      submitCheck() {
        this.$http.post("detail/check-course", {
          id: this.$route.params.courseId,
          is_pass: this.check.is_pass,
          check_advice: this.check.check_advice
        }).then((res) => {
          this.getDetail();
          this.isDiabledCheck = true
        })

      }
    },

  };

</script>
<style scoped>

  .detail-section, .div-schedule {
    margin: 5px 0 0 0;
  }
  .group-btn-right {
    margin: 5px 0;
    display: flex;
    justify-content:flex-end
  }
  .group-btn-right button {
    margin: 0 2px;
  }
  .group-btn-right button:last-child{
    margin-right: 20px;
  }
  .mint-cell-wrapper button {
    margin: 7px 0 0 0;
  }
</style>
