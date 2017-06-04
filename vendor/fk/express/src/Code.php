<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-02
 */

namespace fk\express;

class Code
{

    public static $names = [
        'anxindakuaixi' => '安信达',
        'auspost' => '澳大利亚(Australia Post)',
        'huitongkuaidi' => '百世快递',
        'youzhengguonei' => '邮政包裹/平邮',
        'bangsongwuliu' => '邦送物流',
        'dhl' => 'DHL-中国件',
        'datianwuliu' => '大田物流',
        'debangwuliu' => '德邦物流',
        'ems' => 'EMS',
        'emsguoji' => 'EMS-国际件',
        'rufengda' => '如风达',
        'fedex' => 'FedEx-国际件',
        'guotongkuaidi' => '国通快递',
        'gongsuda' => '共速达',
        'youzhengguoji' => '国际包裹',
        'tiandihuayu' => '天地华宇',
        'jiajiwuliu' => '佳吉快运',
        'jiayiwuliu' => '佳怡物流',
        'canpost' => '加拿大(Canada Post)',
        'kuaijiesudi' => '快捷速递',
        'longbanwuliu' => '龙邦速递',
        'lianbangkuaidi' => '联邦快递',
        'lianhaowuliu' => '联昊通',
        'ganzhongnengda' => '能达速递',
        'quanyikuaidi' => '全一快递',
        'quanfengkuaidi' => '全峰快递',
        'quanritongkuaidi' => '全日通',
        'shentong' => '申通快递',
        'shunfeng' => '顺丰速运',
        'suer' => '速尔快递',
        'tnt' => 'TNT',
        'tiantian' => '天天快递',
        'ups' => 'UPS',
        'usps' => 'USPS',
        'xinbangwuliu' => '新邦物流',
        'neweggozzo' => '新蛋奥硕',
        'hkpost' => '香港(HongKong Post)',
        'yuantong' => '圆通速递',
        'yunda' => '韵达快递',
        'youshuwuliu' => '优速物流',
        'zhongtong' => '中通快递',
        'zhongtiewuliu' => '中铁物流',
        'zhaijisong' => '宅急送',
        'zhongyouwuliu' => '中邮物流',
    ];

    public static function getName($code)
    {
        return static::$names[$code] ?? $code;
    }

    public static function all()
    {
        return array_keys(static::$names);
    }

}