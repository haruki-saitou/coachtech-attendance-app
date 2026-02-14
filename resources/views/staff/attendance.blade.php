@extends('layouts.app')

@section('content')
    @if (session('status'))
        <div id="flash-message"
            class="max-w-[900px] bg-green-100 border border-green-400 text-green-700 px-4 py-4 rounded my-2 mx-auto text-center">
            {{ session('status') }}
        </div>

    @elseif (session('error'))
        <div id="flash-message"
            class="max-w-[900px] bg-green-100 border border-green-400 text-green-700 px-4 py-4 rounded my-2 mx-auto text-center">
            {{ session('error') }}
        </div>
    @endif
    <div
        class="container bg-[#F0EFF2] max-w-[1400px] mx-auto px-8 py-4 flex flex-col items-center justify-center min-h-[calc(100vh-80px)]">
        <div
            class="status-area bg-[#c8c8c8] flex justify-center items-center gap-1 py-2 px-4 rounded-full text-[#696969] font-bold">
            @if (!$attendance)
                <span></span>
                <p class="status-text">勤務外</p>
            @elseif ($attendance->check_out_at)
                <span></span>
                <p class="status-text">退勤済</p>
            @elseif ($attendance->is_resting)
                <span></span>
                <p class="status-text">休憩中</p>
            @else
                <span></span>
                <p class="status-text">出勤中</p>
            @endif
        </div>

        <div class="clock-area text-center mb-8">
            <div id="real-time-date" class="text-4xl my-10"></div>
            <div id="real-time-clock" class="text-7xl font-bold text-center mt-2"></div>
        </div>

        <div class="button-area flex justify-center gap-16 max-w-6xl mt-10">
            @if (!$attendance)
                <x-attendance-button text="出勤" action="{{ route('start.attendance') }}" type="main" />
            @elseif ($attendance->check_out_at)
                <span class="text-2xl font-bold">お疲れ様でした。</span>
            @elseif ($attendance->is_resting)
                <x-attendance-button text="休憩戻" action="{{ route('end.rest') }}" type="sub" />
            @else
                <x-attendance-button text="退勤" action="{{ route('end.attendance') }}" type="main" />
                <x-attendance-button text="休憩入" action="{{ route('start.rest') }}" type="sub" />
            @endif
        </div>
    </div>
@endsection
@section('js')
    <script>
        // リアルタイム時計のスクリプト
        function updateClock() {
            const now = new Date();
            const year = now.getFullYear();
            const month = now.getMonth() + 1; // 月は0から始まるため+1
            const date = now.getDate();

            const dayName = ['日', '月', '火', '水', '木', '金', '土'];
            const dayOfWeek = dayName[now.getDay()];

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            const dateElement = document.getElementById('real-time-date');
            const clockElement = document.getElementById('real-time-clock');

            if (dateElement) {
                dateElement.textContent = `${year}年${month}月${date}日(${dayOfWeek})`;
            }
            if (clockElement) {
                clockElement.textContent = `${hours}:${minutes}`;
            }
        }

        setInterval(updateClock, 1000);
        updateClock(); // 初回呼び出し
    </script>
@endsection
