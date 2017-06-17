# 接口地址规则(举例)

HOST =  http://39.108.76.218/api/

API = user

接口地址 = http://39.108.76.218/api/user

# 接口 HOST
http://39.108.76.218/api/

# 请求凭证
`Login: Yes` 接口需要传在header中添加头信息`X-Access-Token`,
其值在`注册/登录接口`返回, 返回参数名 `access_token`

# Code

- `422` 客户端验证错误, 客户端需要将返回参数中的errors字段展示出来


接口
==========

## 1. 用户注册
- API: user
- Method: POST
- Params:
    - 见 [6. 获取国家代码接口](#6-获取国家代码接口)

    ```
    |   Field       |Required   |Default|Example|
    |---            |---        |---    |---
    |mobile         |Y          |       | 13512345678
    |state_code     |Y          |       | +86
    |password       |Y          |       | 123456
    |nickname       |Y          |       | Felix
    ```

- Result:

    ```json
    {
      "code": 200,
      "message": "注册成功",
      "data": {
        "id": 1,
        "nickname": "Felix",
        "state_code": "+86",
        "mobile": "13541013376",
        "avatar": "",
        "account": "10000211",
        "im_account": "",
        "im_password": "",
        "sex": "unknown",
        "city_name": "5001",
        "city_code": "成都",
        "age": 0,
        "it_says": "",
        "address": "",
        "created_at": "2017-06-05 22:19:29"
      },
      "access_token": "KsBNIaxakS69WL9nItoG8ZP7hM9qOCu3FBOLz80s"
    }
    ```

## 2. 用户登录
- API: user
- Method: PUT
- Params:

    ```
    |   Field       |Required   |Default|Example|
    |---            |---        |---    |---
    |mobile         |Y          |       | 13512345678
    |password       |Y          |       | 123456
    ```

- Result: 见 [1. 用户注册](#1-用户注册)

    ```json
    {
      "code": 200,
      "message": "登录成功",
      "data": {
        "...": "..."
      },
      "access_token": "KsBNIaxakS69WL9nItoG8ZP7hM9qOCu3FBOLz80s"
    }
    ```

## 3. 获取用户资料
- API: user
- Method: GET
- Login: Yes
- Params:

    ```
    |   Field       |Required   |Default|Example|
    |---            |---        |---    |---
    |mobile         |Y          |       | 13512345678
    |password       |Y          |       | 123456
    ```

- Result: 见 [1. 用户注册](#1-用户注册)

    ```json
    {
      "code": 200,
      "message": "获取用户信息成功",
      "data": {
        "...": "..."
      }
    }
    ```

## 4. 用户注销
- API: user/logout
- Method: PUT
- Login: Yes
- Params: None
- Result:

    ```json
    {
      "code": 200,
      "message": "注销成功"
    }
    ```

## 5. 添加好友
- API: friend
- Method: POST
- Login: Yes
- Params:

    ```
    |   Field       |Required   |Default|Example|
    |---            |---        |---    |---
    |friend_id      |Yes        |       |1
    ```

- Result:

    ```json
    {
      "code": 200,
      "message": "添加好友成功"
    }
    ```

## 6. 获取好友列表
- API: friend
- Method: GET
- Login: Yes
- Params:

    ```
    |   Field       |Required   |Default|Example|
    |---            |---        |---    |---
    |per_page       |No         |1000   |每页展示数量, 默认1000条
    ```

- Result:

    ```json
    {
      "code": 200,
      "message": "获取好友列表成功",
      "list": [
        {
          "id": 3,
          "friend_id": 1,
          "friend_nickname": "Felix"
        },
        {
          "id": 2,
          "friend_id": 1,
          "friend_nickname": "Felix"
        }
      ],
      "pagination": {
        "total": 2,
        "per_page": 1000,
        "current_page": 1,
        "last_page": 1,
        "from": 1,
        "to": 2
      }
    }
    ```

# 6. 获取国家代码接口

- API: state/code
- Login: No
- Params: None
- Result:

    ```json
    {
      "code": 200,
      "message": "获取国家/地区代码成功",
      "list": [
        {
          "id": 15,
          "name": "中国",
          "code": "+86"
        },
        {
          "id": 183,
          "name": "图瓦卢",
          "code": "+688"
        }
      ],
      "pagination": {
        "total": 183,
        "per_page": 1000,
        "current_page": 1,
        "last_page": 1,
        "from": 1,
        "to": 183
      }
    }
    ```

# Codes

**成功**
- `200` 请求成功
- `202` 收到请求, 但还未作出处理

**客户端错误**
- `404` 请求资源未找到
- `421` 登录时, 账号密码错误
- `422` 请求参数验证错误

**服务器错误**
- `522` 服务器保存至数据库失败
- `523` 服务器访问第三方接口失败

# Pagination

- `total` 总条数
- `per_page` 每页展示条数
- `current_page` 当前页码
- `last_page` 最后一页页码
- `from` 当前页第一条编号, e.g. from=1 第一条
- `to` 当前页最后一条编号, e.g. from=20 第二十条
