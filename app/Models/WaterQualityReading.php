<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterQualityReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'turbidity_ntu',
        'ec_s_m',
        'tds_ppm',
        'orp_mv',
        'reading_time'
    ];

    protected $casts = [
        'reading_time' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}