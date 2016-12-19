// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';

import MintUI from 'mint-ui'
import 'mint-ui/lib/style.css'
import VueRouter from 'vue-router'
import routerConfig from './router'
import App from './App'

import {student,teacher} from './mock'
Vue.use(VueRouter)
Vue.use(MintUI)
//开启debug模式
Vue.config.debug = true;

// ======================配置路由===============================
var router = new VueRouter(routerConfig)

window._const = {
  userType: '',
}
window.teacher = teacher
window.student = student
/* eslint-disable no-new */
new Vue({
  router: router,
  render: h => h(App)
}).$mount('#app');

