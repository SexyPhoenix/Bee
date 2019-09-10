#### 介绍
***
Bee 是人力资源系统中的考勤应用，主要功能用于员工申请假单。Bee具有较高的性能、扩展性等，其中包括前后端分离、插拔式的规则验证（验证器）、数据过滤（装饰器）、消息队列等，后端在Laravel 的MVC模式上，又添加了Services、Repositories层，明确每个类的职责，使用Passport 的密码模式获取接口数据。

#### 技术栈
***
- 服务器：Linux（Centos 7）、Nginx  
- 前端：Element、Vue、Vue-Router、Vuex、Webpack、V-calendar  
- 资源：Elephant  
- 后端：Laravel  
- 缓存：Redis  
其他的就是一些基本工具了，比如node、npm（cnpm）、composer、git等  

#### 演示
###### 1. 登录
![image](https://raw.githubusercontent.com/SexyPhoenix/Bee/master/uploads/guide/login.gif)
###### 2. 申请
![image](https://raw.githubusercontent.com/SexyPhoenix/Bee/master/uploads/guide/apply.gif)
###### 3. 编辑
![image](https://raw.githubusercontent.com/SexyPhoenix/Bee/master/uploads/guide/edit.gif)
###### 4. 审批
![approval.gif](https://raw.githubusercontent.com/SexyPhoenix/Bee/master/uploads/guide/approval.gif)
###### 5. 搜索
![search.gif](https://raw.githubusercontent.com/SexyPhoenix/Bee/master/uploads/guide/search.gif)

#### 安装
***
###### 1. 下载
```
git clone git@github.com:SexyPhoenix/Bee.git 
```
###### 2. Laravel
```
composer install
chmod -R 777 storage //测试环境设置
php artisan key:generate
```
###### 3. 上传文件夹
```
mkdir -p uploads/bee
chmod -R 777 uploads //测试环境设置
```
###### 4. 配置.env
```
cp .env.example .env
```
```
APP_URL=http://localhost
ASSET_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```
###### 5. 数据库
```
php artisan migrate
```
###### 6. Passport
```
php artisan passport:keys
php artisan passport:client --password --name='bee'  //name 自定义
```
###### 7. 开启消息队列
```
php artisan queue:work bee --queue=apply --sleep=3 --tries=3 & > /dev/null
```
###### 8. Nginx
```
server {
    listen 80;
    server_name dev.bee.goods; // 自定义
    index index.html index.htm index.php;

    location / {
        rewrite ^/(.*)$ /index.php/$1 last;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ /uploads/ {
       root "/var/www/Bee"; // 自定义
    }

    location ~ \.(html|gif|jpg|jpeg|css|js|eot|svg|ttf|woff|woff2|png)$ {
      root "/var/www/Bee/public"; // 自定义
    }

    location ~ (.+\.php)(.*)$ {

        root /var/www/Bee/public; // 自定义
        fastcgi_split_path_info ^(.+\.php)(.+)$;
        fastcgi_pass unix:/var/run/php-fpm/php7-fpm.sock;  // 自定义
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;

        fastcgi_intercept_errors off;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
     }
}
```
###### 9. 前端
```
cnpm install //cnpm淘宝的

export const API_DOMAIN = 'http://xxx/beeapi'  //resources\js\config\bee.js 文件中修改域名
npm run dev
```
###### 10. 导入基本数据（database\sql\bee.sql）
最后，注意配置域名到host。

打开 http://xxx/bee#/  
账号：zhangxiaofan@qq.com  密码：123456






























