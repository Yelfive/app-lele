# 接口地址规则(举例)

HOST =  http://39.108.76.218/api/

API = user

接口地址 = http://39.108.76.218/api/user

# 接口 HOST
http://39.108.76.218/api/

# 请求凭证
`Login: Yes` 接口需要传在header中添加头信息`X-Access-Token`,
其值在`注册/登录接口`返回, 返回参数名 `access_token`

# Codes

**成功**
- `200` 请求成功
- `202` 收到请求, 但还未作出处理

**客户端错误**
- `404` 请求资源未找到
- `421` 登录时, 账号密码错误
- `422` 请求参数验证错误, 客户端需要将返回参数中的errors字段展示出来

**服务器错误**
- `522` 服务器保存至数据库失败
- `523` 服务器访问第三方接口失败

# Pagination

> 列表数据时返回`pagination`字段, 包涵:

- `total` 总条数
- `per_page` 每页展示条数
- `current_page` 当前页码
- `last_page` 最后一页页码
- `from` 当前页第一条编号, e.g. from=1 第一条
- `to` 当前页最后一条编号, e.g. from=20 第二十条

> 列表接口请求均包含可选参数

- `per_page` 每页展示条数
- `page` 当前页

接口
==========

## 1. 用户注册
- API: user
- Method: POST
- Params:

    |   Field       |Required   |Default|Example
    |---            |---        |---    |---
    |mobile         |Y          |       | 13512345678
    |password       |Y          |       | 111111
    |verify_code    |Y          |       | 123456, 六位数验证码
    |nickname       |N          |       | Felix
    |sex            |N          |unknown| male=男,female=女
    |address        |N          |       | 生活地区
    |avatar         |N          |       | 用户头像
    |age            |N          |0      | 用户年龄

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

    |   Field       |Required   |Default|Example
    |---            |---        |---    |---
    |mobile         |Y          |       | 13512345678
    |password       |Y          |       | 123456

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

    |Field          |Required   |Default|Example
    |---            |---        |---    |---
    |mobile         |Y          |       | 13512345678
    |password       |Y          |       | 123456

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
- API: friends/request
- Method: POST
- Login: Yes
- Params:

    > 三者必填其一, 优先级：friend_id > mobile > account

    |Field          |Required   |Default| Example
    |---            |---        |---    |---
    |friend_id      |Yes        |       | 好友ID 
    |mobile         |Yes        |       | 手机号, 手机号长度低于11时, 判定为乐乐号
    |account        |No         |       | 乐乐号,

- Result:

    ```json
    {
      "code": 200,
      "message": "好友请求发送成功"
    }
    ```
    
### 5.1 申请列表

- API: friends/request
- Method: GET
- Login: Yes
- Params: 

    |Field          |Required   |Default|Example
    |---            |---        |---    |---
    |per_page       |No         |       |每页显示条数 
    |page           |No         |       |当前页码

- Result:

    ```json
    {
      "code": 200,
      "message": "获取列表成功",
      "list": [
        {
            "uid": 3,
            "nickname": "Felix",
            "mobile": "13541010003",
            "state_code": "+86",
            "avatar": "",
            "account": "10000213",
            "sex": "unknown",
            "city_name": "5001",
            "city_code": "成都",
            "age": 0,
            "it_says": "",
            "request_id": 3,
            "sender": 10,
            "friend_id": 3,
            "created_at": 1498572631,
            "updated_at": 1498572631,
            "status": 0,
            "from": 1,
            "remark": ""
        }
      ],
      "pagination": {}
    }
    ```
    **list**
    
    Field       |Description
    ---         |---
    uid         |用户ID
    request_id  |好友请求id
    sender      |申请人ID
    friend_id   |被申请人ID
    created_at  |申请时间
    updated_at  |申请更新时间, e.g. 同意，拒绝时间
    
    **status**
    
    Status  |Description
    ---     |---
    0       |未处理
    1       |已同意
    -1      |已拒绝
    
    **from**
    
    From    |Description
    ---     |---
    1       |手机号查找
    2       |附近的人
    
    > NOTICE: 根据sender是否自己，来判断是否自己发出的请求
    
### 5.2 同意好友请求

- API: friends/request
- Method: PUT
- Login: Yes
- Params:

    |Field          |Required   |Default|Example
    |---            |---        |---    |---
    |request_id     |Yes        |       |好友请求ID 

- Result:

    ```json
    {
      "code": 200,
      "message": "成功添加好友"
    }
    ```

### 5.3 拒绝好友请求

- API: friends/request
- Method: DELETE
- Login: Yes
- Params:

    |Field          |Required   |Default|Example
    |---            |---        |---    |---
    |request_id     |Yes        |       |好友请求ID

- Result:

    ```json
    {
      "code": 200,
      "message": "好友申请已拒绝"
    }
    ```

### 5.4 删除好友

- API: friend/{id}

    > NOTICE: 这个地方是`friend`.
    
- Method: DELETE
- Login: Yes
- Params:

    |Field          |Required   |Default|Example
    |---            |---        |---    |---
    |id             |Yes        |       |好友UserID

- Result:

    ```json
    {
      "code": 200,
      "message": "删除成功"
    }
    ```

## 6. 获取好友列表
- API: friends
- Method: GET
- Login: Yes
- Params:

    |   Field       |Required   |Default|Example
    |---            |---        |---    |---
    |per_page       |No         |1000   |每页展示数量, 默认1000条

- Result:

    ```json
    {
      "code": 200,
      "message": "获取好友列表成功",
      "list": [
        {
            "id": 10,
            "nickname": "Felix",
            "state_code": "+86",
            "mobile": "13541013373",
            "avatar": "88950b12016202c8798ffe8d0bb46eea10.png",
            "account": "10000321",
            "im_account": "88950b12016202c8798ffe8d0bb46eea10",
            "sex": "female",
            "city_name": "成都",
            "city_code": "5001",
            "age": 0,
            "it_says": "Hello world to you",
            "address": "0",
            "created_at": "2017-06-27 06:36:10",
            "updated_at": "2017-06-27 06:36:10",
            "friend_nickname": "你好"
        }
      ],
      "pagination": {
        "total": 1,
        "per_page": 1000,
        "current_page": 1,
        "last_page": 1,
        "from": 1,
        "to": 1
      }
    }
    ```

# 6. 获取国家代码接口

- API: state/code
- Method: GET
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

# 7. 获取短信验证码

- API: verify-code/sms
- Method: GET
- Login: No
- Params: 

    |Field          |Required   |Default|Example
    |---            |---        |---    |---
    |mobile         |Yes        |       |13541013333
    |for            |Yes        |       |1=注册，2=修改/重置密码

- Result:

    ```json
    {
        "code": 200,
        "message": "验证码获取成功"
    }
    ```

# 8. 修改/重置密码

- API: user/password
- Method: PUT    
- Login: No
- Params: 

    |Field          |Required   |Default|Example
    |---            |---        |---    |---
    |mobile         |Yes        |       |13541013333
    |verify_code    |Yes        |       |123456
    |password       |Yes        |       |111111

- Result:

    ```json
    {
      "code": 200,
      "message": "密码更新成功"
    }
    ```

# 9 修改用户资料

- API: user/profile
- Method: PUT
- Login: Yes
- Params: 

    |Field          |Required   |Default|Example
    |---            |---        |---    |---
    |...            |Yes        |       |字段与 见 [1. 用户注册](#1-用户注册) 相同

- Result: 见 [1. 用户注册](#1-用户注册)

    ```json
    {
      "code": 200,
      "message": "更新成功",
      "data": {
        "...": "..."
      }
    }
    ```

# 10. 获取其他用户资料

- API: user/{user_id}/profile
    
    > `{user_id}` 为变量，用户ID. 如， user_id=1， API=user/1/profile
    
- Method: GET
- Login: Yes
- Params: None
- Result: 见 [1. 用户注册](#1-用户注册)

    ```json
    {
      "code": 200,
      "message": "获取资料成功",
      "data": {
        "...": "...",
        "is_friend": 1
      }
    }
    ```
    
    **Fields**
    
    Field   |Description
    ---     |---
    is_friend|是否好友，0=否，1=是


# 11. 查询附近的人

- API: friends/nearby
- Method: GET
- Login: Yes
- Params: 

    |Field          |Required   |Default|Example
    |---            |---        |---    |---
    |longitude      |Yes        |       |经度
    |latitude       |Yes        |       |纬度
    |sex            |No         |       |female=女，male＝男
    |location_uploaded_at| No   |       |10位时间戳，检索大于该时间的记录
    |page           |No         |1      |
    |per_page       |No         |20     |

- Result:

    ```json
    {
        "code": 200,
        "message": "查询成功",
        "list": [
            {
                "mobile": "13541013373",
                "nickname": "Felix",
                "state_code": "+86",
                "sex": "female",
                "avatar": "",
                "city_name": "成都",
                "city_code": "5001",
                "account": "10000321",
                "it_says": "Hello world to you",
                "updated_at": 1498516570,
                "created_at": 1498516570,
                "id": 10,
                "im_account": "88950b12016202c8798ffe8d0bb46eea10",
                "im_password": "8f084ab9a91220498dd1da3cdc22c2c7",
                "age": 0,
                "address": "0",
                "distance": 11131
            }
        ],
        "pagination": {
            "total": 1,
            "per_page": 20,
            "current_page": 1,
            "last_page": 1,
            "from": 0,
            "to": 1
        }
    }
    ```

    **Fields**
    
    Field   |Description
    ---     |---
    distance|距离，单位:米

# 12. 上传用户地理位置

- API: user/coordinate
- Method: PUT
- Login: Yes
- Params: 

    |Field          |Required   |Default|Example
    |---            |---        |---    |---
    |longitude      |Yes        |       |经度
    |latitude       |Yes        |       |纬度

- Result:

    ```json
    {
        "code": 200,
        "message": "更新成功"
    }
    ```
    
# 13. 清空好友请求列表

- API: friends/requests

    > Notice: friend`s`,request`s` 都带`s`

- Method: DELETE
- Login: Yes
- Params: None
- Result:

    ```json
    {
        "code": 200,
        "message": "清空列表成功"
    }
    ```