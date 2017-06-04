# 接口地址规则(举例)

HOST =  http://web.gasoline.mylord.cn/api/

API = user

接口地址 = http://web.gasoline.mylord.cn/api/user

# 接口 HOST
http://web.gasoline.mylord.cn/api/

# 请求凭证
`Login: Yes` 接口需要传在header中添加头信息`X-Access-Token`,
其值在`注册/登录接口`返回, 返回参数名 `access_token`

# Code

- `422` 客户端验证错误, 客户端需要将返回参数中的errors字段展示出来


接口
==========

## 1. 申请加油卡
- API: user
- Method: POST
- Params:

    ```
    |   Field       |Required   |Default|Example|
    |---            |---        |---    |---
    |mobile         |Y          |       | 13512345678
    |name           |Y          |       | 张三
    |idcard         |Y          |       | 512010102029473893
    |address        |Y          |       | 四川成都双流xxx路xx号
    |idcard_front   |Y          |       | <file>
    |idcard_back    |Y          |       | <file>
    |passowrd       |Y          |       | 123456
    |               |           |       |
    |platform       |N          | AliPay| AliPay=支付宝, WeChat=微信(暂不支持)
    |return_url     |N          |http://example.com/members.html|
    ```

- Result:

    ```json
    {
      "code": 200,
      "message": "请求成功.",
      "form": "<form>"
    }
    ```
客户端打印响应参数中的`form`进行页面跳转或调用支付宝APP, 用户输入账号密码完成支付

- 测试用户
    - `买家账号ID`  idjdtq0121@sandbox.com
    - `登录密码`    111111
    - `支付密码`    111111
    - `用户名称`    沙箱环境
    - `证件类型身份证`   IDENTITY_CARD
    - `证件号码`        91545119001116298X

## 2. 用户登录

- API: user
- Method: PUT
- Params:

    ```
    |   Field       |Required   |Default|Example
    |---            |---        |---    |---
    |mobile         |Y          |       | 13512345678
    |passowrd       |Y          |       | 123456
    ```

- Result:

    ```json
    {
        "code": 200,
        "data": {
          "id": 49,
          "name": "张三",
          "idcard": "1234123412341244",
          "mobile": "13541013546",
          "address": "四川成都",
          "gas_card_id": 153,
          "created_at": "2017-05-30 21:29:59",
          "updated_at": "2017-05-30 21:30:02",
          "deleted": 0,
          "gas_card_sn": "",
          "express_status": 1,
          "coupons": [
            {
              "coupon_id": 1,
              "coupon_num": 0
            }
          ]
        },
        "message": "Getting user profile successfully."
    }
    ```
    - express_status
        - `-1` 没有下单,或订单未支付
        - `0`  已下单,但没有物流单号
        - `1`  可以查询物流信息, 调用接口[快递查询](#5-快递查询)

## 3. 获取用户信息

- API: user
- Method: GET
- Login: Yes
- Params: None
- Result:

    ```json
    {
        "code": 200,
        "message": "获取用户信息成功",
        "data": {
            "id": 1,
            "...": "..."
        }
    }
    ```

## 4. 检查是否支付成功
- API: order/paid
- Method: GET
- Login: Yes
- Params

    ```
    |   Field       |Required   |Default|Example
    |---            |---        |---    |---
    |sn             |Y          |       | 100000000445497383974878
    ```

- Result:

    ```json
      {
        "code": 200,
        "message": "Order paid"
      }
    ```

- Codes:
    - `200` 已支付
    - `202` 订单不存在或未支付

## 5. 快递查询
- API: express
- Method: GET
- Login: Yes
- Params: None
- Result:

    ```json
      {
        "code": 200,
        "message": "Getting logistics info successfully.",
        "express_sn": "885014224469703308",
        "company": "yuantong",
        "trace": [
          {
            "time": "2017-05-11 17:52:35",
            "ftime": "2017-05-11 17:52:35",
            "context": "客户 签收人: 已签收，签收人凭取货码签收。 已签收  感谢使用圆通速递，期待再次为您服务",
            "location": null
          },
          {
            "time": "2017-05-09 22:47:50",
            "ftime": "2017-05-09 22:47:50",
            "context": "河北省石家庄市冶河公司(点击查询电话) 已揽收",
            "location": null
          }
        ]
      }
    ```

- Codes
    - `200` 查询成功
    - `202` 有订单信息,但是快递单号没有录入
    - `404` 没有订单信息,无法查物流

# 6. 帮麦同步积分接口
- API: indoor-buy/points
- Method: POST
- Params

    ```
    |   Field       |Required   |Default|Example
    |---            |---        |---    |---
    |current_points |Y          |       | 100
    |id             |Y          |       | 1, 帮麦用户ID
    ```

- Result

    ```json
    {
      "code": 200,
      "message": "积分同步成功"
    }
    ```
# 7. 统一支付接口

- API: pay
- Method: POST
- Login: Yes
- Params:

    ```
    |   Field       |Required   |Default|Example
    |---            |---        |---    |---
    |goods_id       |Y          |       | 1, 商品ID. 2=加油卡充值, 每件200
    |num            |Y          |       | 1, 购买数量.
    |coupon_id      |N          |       | 1, 优惠券ID.
    |coupon_num     |N          |       | 10, 优惠券数量.
    ```

- Result:

    ```json
    {
      "code": 200,
      "message": "生成支付订单成功",
      "info": "<form>"
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
