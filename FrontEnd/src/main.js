// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';

import MintUI from 'mint-ui'
import 'mint-ui/lib/style.css'
import VueRouter from 'vue-router'
import routerConfig from './router'
import App from './App'

Vue.use(VueRouter)
Vue.use(MintUI)

// ======================配置路由===============================
var router = new VueRouter(routerConfig)


/* eslint-disable no-new */
new Vue({
  el: '#app',
  template: '<App/>',
  components: { App },
  router: router
});

