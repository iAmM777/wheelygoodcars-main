<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_plate',
        'brand',
        'model',
        'price',
        'mileage',
        'seats',
        'doors',
        'production_year',
        'weight',
        'color',
        'views_today',
        'views_today_date',
        'image',
        'sold_at',
        'views',
        'views_today',
        'views_today_date',
        'views_today_date' => 'date',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sold_at' => 'datetime',
        'views_today_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
