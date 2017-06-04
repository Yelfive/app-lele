# Usage

```php
<?php

use fk\express\ExpressHelper;

ExpressHelper::instance()->query('Your order id', 'optional your express company code');
```

Results:

```json

{
    "message": "ok",
    "nu": "885071276722103391",
    "ischeck": "1",
    "condition": "F00",
    "com": "yuantong",
    "status": "200",
    "state": "3",
    "data": [
      {
        "time": "2017-05-16 19:47:24",
        "ftime": "2017-05-16 19:47:24",
        "context": "客户 签收人: 丰巢 已签收  感谢使用圆通速递，期待再次为您服务",
        "location": null
      },
      {
        "time": "2017-05-16 17:23:45",
        "ftime": "2017-05-16 17:23:45",
        "context": "快件已被SC富顿中心丰巢【自提柜】代收，请及时取件。有问题请联系派件员18606719395",
        "location": null
      },
      {
        "time": "2017-05-16 14:32:19",
        "ftime": "2017-05-16 14:32:19",
        "context": "四川省成都市武侯新城公司(点击查询电话)陈** 派件中 派件员电话18606719395",
        "location": null
      },
      {
        "time": "2017-05-16 13:09:26",
        "ftime": "2017-05-16 13:09:26",
        "context": "四川省成都市武侯新城公司 已收入",
        "location": null
      },
      {
        "time": "2017-05-16 10:55:14",
        "ftime": "2017-05-16 10:55:14",
        "context": "成都转运中心 已发出,下一站 四川省成都市武侯新城",
        "location": null
      },
      {
        "time": "2017-05-16 10:32:26",
        "ftime": "2017-05-16 10:32:26",
        "context": "成都转运中心 已收入",
        "location": null
      },
      {
        "time": "2017-05-14 21:51:39",
        "ftime": "2017-05-14 21:51:39",
        "context": "杭州转运中心 已发出,下一站 成都转运中心",
        "location": null
      },
      {
        "time": "2017-05-14 21:47:48",
        "ftime": "2017-05-14 21:47:48",
        "context": "杭州转运中心 已打包",
        "location": null
      },
      {
        "time": "2017-05-14 21:39:22",
        "ftime": "2017-05-14 21:39:22",
        "context": "杭州转运中心 已收入",
        "location": null
      },
      {
        "time": "2017-05-14 21:34:43",
        "ftime": "2017-05-14 21:34:43",
        "context": "杭州转运中心 已收入",
        "location": null
      },
      {
        "time": "2017-05-14 19:41:59",
        "ftime": "2017-05-14 19:41:59",
        "context": "浙江省杭州市秋涛路公司 已发出,下一站 杭州转运中心",
        "location": null
      },
      {
        "time": "2017-05-14 19:37:26",
        "ftime": "2017-05-14 19:37:26",
        "context": "浙江省杭州市秋涛路公司 已打包",
        "location": null
      },
      {
        "time": "2017-05-14 19:37:07",
        "ftime": "2017-05-14 19:37:07",
        "context": "浙江省杭州市秋涛路公司(点击查询电话) 已揽收",
        "location": null
      },
      {
        "time": "2017-05-14 18:40:57",
        "ftime": "2017-05-14 18:40:57",
        "context": "浙江省杭州市秋涛路公司 取件人: 林立勇 已收件",
        "location": null
      }
    ]
  }
```