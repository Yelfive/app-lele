<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-25
 */

return [
    // notify url :  http://api.gasoline.mylord.cn/notify/ali-pay.php
    'notifyPath' => 'http://api.gasoline.mylord.cn/notify',
    'platforms' => [
        'AliPay' => [
            'redirectWithHtml' => true, // false to retrieve a redirect url from AliPay
            // 应用ID,您的APPID。
            'app_id' => "2016080400168339",
            // 商户私钥，您的原始格式RSA私钥,**应用私钥**
            'merchant_private_key' => "MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCfG20KxgrmGQVHWZvHnZJveWt6eMmmX4Ybtc9hBSqD/lnUj/FUuq4+63uu4eZRoOMrFj6H49zy8kViUInHxcJzgnrsAftmXb9lyinjMLdm6gJbdWdX1OOtA2amc/2+ipLL9JDUvRTyytkzuCJDwcBv6BbZaf5EoXGum20CSZjuacVM5GrgGbM+L9VXEZUByVuhkCn8mNO2izJpxz2Ky7yUFIMI7fVhzJ/nGh7KYTWl14oQTVOnptmKk8UAQbxsm/o2uL5mD91S/qdJo3tLTgCldIN5vtqVUnjpJvBpGGrbpniyT4tLEaEYET57HjGNlBbEl9pw2mkPoKqmkkFNgVvpAgMBAAECggEAWzyx6cSpCUt9wG19LBmKmEvrOv66u6H5WDiIo9z/hVM8ley4+dpXAxOsCBvYJCT7P4Dw+KHM2K/e48LFT35yxCQlcuKsFAZcM/Wa0YHlGanFEkcfrLkSmeriWT8YWcRC65vh1Wxb7+IsbUEAvRQE0ucm+yORwC9H7lWITBCCFuw+s7G6pnyP8bzAnfJvBDamqopPb+WI+EmzbQYYn3Z7TmSGR2UpXpgBhot0nvH0vxhJdWf7dqd6pOj32C/0h0AeNkOeCKzQk2WJ7+AhcL9Izi98KjAO6ggrK6FvKdSEEgiBz3KfrpQGU7dhBO7TttsAOZKEMaOxQhdMkyMQcfzfdQKBgQDQylQRPLvpX/itgnEMSt0z8JcyLgUBXpsen4E0lxJQqNPNpyZkfPvX4AoR56z12JbmKBHQ+X6uWEc2ZAZb93OWYyayTkx2tUClx9mBEQagubZgYiVfCkXqoLQJuGHBRQZk73YiKeZkr8/R3dqVuce3P/+eYvLWB8YBoBWrgY43YwKBgQDDFTjnG3kn8iR5jH62g4IUg6xXKoMA/HSc0/vea9T6PDGMH1wp3VxYBY6wsllTTBdA/i11UxTck7upZNFs9Mg48wy4vyCPNQjvYmYnJz+Civac0vJ5JswGWFSHLZepdIjLhUsipRkVL4/KcKWYH7E2m7dYq5akaNGI1fzulxK/QwKBgFwf+koEx1EpBHvio2juG35QeRYuEg1RphY0APmocu9eHt6OEzWhpCnTc/4EgF+VqrdxFLNlcs0QrJNGPTwVCk5f/3ILdFeokBbWAJWfYpJGfz817xFpYgIt2GK1lYSGpVSiCDj4zey9WnBaeJk5+PJVbb+AH2/qzCZpcUZUiuDLAoGAAmJjBKgYTf2upnGqJs7qqxeE5rfVTBDJ2BTbMje9LaXIsT684KIc+9JtkQlNADCg5CF3KsTuL1f65jIeQSeovtFxqVxkqHtsdYiNWRMouo66PuZxIjvwKG+x8MkE3oRXG62wYmcELHcdWcsQqxBQGSvEDOhj+fRyAysiYUQlbYMCgYAYF8crDnO02VV+AnmaRunWiVeAsMywFoiu246yird70GKirnrHUgcn9bn7QSLroG9GmM4SgY4hdyxqsZ1wqvvdwArjumebojXWl9OfUXFSurkW+BlXZ6lXLjuSy0EjCehxLgJJFSfFgTSI0tuqSs+GBSazrmgtQGrr2YHieEQZqw==",
            // 异步通知地址
            'notify_url' => "http://工程公网访问地址/alipay.trade.wap.pay-PHP-UTF-8/notify_url.php",
            // 同步跳转
            'return_url' => "http://web.gasoline.mylord.cn/members.html",
            // 编码格式
            'charset' => "UTF-8",
            // 签名方式
            'sign_type' => "RSA2",
            // 支付宝网关
            'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",
            // **支付宝公钥**,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArvRCubpqNQhN4vLqOMxhiFdI0/YkftjwQvpysq1R1KLiHOnmNsGLDJ5RlT6uXAAitsWolXHizrqOisgvdnJktQ2HSuu104Fz3l3zAe1NxFejAGNVI4WxLlkAPqPkuyELGnNZGNlKHisbNi5pGN4vmsHLsEpFwCS0gO295xf+7NSJTNnj7W7gv6l6CtcJYmctLJy7DV81CHHw/VmpW2abEWTc1OXBgku90e+XJutpNSWTL+KGgjs1cdH6vsKG4FzcxWeozkqbBjWZVL/qMZBEQkG2+2AxzwLOAmVaVskgXUVhf8MoYe7PgA4M65rrm8QTwRGOAU//uoRuU4Dl8Px88QIDAQAB",
        ],
    ],
];