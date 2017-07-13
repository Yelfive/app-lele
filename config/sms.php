<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-01
 */

return [
    'default' => 'YunPian',
    'AliCloud' => [
        'sender' => \fk\messenger\Sender\AliCloud::class,
        'endPoint' => 'https://1082184838736512.mns.cn-hangzhou.aliyuncs.com/',
        'accessId' => 'LTAIonRHBHXhYRDz',
        'accessKey' => 'MYu3tUs4tYIMao0fJIdOcN1zRg8ZK4',
        'topic' => 'sms.topic-cn-hangzhou',
        'signature' => '乐乐'
    ],
    'AliDaYu' => [
        'sender' => \fk\messenger\Sender\AliDaYu::class,
        'appKey' => '24226067',
        'secretKey' => '9ce776e8d7121bee4a386fbb2750d663',
        'signature' => '乐乐',
        'logPath' => storage_path('sms'),
    ],
    'YunPian' => [
        'sender' => \fk\messenger\Sender\YunPian::class,
        'app' => '乐聊',
        'signature' => '乐聊交友',
        'apiKey' => '3aae4db80d9a52ca6def01795669874f',
    ],
];