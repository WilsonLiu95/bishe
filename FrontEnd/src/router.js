export default {
  base: __dirname,
  routes: [

    // ========================================微信端========================================
    // 功能区
    { path: '/', redirect: "/register" },
    { path: '/register', component: require('_views/wechat/register.vue') },
    { path: '/wechat', component: require('_views/wechat/wechat.vue') },

    // tab页面
    {
      path: '/(student|teacher)', component: require('_views/wechat/tab/index.vue'), // 通过这个识别老师和学生
      children: [
        // 基本的四个
        { path: '', redirect: "course" }, // 重定向到默认的course
        { path: 'course', component: require('_views/wechat/tab/course.vue') },
        { path: 'schedule', component: require('_views/wechat/tab/schedule.vue') },
        { path: 'message', component: require('_views/wechat/tab/message.vue') },
        { path: 'account', component: require('_views/wechat/tab/account.vue') },
      ]
    },
    // 其他页面
    { name: "details", path: '/(student|teacher)/details/:courseId', component: require('_views/wechat/page/details.vue') },
    { name: "teacher-info", path: '/(student|teacher)/details/:courseId/teacher-info', component: require('_views/wechat/page/teacher-info.vue') },

    { name: "student-list", path: '/teacher/details/:courseId/student-list', component: require('_views/wechat/page/student-list.vue') },
    { path: '/teacher/details/:courseId/student-info/:index', component: require('_views/wechat/page/student-info.vue') },
    { name: "select-student",path: '/teacher/details/:courseId/select-student', component: require('_views/wechat/page/select-student.vue')},
    { name: "create-course",path: '/teacher/create-course', component: require('_views/wechat/page/create-course.vue')},

    // ========================================PC管理系统========================================
    { path: '/login', component: require('_views/pc/login.vue') },
    {
      path: '/admin', component: require('_views/pc/admin.vue'), // 通过这个识别老师和学生
      children: [
        { path: '', redirect: "deal" }, // 重定向到默认的course§
        { path: "deal", component: require('_views/pc/deal.vue') },
        { path: "export", component: require('_views/pc/export.vue') },
      ]
    },
    { path: '*', component: require('_views/wechat/404.vue') }
  ]
}
