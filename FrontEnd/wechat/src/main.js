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
import { Indicator, Toast, MessageBox } from 'mint-ui'
import validator from 'validator'

Vue.use(VueRouter)
Vue.use(MintUI)

//开启debug模式
Vue.config.debug = true;

// ======================配置路由===============================
var router = new VueRouter(routerConfig)

// ======================配置mock数据和全局常量===============================
window._const = {
  msg: [], // 保存所有消息
  isTeacher: '',
  search: "", // 搜索信息
  page: '', // 用户在哪一页
}
window.util = {
  v: validator,
  is(type, value, option) {
    if (value === undefined || value === null) {
      return false
    }
    var args = [].slice.call(arguments).slice(2);
    return validator[type](value, args)
  },
  toast: Toast,
  box: MessageBox,
}


// ======================配置HTTP请求===============================

// Add a request interceptor
axios.interceptors.request.use(function (config) {
  if (!config.noIndicator) {
    Indicator.open({
      text: '请求中...',
      spinnerType: 'double-bounce'
    });
  }
  // Do something before request is sent

  return config;
}, function (error) {
  // Do something with request error
  return Promise.reject(error);
});

// Add a response interceptor

axios.interceptors.response.use(function (response) {
  if (typeof (response.data.msg) == "string" && response.data.msg !== "") {
    // 如果msg存在，且不为空，则弹出
    util.toast({
      message: response.data.msg,
      duration: 2000
    })
  }

  if (response.data.state == 301) {
    if (response.data.url) {
      location.href = response.data.url;
    } else {
      router.push(response.data.option)
    }
  }
  // Do something with response data
  Indicator.close();
  return response;
}, function (error) {
  // Do something with response error
  return Promise.reject(error);
});


axios.defaults.baseURL = (process.env.NODE_ENV !== 'production' ? config.dev.httpUrl : config.build.httpUrl);// 同时根据不同环境引用不同的ajax请求前缀。
axios.defaults.withCredentials = true; // 本地dev开发时，存在跨域。跨域请求时，将不带上cookie。需要设置这个参数为true才会带上cookie。坑了几天。
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
// phpstorm断点调试 需要此参数
// axios.defaults.params = {
//   XDEBUG_SESSION_START: "PHPSTORM"
// }
Vue.prototype.$http = axios
/* eslint-disable no-new */
new Vue({
  router: router,
  render: h => h(App)
}).$mount('#app');

