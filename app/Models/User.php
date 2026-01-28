<?php
namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'photo' // tambahkan ini
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function sensorReadings()
    {
        return $this->hasManyThrough(
            SensorReading::class,  // Model tujuan
            Device::class,         // Model perantara
            'user_id',             // Foreign key di tabel devices
            'device_id',           // Foreign key di tabel sensor_readings
            'id',                  // Local key di tabel users
            'id'                   // Local key di tabel devices
        )->select(
            'sensor_readings.*',   // Ambil semua kolom dari sensor_readings
            'devices.id as device_table_id' // Tambah alias untuk devices.id
        );
    }
}
