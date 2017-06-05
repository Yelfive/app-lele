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
        "id": 3,
        "mobile": "13541013372",
        "state_code": "+86",
        "nickname": "Felix",
        "it_says": "",
        "address": "",
        "city_name": "5001",
        "city_code": "成都",
        "account": "10000213",
        "created_at": "2017-06-05 16:22:44"
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

- Result:

    ```json
    {
      "code": 200,
      "message": "注册成功",
      "data": {
        "id": 3,
        "mobile": "13541013372",
        "state_code": "+86",
        "nickname": "Felix",
        "it_says": "",
        "address": "",
        "city_name": "5001",
        "city_code": "成都",
        "account": "10000213",
        "created_at": "2017-06-05 16:22:44"
      },
      "access_token": "KsBNIaxakS69WL9nItoG8ZP7hM9qOCu3FBOLz80s"
    }
    ```

## 2. 用户注销
- API: user/logout
- Method: PUT
- Login: Yes
- Params:

    ```
    |   Field       |Required   |Default|Example|
    |---            |---        |---    |---
    ```

- Result:

    ```json
    {
      "code": 200,
      "message": "注销成功",
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
