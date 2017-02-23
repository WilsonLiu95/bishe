# 基于微信的华科毕设选题系统

## 技术栈
### 前端
- vue2.0
- vue-router
- axios
- mint-ui
### 后台
- laravel
- redis
- mysql

## 时间
1. 2016-12-16号开始

grant all privileges on *.* to admin@localhost identified by 'admin' with grant option 
grant all privileges on *.* to admin@"%" identified by 'admin' with grant option


grant all PRIVILEGES on *.* to wilson@'119.29.173.208' identified by 'wilson';

## 消息系统
### 对于老师
1. 学生选定“我”的课程
2. 学生退选“我”的课程
3. 我发布的课程审核 通过or打回，收到通知

### 对于学生
1. 老师选定其他人，收到退选通知
2. 老师选定自己，收到互选成功通知
3. 老师已将课程删除，收到退选通知
4. 互选后，老师退选学生，重新回到“互选中”状态

## 对于专业方向管理员 具有审核功能
在课题页面排序时，将“待审核”的课程排列在前面