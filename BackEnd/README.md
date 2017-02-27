# 毕业课设选题系统 后台开发指南
1. 下载代码到本地
2. composer install (按照相应的依赖，如果没有安装composer，请先安装)
3. 本地开发则复制`.env.dev`,线上则复制`.env.pro`,改名为`.env`
4. `php artisan  key:generate` 重置`.env`中的密钥
5. 检测并改正`.env`中相应的数据库配置及其他配置,同时记得打开redis服务器
6. `php artisan db:seed` 初始化数据库(如果数据库已存在则 `php artisan migrate:refresh --seed`)
7. `php artisan serve --host=127.0.0.1` 启动服务器，访问 `http://localhost:8000/test` 看是否启动成功
8. 本地配置host， 将`bishe.wilsonliu.cn`映射到`127.0.0.1` 

## 记录出现过的问题
###  小问题
1. 配置中的`unix_socket`，应该是记录socket的路径，换台机子可能对应路径不一样
2. guzzle请求https服务时(微信认证)，本地没有对应的SSL证书造成请求失败。可以省去SSL证书认证`$client = new \GuzzleHttp\Client(['base_uri'=>'https://api.weixin.qq.com','verify' => false]);`
