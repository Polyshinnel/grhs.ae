<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSettings extends Model
{
    protected $fillable = [
        'address', 'phone', 'email', 'map_latitude', 'map_longitude', 'social_links',
    ];

    protected function casts(): array
    {
        return [
            'map_latitude' => 'decimal:7',
            'map_longitude' => 'decimal:7',
            'social_links' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $settings): void {
            $settings->setAttribute('id', 1);
        });
    }

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'address' => 'Office 2203, 22nd floor, Ontario Tower, Business Bay, Dubai, UAE',
            'phone' => '+971 58 533 8524',
            'email' => 'sales@grhs.ae',
            'map_latitude' => 25.1858999,
            'map_longitude' => 55.2621375,
            'social_links' => [
                ['platform' => 'whatsapp', 'text' => '+971 58 533 8524', 'url' => 'https://wa.me/971585338524'],
                ['platform' => 'instagram', 'text' => '@grhs_uae', 'url' => 'https://www.instagram.com/grhs_uae/'],
            ],
        ];
    }

    public static function singleton(): self
    {
        return static::query()->firstOrCreate(['id' => 1], static::defaults());
    }
}
