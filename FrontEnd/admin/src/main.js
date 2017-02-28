// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';

import VueRouter from 'vue-router'
import axios from 'axios'

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-default/index.css'

import routerConfig from './router'
import config from '../config'
import App from './App'
Vue.use(VueRouter)
Vue.use(ElementUI)

import { Loading,Message } from 'element-ui';
//开启debug模式
Vue.config.debug = true;

// ======================配置路由===============================
var router = new VueRouter(routerConfig)


// ======================配置HTTP请求===============================
var loading
// Add a request interceptor
axios.interceptors.request.use(function (config) {
  // Do something before request is sent
  loading = Loading.service({
    fullscreen: true,
    text:"请求中..."
  })

  return config;
}, function (error) {
  // Do something with request error
  return Promise.reject(error);
});

// Add a response interceptor

axios.interceptors.response.use(function (response) {
  if (response.data.msg && typeof (response.data.msg) == "string") {
    // 如果msg存在，且不为空，则弹出
    Message({
      message:response.data.msg,
      type: response.data.state == 0 ? "error" : "success" // 状态为0则为错误，其他都显示为成功
    })
  }

  if (response.data.state == 301) {
    if (response.data.url) {
      location.href = response.data.url;
    } else {
      router.push(response.data.option)
    }
  }

  loading.close();

  return response;
}, function (error) {
  // Do something with response error
  return Promise.reject(error);
});

axios.defaults.baseURL = (process.env.NODE_ENV !== 'production' ? config.dev.httpUrl : config.build.httpUrl); // 根据环境不同，配置不同的ajax请求前缀
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
axios.defaults.withCredentials = true; // 跨域相关

Vue.prototype.$http = axios // 将axios绑定到vue上
/* eslint-disable no-new */
new Vue({
  router: router,
  render: h => h(App)
}).$mount('#app');

