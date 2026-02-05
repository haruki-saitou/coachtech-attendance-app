<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceCorrect extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'attendance_id',
        'updated_check_in_at',
        'updated_check_out_at',
        'updated_comment',
        'updated_rests',
    ];
    protected $casts = [
        'updated_check_in_at' => 'datetime',
        'updated_check_out_at' => 'datetime',
        'updated_rests' => 'array',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}