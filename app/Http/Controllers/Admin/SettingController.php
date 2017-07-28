<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-26
 */

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use fk\utility\Http\Request;

class SettingController
{
    public function display()
    {
        $settings = $this->loadSettings();
        return view('setting.index', [
            'settings' => $settings,
        ]);
    }

    protected function loadSettings()
    {
        return [
            ['app_name', '应用名称', Setting::retrieve('app_name'), '平台发送短信时候，提示的应用名称，如：洛洛'],
            ['sms_signature', '短信签名', Setting::retrieve('sms_signature'), '平台向用户发布短信时，短信内容的签名. <span style="color: #ff727c">修改该配置前，请先到短信运营商处修改短信签名</span>'],
        ];
    }

    public function save(Request $request)
    {
        $settings = $request->input('Settings');

        foreach ($settings as $code => $value) {
            Setting::store($code, $value);
        }
        return redirect()->to('/admin/settings');
    }
}