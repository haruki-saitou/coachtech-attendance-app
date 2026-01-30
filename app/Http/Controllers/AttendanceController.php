<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\AttendanceCorrect;

class AttendanceController extends Controller
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
    public function start_attendance(Request $request)
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
    public function end_attendance(Request $request)
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

        return redirect()->route('attendance.top');
    }

    public function attendance_list(Request $request)
    {
        $user = Auth::user();

        $month = $request->query('month', today()->format('Y-m'));
        $date = Carbon::parse($month);

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('check_in_at', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
            ->with('rests')
            ->orderBy('check_in_at', 'asc')
            ->get();

        $prev_month = $date->copy()->subMonth()->format('Y-m');
        $next_month = $date->copy()->addMonth()->format('Y-m');
        return view('staff.attendance_list', compact('attendances', 'date', 'prev_month', 'next_month'));
    }

    public function attendance_detail($id)
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('id', $id)
            ->with('rests')
            ->firstOrFail();

        $attendanceCorrect = AttendanceCorrect::where('attendance_id', $attendance->id)->latest()->first();

        return view('staff.attendance_detail', compact('attendance', 'user', 'attendanceCorrect'));
    }

    public function attendance_detail_update(Request $request, $id)
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

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
                'end_at' => isset($rest_ends[$index]) ? Carbon::parse($date . ' ' . $rest_ends[$index])->toDateTimeString() : null,
            ];
        }
        AttendanceCorrect::create([
            'attendance_id' => $attendance->id,
            'updated_check_in_at' => $new_check_in,
            'updated_check_out_at' => $new_check_out,
            'updated_comment' => $request->input('comment'),
            'updated_rests' => $updated_rests,
        ]);

        $attendance->update(['status' => '承認待ち']);

        return redirect()->route('attendance.detail', ['id' => $attendance->id])->with('success', '修正依頼を申請しました。');
    }

    public function stamp_list()
    {
        $user = Auth::user();
        $tab = request()->query('tab', 'pending');
        $query = Attendance::where('user_id', $user->id)->has('attendanceCorrect');
        if ($tab === 'approved') {
            $query->where('status', '承認済み');
        } else {
            $query->where('status', '承認待ち');
        }
        $correct_requests = $query->orderBy('updated_at', 'asc')->get();
        return view('staff.stamp_list', compact('correct_requests', 'tab'));
    }
}
