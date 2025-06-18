<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishFeeding extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fish_name',
        'feed_type',
        'feeding_time',
        'feed_weight',
        'fish_weight',
        'fish_count',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
