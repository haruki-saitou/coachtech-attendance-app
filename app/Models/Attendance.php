<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Attendance extends Model
{
    use HasFactory;

    const LEGAL_REST_OVER_8H = 3600; // 8時間超の法定最低休憩時間 = 1時間（3600秒）
    const LEGAL_REST_OVER_6H = 2700; // 6時間超の法定最低休憩時間 = 45分（2700秒）

    protected $fillable = [
        'user_id',
        'status',
        'check_in_at',
        'check_out_at',
        'comment',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    // リレーションシップ: ユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // リレーションシップ: 休憩
    public function rests()
    {
        return $this->hasMany(Rest::class);
    }

    // リレーションシップ: 勤怠修正申請
    public function attendanceCorrect()
    {
        return $this->hasOne(AttendanceCorrect::class);
    }

    // アクセサ: 休憩中かどうか
    public function getIsRestingAttribute()
    {
        return $this->rests->whereNull('end_at')->isNotEmpty();
    }

    // アクセサ: 総休憩時間（秒）, 実測値と法定最低休憩時間の大きい方
    public function getTotalRestTimeAttribute()
    {
        $actualRestTime = $this->rests->reduce(function ($carry, $rest) {
            if ($rest->start_at && $rest->end_at) {
                return $carry + $rest->start_at->diffInSeconds($rest->end_at);
            }
            return $carry;
        }, 0);

        if (!$this->check_in_at || !$this->check_out_at) {
            return $actualRestTime;
        }

        $stayTime = $this->check_in_at->diffInSeconds($this->check_out_at);

        $minimumRestTime = 0;
        if ($stayTime > 8 * 3600) {
            $minimumRestTime = self::LEGAL_REST_OVER_8H;
        } elseif ($stayTime > 6 * 3600) {
            $minimumRestTime = self::LEGAL_REST_OVER_6H;
        }
        return max($actualRestTime, $minimumRestTime);
    }

    // アクセサ: 総労働時間（秒）、滞在時間から総休憩時間を引いたもの
    public function getTotalWorkedTimeAttribute()
    {
        if (!$this->check_in_at || !$this->check_out_at) {
            return 0;
        }
        $stayTime = $this->check_in_at->diffInSeconds($this->check_out_at);

        return max(0, $stayTime - $this->total_rest_time);
    }

    // フォーマット済みの総労働時間（HH:MM形式）
    public function getFormattedTotalWorkedTimeAttribute()
    {
        $seconds = $this->total_worked_time;
        return sprintf('%02d:%02d', floor($seconds / 3600), floor(($seconds % 3600) / 60));
    }

    // フォーマット済みの総休憩時間（HH:MM形式）
    public function getFormattedTotalRestTimeAttribute()
    {
        $seconds = $this->total_rest_time;
        return sprintf('%02d:%02d', floor($seconds / 3600), floor(($seconds % 3600) / 60));
    }
}