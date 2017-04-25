export default {
  base: __dirname,
  routes: [
    // ========================================PC管理系统========================================
    { path: '/', redirect: "/login" },
    { path: '/login', component: require('_views/login.vue') },
    {
      name: 'admin',path: '/admin', component: require('_views/admin.vue'), // 通过这个识别老师和学生
      children: [
        { path: '', redirect: "home" }, 
        { path: "home", component: require('_views/home.vue') },
        { path: "student", component: require('_views/student.vue') },
        { path: "course", component: require('_views/course.vue') },
        { path: "system-config", component: require('_views/system-config.vue') },
      ]
    },
    { path: '*', component: require('_views/404.vue') }
  ]
}
