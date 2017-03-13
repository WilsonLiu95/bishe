# 毕业课设选题系统 后台开发指南
## 初始化说明
1. 下载代码到本地
2. composer install (按照相应的依赖，如果没有安装composer，请先安装)
3. 本地开发则复制`.env.dev`,线上则复制`.env.pro`,改名为`.env`
4. `php artisan  key:generate` 重置`.env`中的密钥
5. 检测并改正`.env`中相应的数据库配置及其他配置,同时记得打开redis服务器
6. `php artisan migrate:refresh --seed` 初始化数据库
7. `php artisan serve --host=127.0.0.1` 启动服务器，访问 `http://127.0.0.1:8000/test` 看是否启动成功
8. 本地配置host， 将`dev.wilsonliu.cn`映射到`127.0.0.1` 。之后开发都通过该域名进行访问(因为微信登录设置了安全域名)
9. 代码的编写只需要在`app/Http/routes/`下加相应的路由，之后在`app/Http/Controller/Wechat`添加相应文件，编写相应的`function`
## 记录出现过的问题
###  小问题
1. 配置中的`unix_socket`，应该是记录socket的路径，换台机子可能对应路径不一样
2. guzzle请求https服务时(微信认证)，本地没有对应的SSL证书造成请求失败。可以省去SSL证书认证`$client = new \GuzzleHttp\Client(['base_uri'=>'https://api.weixin.qq.com','verify' => false]);`
3. window平台可以安装XMAPP提供mysql等服务。XMAPP安装后,无法初始化数据。原因是安装后，密码没有设置为root [设置方法](http://www.cnblogs.com/xlw1219/p/3197771.html)

## 整体说明
本项目主要采用laravel作为框架。另外使用了guzzle作为http请求插件。
所有的配置文件都在`.env`中，包括数据库配置，和微信公众号配置，和跨域设置的域名，与前端的前缀路径(给前端返回重定向请求时使用)无法初始化数据。原因是安装后，密码没有设置为root

### 数据库
`database`文件夹下，`migrations`下为数据库迁移的建表文件。`Factories`下为利用laravel的模型工程进行数据填充。
- [数据库: 迁移](https://laravel-china.org/docs/5.1/migrations)
- [数据填充](https://laravel-china.org/docs/5.1/seeding)

这两个都是laravel提供的功能，非常强大，可以直接生成数据，不用个人一步步填充。

### 模型 
`app/model`下为模型文件，具体查看laravel文档[Eloquent: 入门](https://laravel-china.org/docs/5.1/eloquent)
主要是定义了一些关联。还有如老师和学生表，则定义了一个`account`函数，用于补全个人信息，因为部分信息诸如学院等这些需要查询其他表

### 路由 
`app/Http/routes.php`为路由文件，这里将PC和微信端分别用不同的`prefix`前缀隔离开来，同时，加入鉴权的中间件。经过该中间件可确保用户已登录。
如无特殊情况，请将API置于中间件流程中。

### 中间件 
中间件主要是鉴权和跨域两部分。

跨域是因为本地开发时，前端的服务器在8080端口，后端在8000端口，跨域需要设置跨域头。所以默认全局引用。

鉴权则分为微信端和pc端。

微信端的鉴权原理为，微信登录会给出一个openid。每个openid对应一个用户，我们将识别这个openid是否存在于数据库中，如果存在则认为该微信为数据库里的该条记录。
否则将重定向到注册界面， 进行注册。

PC管理端则简单的通过`cookie`进行鉴权，用户登录过了就在session中将`isLogin`置为`true`。如果isLogin不为`true`则表示未登录，重新定向到登录界面。

PC端不提供注册，只有登录。PC为教务科使用。
微信端只有注册，确认名字与学号/工号对应即绑定该微信与该学生/老师。

### 控制器
控制器承担最多的任务。也是业务代码的落地之处。Admin为PC管理端的后台代码，Wechat为微信移动端的代码

首先，所有控制器都继承了`app/Http/Controller/Controller.php`。因此，我在此写入了一些常用的方法。
- `$this->json()` 用于单纯的数据传输
- `$this->toast()` 与`$this->json()`相比，多了一个`msg`属性，用于弹出toast提示框，诸如"您还未注册"，"你们有操作权限"等等提示
- `$this->redirect()` 重定向，有两种一种是url，一种是前端路由

另外对于单个系统存在的诸多通用函数，也进行了封装以便复用。wechat的通用函数为 `app/Http/Controller/BaseTrait.php`（`trait`为php的新特性，如果不了解的可以先百度）。
`Wechat.php`为微信授权登录的代码。关于微信的授权登录请查阅微信开发文档。

### 关于微信 
微信开发的权限需要通过认证，认证后需要服务号才有全部权限。所以个人进行开发时，可以申请私人测试账号，测试账号拥有全部权限，但是需要用户先关注你的测试公众号，才能打开你的网页。
- 测试号申请链接 http://mp.weixin.qq.com/debug/cgi-bin/sandbox?t=sandbox/login`
- 接口调试工具 http://mp.weixin.qq.com/debug/ 


## TODO LIST
1. 后端很多接口的数据未进行校验
