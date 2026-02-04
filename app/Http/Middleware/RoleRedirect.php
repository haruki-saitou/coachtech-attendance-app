<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AttendanceController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class RoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ログインユーザーが管理者の場合
        if ($request->user() && $request->user()->role === 1) {
            if ($request->is('stamp_correction_request/list')) {
                // 管理者のコントローラーへ移動
                $view = app(AdminAttendanceController::class)->admin_attendance_list($request);
                return response($view);
            }
        }
        // 管理者以外の場合はスタッフ用画面へ移動
        return $next($request);
    }
}
