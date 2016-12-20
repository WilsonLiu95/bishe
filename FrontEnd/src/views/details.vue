<template>
  <div class="details-page">
    <!--<mt-header title="课程信息">
        <mt-button icon="back" @click="$router.go(-1)" slot="left">返回</mt-button>
    </mt-header>-->
    <mt-field label="课程" placeholder="课程名称" v-model="course.title"></mt-field>
    <mt-field label="导师" placeholder="导师姓名" v-model="course.teacherName"></mt-field>
    <mt-field label="电话" placeholder="导师姓名" v-model="course.teacherPhone"></mt-field>
    <mt-field label="方向" placeholder="课程所属方向" v-model="course.direction"></mt-field>
    <mt-field label="所需人数" placeholder="所需人数" v-model="course.finalHeadCount"></mt-field>
    <!--<mt-field label="课程状态" placeholder="课程状态" v-model=""></mt-field>-->
    <mt-field label="简介" placeholder="简介" type="textarea" rows="3" v-model="course.briefIntro"></mt-field>
    <mt-field label="详情" placeholder="课题详情" type="textarea" rows="5" v-model="course.details"></mt-field>
    <div v-if="userType == 'teacher'" class="details-notify">
      <mt-cell title="当前进度" :value="course.status" label="已有3人选定该课程"></mt-cell>
      <mt-button size="normal" type="primary">保存</mt-button>
      <mt-button size="normal" type="primary">发布</mt-button>
    </div>
    <div v-if="userType == 'student'" class="details-notify">
      <mt-cell title="当前进度" :value="course.status" label="已有3人选定该课程"></mt-cell>
      <mt-button size="large" type="primary">选定</mt-button>
    </div>
  </div>
</template>
<script>
  export default {
    name: "index",
    data() {
      return {
        userType: window._const.userType,
        course: {
          // 课程信息
          teacherName: "许炜",  // 允许数据冗余，减少查表操作
          teacherPhone: "18585858888",
          direction: "互联网", // 课程的所属方向

          finalHeadCount: 1, // 最终需要的人数
          currentHead: 2, // 当前选了该课程的人数
          status: "互选中", // 状态包含 0:编辑中,1:进行中,2:已选定 待确认@TODO 是否需要删除课程的功能，还是创建后仅限修改

          // 课程介绍  待确认@TODO 还需要哪些课程属性
          title: "基于微信的毕设选题系统",
          briefIntro: "基于微信设计一套可以提供师生双方互选毕业设计课题的系统", // 简介
          details: "毕业课设选题系统，来源于院系要求。首先希望能够解决的问题是增加专业方向限制的功能，即本方向的人只能选择本方向的课题。另外老的系统由于开发时间较早，界面以及功能等方面渐渐不能满足现在的需求。同时也希望在选课上面进行一些改变，添加一些有趣的玩法。也做的更加合理。同时开发的时候考虑可扩展性，以快速接入其他院系。", // 详情
        }
      }
    },
    watch: {
    },
    created() {
    },
    computed: {
    },
    methods: {
    },

  };
</script>
