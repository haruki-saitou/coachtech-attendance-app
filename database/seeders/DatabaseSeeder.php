<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\AttendanceCorrect;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. 管理者を作る（ログイン用）
        User::create([
            'name' => '管理者太郎',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'email_verified_at' => now(),
        ]);

        // 2. 1月の1ヶ月分のデータを作る（ループで自動作成）
        $startDate = Carbon::create(2025, 12,1);
        $endDate = Carbon::create(2026,1,31);
        $totalDays = $startDate->diffInDays($endDate)+1;

        for ($s = 1; $s <= 10; $s++) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => "staff{$s}@example.com",
                'password' => Hash::make('password'),
                'role' => 0,
                'email_verified_at' => now(),
            ]);


            for ($i = 0; $i < $totalDays; $i++) {
                $currentDate = $startDate->copy()->addDays($i);

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

                if (rand(1,10) === 1) {
                    $originalRests = $attendance->rests->map(function ($rest) {
                        return [
                            'start_at' => $rest->start_at->format('H:i'),
                            'end_at' => $rest->end_at->format   ('H:i'),
                        ];
                    })->toArray();

                    AttendanceCorrect::create([
                        'attendance_id' => $attendance->id,
                        'updated_check_in_at' => $attendance->check_in_at->copy()->setTime(9, 30, 0),
                        'updated_check_out_at' => $attendance->check_out_at->copy()->setTime(18, 30, 0),
                        'updated_rests' => $originalRests,
                        'updated_comment' => '電車遅延のため修正お願いします。',
                        'created_at' => $currentDate->copy()->setTime(19, 30, 0),
                        'updated_at' => $currentDate->copy()->setTime(19, 30, 0),
                    ]);
                    $attendance->update([
                        'status' => '承認待ち'
                    ]);
                }
            }
        }
    }
}
