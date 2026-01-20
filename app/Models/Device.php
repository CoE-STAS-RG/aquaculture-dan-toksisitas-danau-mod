<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $device_code
 * @property string $name
 * @property int $user_id
 * @property string|null $description
 * @property string|null $location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SensorReading> $readings
 * @property-read int|null $readings_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereDeviceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Device whereUserId($value)
 * @mixin \Eloquent
 */
class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_code',
        'name',
        'user_id',
        'description',
        'location'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function readings()
    {
        return $this->hasMany(SensorReading::class);
    }

        public function sensorReadings()
    {
        return $this->hasMany(SensorReading::class, 'device_id', 'id');
    }
}