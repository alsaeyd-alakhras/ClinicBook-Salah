<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        $value = $setting->value;
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_string($value) && self::isJson($value)) {
            return json_decode($value, true);
        }

        return $value;
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string) $value]
        );
    }

    private static function isJson(string $value): bool
    {
        json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
