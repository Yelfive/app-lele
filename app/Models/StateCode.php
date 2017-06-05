<?php

namespace App\Models;

/**
 * Fields in the table `state_code`
 *
 * @property integer $id 
 * @property string $name State name
 * @property string $code State code
 *
 *
 */
class StateCode extends Model 
{

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'state_code';

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20'],
        ];
    }

}