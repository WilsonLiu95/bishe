<template>
  <div class="details-page">
    <!--<mt-header title="课程信息">
        <mt-button icon="back" @click="$router.go(-1)" slot="left">返回</mt-button>
    </mt-header>-->
    <mt-field label="课程" placeholder="课程名称" v-model="course.title"></mt-field>
    <mt-field label="导师" placeholder="导师姓名" v-model="course.teacher"></mt-field>
    <mt-field label="电话" placeholder="导师姓名" v-model="course.teacher_phone"></mt-field>
    <mt-field label="详情" placeholder="课题详情" type="textarea" rows="8" v-model="course.details"></mt-field>

    <div v-if="userType == 'teacher'" class="details-notify">
      <mt-cell title="当前进度" :value="course.status" :label="'已有' + course.student_num+'人选定该课程'"></mt-cell>
      <mt-button size="normal" type="primary">保存</mt-button>
      <mt-button size="normal" type="primary">发布</mt-button>
    </div>
    <!--start 学生界面-->
    <div v-if="userType == 'student'" class="details-notify">
      <div v-if="course.status == 2">
        <mt-button size="large" class="details-notify-btn" type="default">课程已被删除</mt-button>
      </div>
      <!--start 课程进度为互选中-->
      <div v-if="course.status == 1">
        <mt-cell title="进度" :value="'互选中,已有' + course.student_num+'人选定该课程'"></mt-cell>
        <mt-cell title="名单" :label="course.student_list"></mt-cell>

        <mt-button v-if="course.isSelected" size="large" class="details-notify-btn" type="default" >已选定，请主动联系老师，完成互选</mt-button>
        <mt-button v-else="course.isSelected" size="large" class="details-notify-btn" type="primary" @click="select">选定</mt-button>
      </div>
      <!--课程状态为已互选-->
      <div v-if="course.status == 0">
        <mt-cell title="进度" value="已完成互选"></mt-cell>
        <mt-cell title="互选学生" :value="course.student_list"></mt-cell>
      </div>

    </div>
  </div>
</template>
<script>
  import { Toast } from 'mint-ui';
  export default {
    name: "index",
    data() {
      return {
        userType: util.getUserType(),
        course: {} // 状态包含 0:已删除,1:互选中,2:已互选
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
        this.$http.get("detail?id=" + util.hashArr(3)).then((res) => {
          this.course = res.data
        })
      },
      select() {
        this.$http.get("detail/select?id=" + util.hashArr(3)).then( (res) =>{
          this.getDetail();
          Toast({
            message: res.data.msg,
            duration: 1500
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
