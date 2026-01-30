<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RestController;


Route::middleware(['auth','verified'])->group(function () {
    // 勤怠関連のルート
    Route::get('/attendance', [AttendanceController::class, 'attendance_top'])->name('attendance.top');
    // 出勤・退勤のルート
    Route::post('/attendance/start', [AttendanceController::class, 'start_attendance'])->name('start.attendance');
    Route::post('/attendance/end', [AttendanceController::class, 'end_attendance'])->name('end.attendance');
    // 勤怠一覧のルート
    Route::get('/attendance/list', [AttendanceController::class, 'attendance_list'])->name('attendance.list');
    // 勤怠詳細のルート
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'attendance_detail'])->name('attendance.detail');
    // 勤怠詳細のルート（更新処理）
    Route::patch('/attendance/detail/{id}', [AttendanceController::class, 'attendance_detail_update'])->name('attendance.update');
    // 打刻修正申請一覧のルート
    Route::get('/stamp_correction_request/list', [AttendanceController::class, 'stamp_list'])->name('stamp.list');

    // 休憩開始・終了のルート
    Route::post('/rest/start', [RestController::class, 'start_rest'])->name('start.rest');
    Route::post('/rest/end', [RestController::class, 'end_rest'])->name('end.rest');
});