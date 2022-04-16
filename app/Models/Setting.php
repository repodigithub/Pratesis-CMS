<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    public $fillable = ['key', 'value'];

    public static function set($key, $value)
    {
        return self::updateOrCreate(
            [
                'key' => $key
            ],
            [
                'value' => $value
            ]
        )->value;
    }

    public static function get($key)
    {
        return self::firstOrCreate(['key' => $key])->value;
    }
}
