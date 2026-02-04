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
        $status = ($tab ==='approved') ? '承認済み' : '承認待ち';
        $query = Attendance::query();
        if ($user->can('admin')) {
            $dir = 'admin';
            $query->with(['user', 'attendanceCorrect']);
        } else {
            $dir = 'staff';
            $query->where('user_id', $user->id)->has('attendanceCorrect');
        }
        $correct_requests = $query->where('status', $status)->orderBy('updated_at', 'desc')->get();
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
        $attendanceCorrect = AttendanceCorrect::where('attendance_id', $attendance->id)->latest()->first();
        return view($dir. '.attendance_detail', compact('attendance', 'user', 'attendanceCorrect'));
    }
}
