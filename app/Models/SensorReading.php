<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $device_id
 * @property float $temperature
 * @property float $ph
 * @property float $dissolved_oxygen
 * @property float $risk_level
 * @property string $reading_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SensorReading> $device
 * @property-read int|null $device_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading whereDissolvedOxygen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading wherePh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading whereReadingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading whereRiskLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading whereTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensorReading whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SensorReading extends Model
{
    use HasFactory;
    public $timestamps = 'true' ; // <--- tambahkan ini
    protected $fillable = [
        'device_id',
        'env_temperature',
        'water_temperature',
        'ph',
        'dissolved_oxygen',
        'risk_level'
    ];
    

    public function device()
    {
        return $this->belongsTo(Device::class); // Ini yang benar
    }
}