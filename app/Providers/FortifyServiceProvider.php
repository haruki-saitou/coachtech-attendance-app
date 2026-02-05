<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));
        Fortify::verifyEmailView(fn () => view('auth.verify-email'));
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->input(Fortify::username()))->first();

            if (
                $user &&
                Hash::check($request->input('password'), $user->password)
            ) {
                if ($request->is('admin/login')) {
                    return ($user->role === 1) ? $user : null;
                }
                return ($user->role === 0) ? $user : null;
            }
            return null;
        });

        Fortify::loginView(function () {
            if (request()->is('admin/*')) {
                return view('auth.admin_login');
            }
            return view('auth.login');
        });

        $this->app->instance(\Laravel\Fortify\Contracts\VerifyEmailResponse::class, new class implements
        \Laravel\Fortify\Contracts\VerifyEmailResponse {
        public function toResponse($request)
            {
                return redirect()->route('attendance.top')->with('status', 'メールアドレスが認証されました。');
            }
        });

        $this->app->bind(FortifyLoginRequest::class, LoginRequest::class);
        $this->app->instance(LoginResponseContract::class, new class implements LoginResponseContract {
            public function toResponse($request)
            {
                if ($request->user()->role === 1) {
                    return redirect('/admin/attendance/list');
                }
                return redirect('/attendance');
            }
        });

        $this->app->instance(\Laravel\Fortify\Contracts\LogoutResponse::class, new class implements \Laravel\Fortify\Contracts\LogoutResponse {

            public function toResponse($request)
            {
                if ($request->is('admin/*') || str_contains(url()->previous(), 'admin')) {
                    return redirect('/admin/login')->with('status', 'ログアウトしました');
                }
                return redirect('/login')->with('status', 'ログアウトしました');
            }
        });
    }
}