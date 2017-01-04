export default {
  base: __dirname,
  routes: [
    { path: '/', component: require('_views/login.vue') },
    { path: '/wechat', component: require('_views/wechat.vue') },
    { path: '/login', component: require('_views/login.vue') },
    {
      path: '/(student|teacher)', component: require('_views/index.vue'), // 通过这个识别老师和学生
      children: [
        // 基本的四个
        { path: '', redirect: "course" }, // 重定向到默认的course
        { path: 'course', component: require('_views/course.vue') },
        { path: 'schedule', component:  require('_views/schedule.vue')  },
        { path: 'message', component: require('_views/message.vue')  },
        { path: 'account', component:  require('_views/account.vue')  },
      ]
    },
    {
      path: '/(student|teacher)/details/:id', component: require('_views/details.vue'),
    },
    { path: '*', component: require('_views/not-found.vue') }
  ]
}
