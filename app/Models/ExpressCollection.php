<?php

namespace App\Models;

use fk\express\Code;
use fk\express\ExpressHelper;

/**
 * Fields in the table `express_collection`
 *
 * @property integer $id
 * @property string $code E.g. yuantong
 * @property string $short_code e.g. yt
 * @property string $name Chinese name for the company
 * @property string $short_name Short version of name
 * @property string $home_url Url of home page
 * @property string $contact_tel Contact telephone number
 * @property string $complaint_tel Contact telephone number
 * @property string $sn_regexp RegExp for the sn
 * @property string $sn_description RegExp for the sn
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 *
 */
class ExpressCollection extends Model
{

    const CREATED_BY = null;

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'express_collection';

    public function rules()
    {
        return [
            'code' => ['required', 'string', 'max:100'],
            'short_code' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100'],
            'short_name' => ['required', 'string', 'max:100'],
            'home_url' => ['required', 'string', 'max:255'],
            'contact_tel' => ['required', 'string', 'max:20'],
            'complaint_tel' => ['string', 'max:20'],
            'sn_regexp' => ['string', 'max:100'],
            'sn_description' => ['string', 'max:100'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
        ];
    }

    /**
     * @return bool|\Illuminate\Database\Eloquent\Model
     */
    public static function synchronize()
    {

        $codes = Code::all();
        static::truncate();
        /** @var static $model */
        foreach ($codes as $code) {
            $data = (new ExpressHelper())->queryCompanyByCode($code);
            $model = static::create([
                'code' => $data['number'],
                'short_code' => $data['shortNumber'],
                'name' => $data['name'],
                'short_name' => $data['shortName'],
                'home_url' => $data['siteUrl'],
                'complaint_tel' => $data['complainTel']??'',
                'contact_tel' => $data['contactTel'],
                'sn_regexp' => $data['checkReg']??'',
                'sn_description' => $data['checkRule']??'',
            ]);
            if ($model->hasErrors()) {
                return $model;
            }
        }
        return true;
    }

}