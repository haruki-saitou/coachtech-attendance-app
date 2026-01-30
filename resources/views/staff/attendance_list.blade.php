@extends('layouts.app')

@section('content')
    <div class="container max-w-[1400px] mx-auto px-2 py-2 flex flex-col items-center min-h-[calc(100vh-80px)]">
        <div class="w-full max-w-[800px] py-14 rounded-lg">
            <h1 class="border-l-8 border-black pl-4 text-3xl font-bold mt-6 mb-6">勤怠一覧</h1>
            <div class="bg-white mt-10 py-2 px-4 rounded-lg flex items-center justify-between gap-8 mb-6">
                {{-- 前月へ --}}
                <a href="{{ route('attendance.list', ['month' => $prev_month]) }}"
                    class="flex justify-center items-center gap-2 font-bold text-normal text-[#737373] px-2 py-1 rounded hover:bg-gray-100">
                    <img src="{{ asset('images/arrow.png') }}" alt="前月へ" class="h-4 w-4 opacity-30">前月</a>
                {{-- 表示中の年月 --}}
                <span class="flex items-center gap-2 text-lg font-bold">
                    <img src="{{ asset('images/calendar.png') }}" alt="カレンダー" class="h-6 w-6">
                    {{ $date->format('Y/m') }}
                </span>
                {{-- 翌月へ --}}
                <a href="{{ route('attendance.list', ['month' => $next_month]) }}"
                    class="flex justify-center items-center gap-2 font-bold text-normal text-[#737373] px-2 py-1 rounded hover:bg-gray-100">翌月
                    <img src="{{ asset('images/arrow.png') }}" alt="前月へ"
                        class="h-4 w-4 transform rotate-180 opacity-30">
                </a>
            </div>
            <table class="w-full max-w-[900px] bg-white rounded-lg overflow-hidden mt-6 text-[#737373]">
                <thead>
                    <tr class="font-bold border-b-3 border-[#E1E1E1]">
                        <th class="pl-14 px-2 text-left">日付</th>
                        <th class="py-2 px-2">出勤</th>
                        <th class="py-2 px-2">退勤</th>
                        <th class="py-2 px-2">休憩</th>
                        <th class="py-2 px-2">合計</th>
                        <th class="py-2 px-2">詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attendances as $attendance)
                        <tr class="font-bold text-center border-t-2 border-[#E1E1E1] hover:bg-[#F9F9F9]">
                            {{-- 1. 日付表示：check_in_at を利用 --}}
                            <td class="py-2 px-2">{{ $attendance->check_in_at->isoFormat('MM/DD(ddd)') }}</td>

                            {{-- 2. 出勤・退勤：モデルの名前 (at) と合わせる --}}
                            <td class="py-2 px-2">{{ $attendance->check_in_at->format('H:i') }}</td>
                            <td class="py-2 px-2">
                                {{ $attendance->check_out_at ? $attendance->check_out_at->format('H:i') : '-' }}</td>
                            {{-- 3. 休憩・合計：モデルで作った「魔法の言葉」を呼ぶだけ！ --}}
                            <td class="py-2 px-2">{{ $attendance->formatted_total_rest_time }}</td>
                            <td class="py-2 px-2">{{ $attendance->formatted_total_worked_time }}</td>

                            <td class="py-2 px-2">
                                <a href="{{ route('attendance.detail', ['id' => $attendance->id]) }}"
                                    class="text-gray-900 cursor-pointer hover:text-gray-700">詳細</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
