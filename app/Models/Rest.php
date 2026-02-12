<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Attendance;

class Rest extends Model
{
    use HasFactory;
    protected $fillable = [
        'attendance_id',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    // リレーションシップ: 勤怠
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
