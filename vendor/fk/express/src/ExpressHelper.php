<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-16
 */

namespace fk\express;

use Curl\Curl;

class ExpressHelper
{

    const STATUS_SIGNED = 3;

    protected $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    public function query($postSn, $expressCompanyCode = '')
    {
        $names = [];
        if ($expressCompanyCode) $names = [$expressCompanyCode];
        foreach ($this->getExpressNamesBySn($postSn) as $info) {
            $names[] = $info['comCode'];
        }
        foreach ($names as $code) {
            $data = $this->tryQuerying($postSn, $code);
            if ($data) {
                $data['com_name'] = $this->queryCompanyByCode($code);
                break;
            }
        }

        return empty($data) ? [] : $data;
    }

    public function queryCompanyByCode($code)
    {
        $name = Code::getName($code);
        if ($name === $code) {
            $url = "https://m.kuaidi100.com/company.do";
            $data = [
                'method' => 'companyjs',
                'number' => $code,
            ];
            $a = $this->curl->post($url, $data);

            $response = json_decode($a->response, true);
            if (!$response) return $code;

            $name = $response['name'];
        }
        return $name;
    }

    protected function tryQuerying($postSn, $expressCompanyCode): array
    {
        $url = "http://www.kuaidi100.com/query?type=$expressCompanyCode&postid=$postSn&id=1&valicode=&temp=0.09561791346856974";

        $response = $this->curl->get($url)->response;
        return $response ? json_decode($response, true) : [];
    }

    protected function getExpressNamesBySn($sn)
    {
        $url = "http://www.kuaidi100.com/autonumber/autoComNum?text=$sn";
        $this->curl->get($url);
        $response = $this->curl->response;
        $data = json_decode($response, true);
        return $data['auto'] ?? [];
    }

    /**
     * @return static
     */
    public static function instance()
    {
        return new static;
    }

}