<template>
  <div class="tab-page-container">

    <mt-cell v-for="n in course.totalCourse" :title="course.courseArr[n-1].title" :label="course.courseArr[n-1].person" :to="userType + '/details/' + course.courseArr[n-1].id"
      is-link>
      <span style="color: green">{{course.courseArr[n-1].status}}</span>
</mt-cell>
<div>
  <div v-if="userType == 'teacher'" class="schedule-notify">
    <span>提示：你可以创建6门选修课程，当前还可以创建3们课程</span>
    <mt-button size="large" type="primary">创建</mt-button>
  </div>
  <div v-if="userType == 'student'" class="schedule-notify">
        <span>提示：你可以创建6门选修课程，当前还可以创建3们课程</span>
  </div>
</div>
</div>

</template>
<script>
  import { Toast } from 'mint-ui';

  export default {
    name: "schedule-tab",
    data() {
      return {
        userType: window._const.userType,
        course: {
          totalCourse: 3,
          courseArr: [{
            title: "《基于微信的选课系统》",
            person: "3/1",
            teacher: "许炜",
            status: "已完成",
            id: "11121254"
          }, {
            title: "《人脸识别的设计》",
            person: "3/1",
            teacher: "许炜",
            status: "待发布",
            id: "23456789"
          }, {
            title: "《给我个面子》",
            person: "3/1",
            teacher: "许炜",
            status: "双选中",
            id: "456232789"
          }]
        },
      }
    },
    created() {
    },
    methods: {

    },

  };

</script>
<style>
.schedule-notify {
  margin-top: 5px;
  font-size: 13px;
  text-align: center;
}
</style>
