<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Rest;
use App\Models\AttendanceCorrect;

class AdminAttendanceController extends Controller
{
/**
* 全スタッフの修正申請を一覧で出す（管理者用）
*/

    public function admin_attendance_list(Request $request)
    {
        $date = Carbon::parse($request->query('date', today()->format('Y-m-d')));
        $staffIds = User::where('role', '0')->pluck('id');
        $attendances = Attendance::whereIn('user_id', $staffIds)
            ->whereDate('check_in_at', $date)
            ->with('user', 'rests')
            ->orderBy('check_in_at', 'asc')
            ->get();
        $prev_date = $date->copy()->subDay()->format('Y-m-d');
        $next_date = $date->copy()->addDay()->format('Y-m-d');
        return view('admin.attendance_list', compact('attendances', 'date', 'prev_date', 'next_date'));
    }

    public function staff_list()
    {
        $users = User::where('role', '0')->get();
        return view('admin.staff_list', compact('users'));
    }

    public function staff_attendance_list($id, Request $request)
    {
        $user = User::findOrFail($id);
        $month = $request->query('month', today()->format('Y-m'));
        $date = Carbon::parse($month);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $attendanceDate = Attendance::where('user_id', $id)
            ->whereBetween('check_in_at', [$startOfMonth, $endOfMonth])
            ->with('rests')
            ->orderBy('check_in_at', 'asc')
            ->get()->keyBy(fn ($item) => $item->check_in_at->format('Y-m-d'));
        $dates = [];
        for ($d = $startOfMonth->copy(); $d->lte($endOfMonth); $d->addDay()) {
            $dates[] = [
                'date' => $d->copy(),
                'attendance' => $attendanceDate->get($d->format('Y-m-d')),
            ];
        }
        $prev_month = $date->copy()->subMonth()->format('Y-m');
        $next_month = $date->copy()->addMonth()->format('Y-m');
        return view('admin.staff_attendance_list', compact('user', 'dates', 'date', 'prev_month', 'next_month'));
    }
    public function approve_correction_request(Request $request)
    {
        $tab = $request->query('tab', 'pending');
        $query = ($tab ==='approved')
        ? AttendanceCorrect::onlyTrashed()
        : AttendanceCorrect::query();

        $correct_requests = $query->with(['attendance.user'])
        ->join('attendances', 'attendance_corrects.attendance_id', '=', 'attendances.id')
        ->select('attendance_corrects.*')
        ->orderBy('attendances.check_in_at', 'asc')
        ->get();

        // 管理者専用の blade を表示
        return view('admin.stamp_list', compact('correct_requests', 'tab'));
    }

    public function approve_attendance(Request $request, $attendance_correct_request_id)
    {
        $attendance = Attendance::with('attendanceCorrect')->findOrFail($attendance_correct_request_id);
        $correct = $attendance->attendanceCorrect;
        if ($correct) {
            $attendance->update([
                'check_in_at' => $correct->updated_check_in_at,
                'check_out_at' => $correct->updated_check_out_at,
                'comment' => $correct->updated_comment,
                'status' => '承認済み',
            ]);

            if ($correct->updated_rests) {
                $attendance->rests()->delete();
                foreach ($correct->updated_rests as $rest) {
                    $attendance->rests()->create([
                        'start_at' => $rest['start_at'],
                        'end_at' => $rest['end_at'],
                    ]);
                }
            }
            $correct->delete();
        } else {
            $attendance->update(['status' => '承認済み']);
        }
        return back()->with('status', '承認しました');
    }

    public function export_csv($id)
    {
        $user = User::findOrFail($id);
        $attendances = Attendance::where('user_id', $id)
            ->with('rests')
            ->orderBy('check_in_at', 'asc')
            ->get();
        $csvData = [];
        $csvData[] = ['日付', '出勤', '退勤', '休憩', '合計'];
        foreach ($attendances as $attendance) {
            $csvData[] = [
                $attendance->check_in_at->format('Y/m/d'),
                $attendance->check_in_at->format('H:i'),
                $attendance->check_out_at ? $attendance->check_out_at->format('H:i') : '-',
                $attendance->formatted_total_rest_time,
                $attendance->formatted_total_worked_time,
            ];
        }
        $fileName = $user->name . '_出勤履歴.csv';
        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
        ]);
    }
}