@extends('layouts.app')

@section('content')
    <div class="container max-w-[1400px] mx-auto px-8 py-4 flex flex-col items-center min-h-[calc(100vh-80px)]">
        <div class="w-full max-w-[800px] py-14 rounded-lg">
            <h1 class="border-l-8 border-black pl-4 text-3xl font-bold mt-6 mb-6">勤怠詳細</h1>
            <form action="{{ route('attendance.update', ['id' => $attendance->id]) }}" method="POST">
                @csrf
                @method('PATCH')
                @php
                    $isPending = $attendance->status === '承認待ち';
                    $wrapperClass = $isPending ? 'border border-[#E1E1E1] rounded-lg overflow-hidden mt-16' : '';
                @endphp
                <div class="{{ $wrapperClass }} rounded-lg overflow-hidden w-full max-[800px] mt-12">
                    <table class="w-full bg-white text-[#737373] font-bold border-collapse">
                        <tbody>
                            <tr class="text-lg border-b-2 border-[#E1E1E1]">
                                <th class="py-8 px-14 text-left">名前</th>
                                <td class="py-8 px-14 text-black">{{ $user->name }}</td>
                            </tr>
                            <tr class="text-lg border-b-2 border-[#E1E1E1]">
                                <th class="py-8 px-14 text-left">日付</th>
                                <td class="py-8 px-8 text-gray-900 flex items-center justify-start gap-4">
                                    <span class="px-8">{{ $attendance->check_in_at?->format('Y年') ?? ' ' }}</span>
                                    <span class="px-14">{{ $attendance->check_in_at?->format('n月j日') ?? ' ' }}</span>
                                </td>
                            </tr>
                            <tr class="text-lg border-b-2 border-[#E1E1E1]">
                                <th class="py-8 px-14 text-left">退勤・出勤</th>
                                <td class="py-8 px-8 text-gray-900 flex items-center gap-4">
                                    @if ($isPending)
                                        <span
                                            class="px-4 py-1 w-32 text-center">{{ $attendanceCorrect->updated_check_in_at?->format('H:i') ?? ' ' }}</span>
                                        <span>〜</span>
                                        <span
                                            class="px-4 py-1 w-32 text-center">{{ $attendanceCorrect->updated_check_out_at?->format('H:i') ?? ' ' }}</span>
                                    @else
                                        <input type="text" name="check_in_at"
                                            value="{{ $attendance->check_in_at?->format('H:i') ?? ' ' }}"
                                            class="border border-[#E1E1E1] rounded px-4 py-1 w-32 text-center focus:outline-none">
                                        <span>〜</span>
                                        <input type="text" name="check_out_at"
                                            value="{{ $attendance->check_out_at?->format('H:i') ?? ' ' }}"
                                            class="border border-[#E1E1E1] rounded px-4 py-1 w-32 text-center focus:outline-none">
                                    @endif
                                </td>
                            </tr>
                            @if ($isPending)
                                @foreach ($attendanceCorrect->updated_rests ?? [] as $index => $c_rest)
                                    @php
                                        $start = !empty($c_rest['start_at'])
                                            ? \Carbon\Carbon::parse($c_rest['start_at'])->format('H:i')
                                            : '';
                                        $end = !empty($c_rest['end_at'])
                                            ? \Carbon\Carbon::parse($c_rest['end_at'])->format('H:i')
                                            : '';
                                    @endphp
                                    @if ($start || $end)
                                        <tr class="text-lg border-b-2 border-[#E1E1E1]">
                                            <th class="py-8 px-14 text-left">休憩{{ $index > 0 ? $index + 1 : '' }}</th>
                                            <td class="py-8 px-8 flex items-center gap-4 text-gray-900">
                                                <span class="px-4 py-1 w-32 text-center"> {{ $start }}</span>
                                                <span>〜</span>
                                                <span class="px-4 py-1 w-32 text-center">{{ $end }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                @for ($i = 0; $i < 2; $i++)
                                    @php $rest = $attendance->rests[$i] ?? null; @endphp
                                    <tr class="text-lg border-b-2 border-[#E1E1E1]">
                                        <th class="py-8 px-14 text-left">休憩{{ $i > 0 ? $i + 1 : '' }}</th>
                                        <td class="py-8 px-8 flex items-center gap-4 text-gray-900">
                                        <input type="text" name="rest_start[]"
                                            value="{{ $rest ? $rest->start_at->format('H:i') : '' }}"
                                            class="border border-[#E1E1E1] rounded px-4 py-1 w-32 text-center focus:outline-none">
                                        <span>〜</span>
                                        <input type="text" name="rest_end[]"
                                            value="{{ $rest ? $rest->end_at->format('H:i') : '' }}"
                                            class="border border-[#E1E1E1] rounded px-4 py-1 w-32 text-center focus:outline-none">
                                        </td>
                                    </tr>
                                @endfor
                            @endif
                            <tr class="text-lg">
                                <th class="py-4 px-14 text-left align-center">備考</th>
                                <td class="py-4 px-8">
                                    @if ($isPending)
                                        <div class="p-6 h-20 w-full text-gray-900 font-bold">
                                            {{ $attendanceCorrect->updated_comment ?? '' }}</div>
                                    @else
                                        <textarea name="comment"
                                            class="border border-[#E1E1E1] rounded p-2 mt-1 h-20 w-full max-w-[306px] text-gray-900 font-bold focus:outline-none resize-none">{{ $attendance->comment ?? '' }}</textarea>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 flex justify-end">
                    @if ($isPending)
                        <span class="text-lg font-bold text-red-400">＊承認待ちのため修正はできません</span>
                    @else
                        <button type="submit"
                            class="bg-gray-900 text-white hover:bg-gray-700 inline-block px-12 py-3 text-xl font-bold rounded-md transition cursor-pointer">
                            修正
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
