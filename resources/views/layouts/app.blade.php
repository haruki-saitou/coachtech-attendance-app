<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ATTENDANCE-APP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="{{ $bodyClass ?? 'bg-[#F0EFF2]' }}">
    <header class="bg-black text-white w-full">
        <div class="max-w-[1400px] mx-auto px-8 py-3 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex-shrink-0">
                @can('staff')
                <a href="{{ route('attendance.top') }}">
                    <img src="{{ asset('images/COACHTECH.png') }}" alt="COACHTECH" class="h-6 md:h-8">
                </a>
                @elsecan('admin')
                <a href="{{ route('admin.staff.list') }}">
                    <img src="{{ asset('images/COACHTECH.png') }}" alt="COACHTECH" class="h-6 md:h-8">
                </a>
                @endcan
            </div>
            @if (!Route::is('login') && !Route::is('register') && !Route::is('verification.notice'))
                <nav class="flex items-center gap-6 lg:gap-8 text-md font-bold lg:text-md flex-shrink-0">
                    @auth
                        @can('staff')
                            <a href="{{ route('attendance.top') }}" class="hover:text-gray-300">勤怠</a>
                            <a href="{{ route('attendance.list') }}" class="hover:text-gray-300">勤怠一覧</a>
                            <a href="{{ route('stamp.list') }}" class="hover:text-gray-300">申請</a>
                        @elsecan('admin')
                            <a href="{{ route('admin.attendance.list') }}" class="hover:text-gray-300">勤怠一覧</a>
                            <a href="{{ route('admin.staff.list') }}" class="hover:text-gray-300">スタッフ一覧</a>
                            <a href="{{ route('admin.stamp.approve', ['attendance_correct_request_id' => 'list']) }}" class="hover:text-gray-300">申請一覧</a>
                        @endcan
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="cursor-pointer hover:text-gray-300">ログアウト</button>
                        </form>
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <main class="tracking-[0.1em]">
        @yield('content')
    </main>
    @yield('js')
    <script>
        function autoHideMessage() {
            const message = document.getElementById('flash-message');
            if (message) {
                setTimeout(() => {
                    message.style.transition = "opacity 1s";
                    message.style.opacity = "0";
                    setTimeout(() => {
                        message.remove();
                    }, 1000);
                }, 3000);
            }
        }

        window.onload = autoHideMessage;
        const observer = new MutationObserver(autoHideMessage);
        observer.observe(document.body, { childList: true, subtree: true});
    </script>
</body>

</html>
