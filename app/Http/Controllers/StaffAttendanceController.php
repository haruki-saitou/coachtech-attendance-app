<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrect;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Requests\UpdateAttendanceRequest;


class StaffAttendanceController extends Controller
{
    // 勤怠画面表示
    public function attendance_top()
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_at', today())->first();

        return view('staff.attendance', compact('user', 'attendance'));
    }

    // 出勤処理
    public function start_attendance()
    {
        $now = now();
        $user = Auth::user();

        $exists = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_at', $now->today())->exists();

        if ($exists) {
            return redirect()->back()->with('error', '既に出勤しています。');
        }

        Attendance::create([
            'user_id' => $user->id,
            'status' => '出勤中',
            'check_in_at' => $now,
        ]);

        return redirect()->route('attendance.top');
    }
    // 退勤処理
    public function end_attendance()
    {
        $now = now();
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_at', $now->today())
            ->whereNull('check_out_at')
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', '出勤していません。');
        }

        if ($attendance->is_resting) {
            $attendance->rests()->whereNull('end_at')->update([
                'end_at' => $now,
            ]);
        }

        $attendance->update([
            'check_out_at' => $now,
            'status' => '退勤済'
        ]);

        return redirect()->route('attendance.top')->with('is_completed', true);
    }

    // 勤怠一覧
    public function attendance_list(Request $request)
    {
        $user = Auth::user();

        $month = $request->query('month', today()->format('Y-m'));
        $date = Carbon::parse($month);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $attendanceDate = Attendance::where('user_id', $user->id)
            ->whereBetween('check_in_at', [$startOfMonth, $endOfMonth])
            ->with('rests')
            ->get()
            ->keyBy(function($item) {
                return $item->check_in_at->format('Y-m-d');
            });
        $dates = [];
        for($d = $startOfMonth->copy(); $d->lte($endOfMonth); $d->addDay()) {
            $dateStr = $d->format('Y-m-d');

            $dates[] = [
                'date' => $d->copy(),
                'attendance' => $attendanceDate->get($dateStr)
            ];
        }

        $prev_month = $date->copy()->subMonth()->format('Y-m');
        $next_month = $date->copy()->addMonth()->format('Y-m');
        return view('staff.attendance_list', compact('date', 'dates', 'prev_month', 'next_month'));
    }

    // 勤怠詳細
    public function attendance_detail_update(UpdateAttendanceRequest $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Attendance::query();
        if (!$user->can('admin')) {
            $query->where('user_id', $user->id);
        }
        $attendance = $query->where('id', $id)->first();

        if (!$attendance) {
            abort(403);
        }

        $date = $attendance->check_in_at->format('Y-m-d');
        $new_check_in = Carbon::parse($date . ' ' . ($request->input('check_in_at') ?: $attendance->check_in_at->format('H:i')));
        $new_check_out = $request->input('check_out_at') ? Carbon::parse($date . ' ' . $request->input('check_out_at')) : ($attendance->check_out_at ? Carbon::parse($date . ' ' . $attendance->check_out_at->format('H:i')) : null);

        $updated_rests = [];
        $rest_starts = $request->input('rest_start', []);
        $rest_ends = $request->input('rest_end', []);
        foreach ($rest_starts as $index => $start) {
            if (!$start)
                continue;
            $updated_rests[] = [
                'start_at' => Carbon::parse($date . ' ' . $start)->toDateTimeString(),
                'end_at' => (!empty($rest_ends[$index])) ? Carbon::parse($date . ' ' . $rest_ends[$index])->toDateTimeString() : null,
            ];
        }
        if ($user->can('admin')) {
            $attendance->update([
                'status' => '承認済み',
                'check_in_at' => $new_check_in,
                'check_out_at' => $new_check_out,
                'comment' => $request->input('comment'),
            ]);
            $attendance->rests()->delete();
            foreach ($updated_rests as $rest) {
                $attendance->rests()->create([
                    'start_at' => $rest['start_at'],
                    'end_at' => $rest['end_at'],
                ]);
            }
            $status_message = '勤怠情報を修正しました。';
        }else{
            AttendanceCorrect::create([
            'attendance_id' => $attendance->id,
            'updated_check_in_at' => $new_check_in,
            'updated_check_out_at' => $new_check_out,
            'updated_comment' => $request->input('comment'),
            'updated_rests' => $updated_rests,
        ]);
            $attendance->update([
                'status' => '承認待ち',
            ]);
            $status_message = '修正依頼を申請しました。';
        }
        return redirect()->route('attendance.detail', ['id' => $attendance->id])->with('status', $status_message);
    }
}
