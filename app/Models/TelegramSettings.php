<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramSettings extends Model
{
    protected $fillable = ['chat_id', 'use_proxy', 'http_proxy'];

    protected $attributes = ['use_proxy' => false];

    protected function casts(): array
    {
        return ['use_proxy' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $settings): void {
            $settings->setAttribute('id', 1);
        });
    }

    public static function singleton(): self
    {
        $settings = static::query()->find(1);

        if ($settings !== null) {
            return $settings;
        }

        $settings = new static(['use_proxy' => false]);
        $settings->id = 1;
        $settings->save();

        return $settings;
    }
}
