<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. テスト用のユーザーを作る（ログイン用）
        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // 2. 1月の1ヶ月分のデータを作る（プロの怠惰：ループで自動作成）
        $date = Carbon::create(2026, 1, 1);
        $daysInMonth = $date->daysInMonth;

        for ($i = 0; $i < $daysInMonth; $i++) {
            $currentDate = $date->copy()->addDays($i);

            // 土日はスキップ（weekendを自動判別）
            if ($currentDate->isWeekend()) {
                continue;
            }

            // 出勤データ
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'status' => '退勤済',
                'check_in_at' => $currentDate->copy()->setTime(9, 0, 0),
                'check_out_at' => $currentDate->copy()->setTime(18, 0, 0),
            ]);

            // 休憩データ（1日2回分）
            Rest::create([
                'attendance_id' => $attendance->id,
                'start_at' => $currentDate->copy()->setTime(12, 0, 0),
                'end_at' => $currentDate->copy()->setTime(13, 0, 0),
            ]);

            Rest::create([
                'attendance_id' => $attendance->id,
                'start_at' => $currentDate->copy()->setTime(16, 0, 0),
                'end_at' => $currentDate->copy()->setTime(16, 15, 0),
            ]);
        }
    }
}
