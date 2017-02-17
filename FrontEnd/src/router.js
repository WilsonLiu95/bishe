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
    { path: '/(student|teacher)/details/:id', component: require('_views/wechat/page/details.vue') },
    { path: '/teacher/student-list/:id', component: require('_views/wechat/page/student-list.vue') },

    // ========================================PC管理系统========================================
    { path: '/login', component: require('_views/pc/login.vue') },
    {
      path: '/admin', component: require('_views/pc/admin.vue'), // 通过这个识别老师和学生
      children: [
        { path: '', redirect: "deal" }, // 重定向到默认的course
        { path: "deal", component: require('_views/pc/deal.vue') },
        { path: "export", component: require('_views/pc/export.vue') },
      ]
    },
    { path: '*', component: require('_views/wechat/404.vue') }
  ]
}
