// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';

import MintUI from 'mint-ui'
import 'mint-ui/lib/style.css'
import VueRouter from 'vue-router'
import axios from 'axios'

import routerConfig from './router'
import config from '../config'
import App from './App'
import { Indicator } from 'mint-ui';

import { Toast, MessageBox } from 'mint-ui';
Vue.use(VueRouter)
Vue.use(MintUI)

//开启debug模式
Vue.config.debug = true;

// ======================配置路由===============================
var router = new VueRouter(routerConfig)

// ======================配置mock数据和全局常量===============================
window._const = {

}
window.util = {
  getUserType: function () {
    var hashArr = location.hash.split("/")
    return ["student", "teacher"].indexOf(hashArr[1]) == -1 ? "" : hashArr[1]
  },
  hashArr: function (num) {
    var hashArr = location.hash.split("/")
    return hashArr[num]
  },
  toast: Toast,
  box: MessageBox,
}


// ======================配置HTTP请求===============================

// Add a request interceptor
axios.interceptors.request.use(function (config) {

  // Do something before request is sent
  Indicator.open({
    text: '请求中...',
    spinnerType: 'double-bounce'
  });
  return config;
}, function (error) {
  // Do something with request error
  return Promise.reject(error);
});

// Add a response interceptor

axios.interceptors.response.use(function (response) {
  if (typeof (response.data.msg) == "string") {
    // 如果msg存在，且不为空，则弹出
    util.toast({
      message: response.data.msg,
      duration: 2000
    })
  }

  if (response.data.state == 301) {
    if (response.data.type == "url") {
      location.href = response.data.url;
    } else if (response.data.type == "route") {
      router.push(response.data.url)
    }
  }
  // Do something with response data
  Indicator.close();
  return response;
}, function (error) {
  // Do something with response error
  return Promise.reject(error);
});

axios.defaults.baseURL = (process.env.NODE_ENV !== 'production' ? config.dev.httpUrl : config.build.httpUrl);
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
axios.defaults.withCredentials = true;

// 调试需要
axios.defaults.params = {
  XDEBUG_SESSION_START: "PHPSTORM"
}
Vue.prototype.$http = axios
/* eslint-disable no-new */
new Vue({
  router: router,
  render: h => h(App)
}).$mount('#app');

