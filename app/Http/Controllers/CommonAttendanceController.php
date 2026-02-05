<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrect;
use Illuminate\Support\Facades\Auth;

class CommonAttendanceController extends Controller
{
    public function stamp_list(Request $request) {
        $user = Auth::user();
        $tab = request()->query('tab', 'pending');
        $query = ($tab === 'approved')
            ? AttendanceCorrect::onlyTrashed()
            : AttendanceCorrect::query();
        $query->join('attendances', 'attendance_corrects.attendance_id', '=', 'attendances.id')
        ->with(['attendance.user']);

        if (!$user->can('admin')) {
            $query->where('attendances.user_id', $user->id);
        }

        $correct_requests = $query->select('attendance_corrects.*')
        ->reorder()
        ->orderBy('attendances.check_in_at', 'asc')
        ->get();
        $dir = $user->can('admin') ? 'admin' : 'staff';
        return view($dir. '.stamp_list', compact('correct_requests', 'tab'));
    }

    public function detail($id) {
        $attendance = Attendance::with('rests', 'user')->findOrFail($id);
        /** @var \App\Models\User $loginUser */
        $loginUser = Auth::user();

        if ($loginUser->cannot('admin') && $loginUser->id !== $attendance->user_id) {
            abort(403);
        }
        $dir = $loginUser->can('admin') ? 'admin' : 'staff';
        $user = $attendance->user;
        $attendanceCorrect = AttendanceCorrect::withTrashed()
        ->where('attendance_id', $attendance->id)
        ->latest()
        ->first();
        return view($dir. '.attendance_detail', compact('attendance', 'user', 'attendanceCorrect'));
    }
}
