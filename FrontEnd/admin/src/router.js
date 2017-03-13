export default {
  base: __dirname,
  routes: [
    // ========================================PC管理系统========================================
    { path: '/', redirect: "/login" },
    { path: '/login', component: require('_views/login.vue') },
    {
      path: '/admin', component: require('_views/admin.vue'), // 通过这个识别老师和学生
      children: [
        { path: '', redirect: "deal" }, // 重定向到默认的course§
        { path: "deal", component: require('_views/deal.vue') },
        { path: "manage", component: require('_views/manage.vue')},
        { path: "logout", redirect: "/login"}    
      ]
    },
    { path: '*', component: require('_views/404.vue') }
  ]
}
