<template>
  <div class="tab-page-container">

    <mt-cell v-for="n in course.totalCourse" :title="course.courseArr[n-1].title" :label="course.courseArr[n-1].person" :to="userType + '/details/' + course.courseArr[n-1].id"
      is-link>
      <span style="color: green">{{course.courseArr[n-1].status}}</span>
</mt-cell>
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

</style>
