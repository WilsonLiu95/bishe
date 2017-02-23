export default {
  base: __dirname,
  routes: [

    // ========================================微信端========================================
    // 功能区
    { path: '/', redirect: "/register" },
    { path: '/register', component: require('_views//register.vue') },
    { path: '/wechat', component: require('_views//wechat.vue') },

    // tab页面
    {
      path: '/(student|teacher)', component: require('_views//tab/index.vue'), // 通过这个识别老师和学生
      children: [
        // 基本的四个
        { path: '', redirect: "course" }, // 重定向到默认的course
        { path: 'course', component: require('_views//tab/course.vue') },
        { path: 'schedule', component: require('_views//tab/schedule.vue') },
        { path: 'message', component: require('_views//tab/message.vue') },
        { path: 'account', component: require('_views//tab/account.vue') },
      ]
    },
    // 其他页面
    { name: "details", path: '/(student|teacher)/details/:courseId', component: require('_views//page/details.vue') },

    { name: "teacher-info", path: '/(student|teacher)/details/:courseId/teacher-info', component: require('_views//page/teacher-info.vue') },
    { path: '/teacher/details/:courseId/student-info/:index', component: require('_views//page/student-info.vue') },

    { name: "student-list", path: '/teacher/details/:courseId/student-list', component: require('_views//page/student-list.vue') },

    { name: "select-student",path: '/teacher/details/:courseId/select-student', component: require('_views//page/select-student.vue')},
    { name: "create-course",path: '/teacher/create-course', component: require('_views//page/create-course.vue')},

    { path: '*', component: require('_views//404.vue') }
  ]
}
