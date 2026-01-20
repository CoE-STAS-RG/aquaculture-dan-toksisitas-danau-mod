<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $device_id
 * @property float|null $env_temperature
 * @property float|null $water_temperature
 * @property float|null $ph
 * @property float|null $dissolved_oxygen
 * @property float|null $risk_level
 * @property float|null $turbidity_ntu
 * @property float|null $ec_s_m
 * @property float|null $tds_ppm
 * @property float|null $orp_mv
 * @property string $reading_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read Device $device
 * 
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading query()
 */
class SensorReading extends Model
{
    use HasFactory;

    public $timestamps = true; 

    protected $table = 'sensor_readings';

    protected $fillable = [
        'device_id',
        'env_temperature',
        'water_temperature',
        'ph',
        'dissolved_oxygen',
        'risk_level',

        // Sensor baru
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