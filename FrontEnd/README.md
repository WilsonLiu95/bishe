# 毕业课设互选系统 前端部分
前端部分分为两部分
- `admin`  PC管理端
- `wechat` 微信端
采用`vue-cli`初始化，打包成单页面。两部分完全独立。技术一致，唯一不同在于组件库不同，`admin`引入的是`Element`。`wechat`引入的是 `mint-ui`。
为了保证精简与移动端的打开速度，所以将两部分进行了拆分，同时也是为了可以并行开发。

## 开发说明
注：
- 在开发前，请先阅读`Vue`的官方文档进行学习
- 配置host， 增加一条记录 `127.0.0.1  dev.wilsonliu.cn`

1. 将整个项目克隆到本地
2. 在命令行中进入相应的文件夹，比如开发微信端则进入`wechat`，安装NPM依赖`npm install`
3. 安装完成后直接`npm run dev`，即可打开服务器进行开发 `http://dev.wilsonliu.cn:8080`。
4. 开发完成之后`npm run build`运行打包。会将所有文件打包到`dist`目录


## 整体说明
### 采用技术
- [vue2.0](https://vuefe.cn/v2/guide/)  框架
- [vue-router](https://router.vuejs.org/zh-cn/) 路由插件
- [axios](https://github.com/mzabriskie/axios) AJAX请求插件
- [mint-ui](http://mint-ui.github.io/docs/#!/zh-cn2) 微信移动端组件库
- [element-ui](http://element.eleme.io/#/zh-CN/component/installation) PC管理端组件库
- [validator.js](https://github.com/chriso/validator.js) 表单校验组件
- [ECMAScript 6 入门](http://es6.ruanyifeng.com/)

### 配置
- 路由配置都在`src/router.js`中。
- http请求的前缀在`config/index.js`中，即`httpUrl`。`build`为打包情况下的前缀。dev为开发时的前缀。

### 组件说明
这里因为引用了组件库，所以并没有自己新建组件。而是以页面形式进行开发。

## 如何新增页面
1. 首先在`src/views/page`目录下新建文件`test.vue`。
2. 在`src/router.js`中添加相应的路由     `{ path: '/test', component: require('_views/page/test.vue') }`
3. 在浏览器里打开 `http://dev.wilsonliu.cn:8080/test`，即可看到对应页面的效果

## 全局变量
因为采用的是单页面的框架，所以全局变量在所有页面都是一直存在的，对于一个小型应用来说。可以充分利用这点。
所以在`src/main.js`中设置了几个挂载在window下的全局变量和方法。

```
window._const = {
  msg: [], // 保存所有消息，只有当消息加载完成时才会存储。当有新消息的时候，msg会清空
  isTeacher: '', // 布尔值，用于判断用户类型
  search: "", // course页面的search字段，用于记忆用户的搜索
  page: '', // 记忆在course页面的哪一页
}
window.util = {
  v: validator,
  is(type, value, option) { // 封装了一次validator验证函数,输入非字符串时，validator会报错。同时封装一下接口
    if (value === undefined || value === null) {
      return false
    }
    var args = [].slice.call(arguments).slice(2);
    return validator[type](value, args)
  },
  toast: Toast, // 提示弹窗
  box: MessageBox, // alert,message,confirm
}

```

### 如何区分学生和老师
最开始通过url来区分，但是通过url区分会造成不安全，虽然后端会对请求进行权限判断，但是在页面显示上会造成困惑。
因此将其改成通过在全局中存储`_const.isTeacher`来标识用户类型，同时通过`util.isTeacher`函数来保证变量必定存在。
当然在`wechat.vue`与`register.vue`两个页面请求中返回的数据中同样存在重置`_const.isTeacher`的数据。故可以减少该请求。

### main.js
`main.js`中在ajax请求前后设置了请求动画，弹窗，url自动跳转三个部分，参见`axios.interceptors.response`。 

```
axios.defaults.baseURL = (process.env.NODE_ENV !== 'production' ? config.dev.httpUrl : config.build.httpUrl);// 同时根据不同环境引用不同的ajax请求前缀。
axios.defaults.withCredentials = true; // 本地dev开发时，存在跨域。跨域请求时，将不带上cookie。需要设置这个参数为true才会带上cookie。坑了几天。

```