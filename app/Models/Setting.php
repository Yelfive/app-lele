<?php

namespace App\Models;

/**
 * Fields in the table `setting`
 *
 * @property string $code
 * @property string $json
 * @property string $setting
 *
 *
 */
class Setting extends Model
{

    const JSON_YES = 'yes';
    const JSON_NO = 'no';

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'setting';

    public function rules()
    {
        return [
            'code' => ['required', 'string', 'max:255'],
            'json' => ['required', 'string'],
            'setting' => ['required'],
        ];
    }

    public static function retrieve($code, $default = null)
    {
        /** @var static $model */
        $model = static::where('code', $code)->first();
        if (!$model) return $default;

        return $model->json === static::JSON_YES
            ? json_decode($model->setting, true)
            : $model->setting;
    }

    public static function store($code, $value)
    {
        if (!is_scalar($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            $json = static::JSON_YES;
        } else {
            $json = static::JSON_NO;
        }

        return static::updateOrInsert([
            'code' => $code,
            'json' => $json,
            'setting' => $value,
        ]);
    }

}