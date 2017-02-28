<template>
  <div class="create-course">
    <!--表单部分-->
    <div class="part-one course-form">
      <mt-field label="课程" placeholder="课程名称" v-model="course.title" :state="course.title ? 'success' : 'error'"></mt-field>
      <mt-field label="详情" placeholder="课题详情" type="textarea" rows="8" :state="course.details ? 'success' : 'error'" v-model="course.details"></mt-field>
    </div>
    <div class="part-two">
      <mt-button type="primary" size="large" class="details-notify-btn" @click="createCourse">
        创建课题，并提交审核
      </mt-button>
    </div>
  </div>
</template>
<script>
  // 课程 状态包含 0:已删除,1:待审核,2:互选中,3:互选完成
  // schedule 状态包含 0:为选定后退选课程,1:为学生选定该课程，2:为互选成功，
  export default {
    name: "create-course",
    data() {
      return {
        course: {}
      }
    },
    methods: {
      createCourse() {
        if(!this.course.title || !this.course.details){
          return util.toast("请正确填写数据")
        }
        util.box.confirm('确定创建该课题？并提交审核？').then(action => {
          this.$http.post("schedule/create-course", this.course).then((res) => {
            if (res.data.state == 1) {
              // 操作成功
              this.$router.push({name:'schedule'})
            }
          })
        },
          action => {
            // 取消删除
            util.toast({
              message: "已取消删除操作"
            });
          }
        )



      }
    },

  };

</script>
<style>
  .details-notify-btn {
    margin-top: 15px;
  }
</style>
