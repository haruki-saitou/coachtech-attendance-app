<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\CommonAttendanceController;
use App\Http\Controllers\RestController;
use App\Http\Controllers\StaffAttendanceController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

// 管理者ログイン関連
Route::prefix('admin')->group(function () {
    Route::get('/login', fn () => view('auth.admin_login'))->name('admin.login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('admin.login.post');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    // ============================================================
    // 👥 共通エリア（管理者・スタッフ双方）
    // ============================================================
    // 申請一覧のルート（共通パスを使用）
    Route::get('/stamp_correction_request/list', [CommonAttendanceController::class, 'stamp_list'])->name('stamp.list');
    // 勤怠詳細画面(スタッフ専用)のルート
    Route::get('/attendance/detail/{id}', [CommonAttendanceController::class, 'detail'])->name('attendance.detail');
    // 勤怠修正申請のルート
    Route::patch('/attendance/detail/{id}', [StaffAttendanceController::class, 'attendance_detail_update'])->name('attendance.update');

    // ============================================================
    // 👑 管理者専用エリア（can:admin）
    // ============================================================
    Route::middleware('can:admin')->group(function () {
        // スタッフ一覧画面
        Route::get('/admin/staff/list', [AdminAttendanceController::class, 'staff_list'])->name('admin.staff.list');
        // スタッフ別勤怠一覧画面
        Route::get('/admin/attendance/staff/{id}', [AdminAttendanceController::class, 'staff_attendance_list'])->name('admin.staff.attendance.list');
        // 勤怠一覧画面
        Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'admin_attendance_list'])->name('admin.attendance.list');
        // 勤怠詳細画面
        Route::get('/admin/attendance/{id}', [CommonAttendanceController::class, 'detail'])->name('admin.attendance.detail');
        // 修正申請承認画面
        Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminAttendanceController::class, 'approve_correction_request'])->name('admin.stamp.approve');
        // 修正申請承認(更新処理)
        Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminAttendanceController::class, 'approve_attendance'])->name('admin.attendance.approve');
        // CSV出力画面
        Route::get('/admin/attendance/export/{id}', [AdminAttendanceController::class, 'export_csv'])->name('admin.attendance.export');
    });

    // ============================================================
    // 👤 スタッフ専用エリア（can:staff）
    // ============================================================
    Route::middleware('can:staff')->group(function () {
        // 勤怠打刻画面のルート
        Route::get('/attendance', [StaffAttendanceController::class, 'attendance_top'])->name('attendance.top');
        // 「出勤・退勤」処理のルート（登録処理）
        Route::post('/attendance/start', [StaffAttendanceController::class, 'start_attendance'])->name('start.attendance');
        Route::post('/attendance/end', [StaffAttendanceController::class, 'end_attendance'])->name('end.attendance');
        // 休憩「開始・終了」処理のルート
        Route::post('/rest/start', [RestController::class, 'start_rest'])->name('start.rest');
        Route::post('/rest/end', [RestController::class, 'end_rest'])->name('end.rest');
        // 勤怠一覧のルート
        Route::get('/attendance/list', [StaffAttendanceController::class, 'attendance_list'])->name('attendance.list');
    });
});
