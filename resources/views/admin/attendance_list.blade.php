@extends('layouts.app')

@section('content')
    <div class="container max-w-[1400px] mx-auto px-2 py-2 flex flex-col items-center min-h-[calc(100vh-80px)]">
        <div class="w-full max-w-[900px] py-14 rounded-lg">
            <h1 class="border-l-8 border-black pl-4 text-3xl font-bold mt-6 mb-6">{{ $date->format('Y年m月d日') }}の勤怠</h1>
            <div class="bg-white mt-10 py-2 px-4 rounded-lg flex items-center justify-between gap-8 mb-6">
                {{-- 前日へ --}}
                <a href="{{ route('admin.attendance.list', ['date' => $prev_date]) }}"
                    class="flex justify-center items-center gap-2 font-bold text-normal text-[#737373] px-2 py-1 rounded hover:bg-gray-100">
                    <img src="{{ asset('images/arrow.png') }}" alt="前月へ" class="h-4 w-5 opacity-30">前日</a>
                {{-- 表示中の年月 --}}
                <span class="flex items-center gap-2 text-lg font-bold">
                    <img src="{{ asset('images/calendar.png') }}" alt="カレンダー" class="h-6 w-6">
                    {{ $date->format('Y/m/d') }}
                </span>
                {{-- 翌日へ --}}
                <a href="{{ route('admin.attendance.list', ['date' => $next_date]) }}"
                    class="flex justify-center items-center gap-2 font-bold text-normal text-[#737373] px-2 py-1 rounded hover:bg-gray-100">翌日
                    <img src="{{ asset('images/arrow.png') }}" alt="前月へ"
                        class="h-4 w-5 transform rotate-180 opacity-30">
                </a>
            </div>
            <x-attendance-table :attendances="$attendances" :showUser="true" routeName="admin.attendance.detail"/>
        </div>
    </div>
@endsection
