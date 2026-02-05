@extends('layouts.app')

@section('content')
    <div class="container max-w-[1400px] mx-auto px-8 py-4 flex flex-col items-center min-h-[calc(100vh-80px)]">
        <div class="w-full max-w-[900px] py-14 rounded-lg">
        @if (session('status'))
            <div id="flash-message"
                class="max-w-[900px] bg-green-100 border border-green-400 text-green-700 px-4 py-4 rounded my-2 mx-auto text-center">
                {{ session('status') }}
            </div>
        @endif
            <h1 class="border-l-8 border-black pl-4 text-3xl font-bold mt-6 mb-6">勤怠詳細</h1>
            <form action="{{ route('attendance.update', ['id' => $attendance->id]) }}" method="POST">
                @csrf
                @php
                    $isPending = isset($attendanceCorrect) && !$attendanceCorrect->trashed();
                    $isApproved = ($attendance->status === '承認済み');
                    $hasBorder = ($isPending || $isApproved) ? '' : 'border border-[#E1E1E1] mt-16';
                    $marginTop = ($isPending || $isApproved) ? 'mt-12' : '';
                @endphp
                @unless (Auth::user()->can('admin') && $isPending)
                    @method('PATCH')
                @endunless
                <div class="{{ $hasBorder }} {{ $marginTop }} rounded-lg overflow-hidden w-full max-[800px]">
                    <table class="w-full bg-white text-[#737373] font-bold border-collapse">
                        <tbody>
                            {{-- 名前 --}}
                            <tr class="text-lg border-b-2 border-[#E1E1E1]">
                                <th class="py-8 px-14 text-left">名前</th>
                                <td class="py-8 px-14 text-black">{{ $user->name }}</td>
                            </tr>
                            {{-- 日付 --}}
                            <tr class="text-lg border-b-2 border-[#E1E1E1]">
                                <th class="py-8 px-14 text-left">日付</th>
                                <td class="py-8 px-8 text-gray-900 flex items-center justify-start gap-4">
                                    <span class="px-8">{{ isset($attendance->check_in_at) ? $attendance->check_in_at->format('Y年') : '' }}</span>
                                    <span class="px-14">{{ isset($attendance->check_in_at) ? $attendance->check_in_at?->format('n月j日') : '' }}</span>
                                </td>
                            </tr>
                            {{-- 退勤・出勤 --}}
                            <tr class="text-lg border-b-2 border-[#E1E1E1]">
                                <th class="py-8 px-14 text-left">退勤・出勤</th>
                                <td class="py-8 px-8 text-gray-900 flex items-center gap-4">
                                    @if ($isPending || $isApproved)
                                        <span
                                            class="px-4 py-1 w-32 text-center">{{ ($isApproved ? $attendance->check_in_at : $attendanceCorrect->updated_check_in_at)?->format('H:i') ?? '' }}</span>
                                        <span>〜</span>
                                        <span
                                            class="px-4 py-1 w-32 text-center">{{ ($isApproved ? $attendance->check_out_at : $attendanceCorrect->updated_check_out_at)?->format('H:i') ?? '' }}</span>
                                    @else
                                        <input type="text" name="check_in_at"
                                            value="{{ isset($attendance->check_in_at) ? $attendance->check_in_at?->format('H:i') : '' }}"
                                            class="border border-[#E1E1E1] rounded px-4 py-1 w-32 text-center focus:outline-none">
                                        <span>〜</span>
                                        <input type="text" name="check_out_at"
                                            value="{{ isset($attendance->check_out_at) ? $attendance->check_out_at?->format('H:i') : '' }}"
                                            class="border border-[#E1E1E1] rounded px-4 py-1 w-32 text-center focus:outline-none">
                                    @endif
                                </td>
                            </tr>
                            {{-- 休憩 --}}
                            @if ($isPending || $isApproved)
                            @php
                                $displayRests = $isPending ? ($attendanceCorrect->updated_rests ?? []) : $attendance->rests;
                            @endphp
                                @foreach ($displayRests as $index => $rest)
                                    <tr class="text-lg border-b-2 border-[#E1E1E1]">
                                        <th class="py-8 px-14 text-left">休憩{{ $index > 0 ? $index + 1 : '' }}</th>
                                        <td class="py-8 px-8 flex items-center gap-4 text-gray-900">
                                            <span class="px-4 py-1 w-32 text-center">
                                                {{ $isPending ? (\Carbon\Carbon::parse($rest['start_at'] ?? '')->format('H:i')) : ($rest?->start_at?->format('H:i') ?? '') }}
                                            </span>
                                            <span>〜</span>
                                            <span class="px-4 py-1 w-32 text-center">
                                                {{ $isPending ? (\Carbon\Carbon::parse($rest['end_at'] ?? '')->format('H:i')) : ($rest?->end_at?->format('H:i') ?? '') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                {{-- 休憩 (通常時:入力枠を出す) --}}
                            @else
                                @for ($i = 0; $i < 2; $i++)
                                    @php $rest = $attendance->rests[$i] ?? null; @endphp
                                    <tr class="text-lg border-b-2 border-[#E1E1E1]">
                                        <th class="py-8 px-14 text-left">休憩{{ $i > 0 ? $i + 1 : '' }}</th>
                                        <td class="py-8 px-8 flex items-center gap-4 text-gray-900">
                                            <input type="text" name="rest_start[]"
                                                value="{{ isset($rest->start_at) ? $rest->start_at->format('H:i') : '' }}"
                                                class="border border-[#E1E1E1] rounded px-4 py-1 w-32 text-center focus:outline-none">
                                            <span>〜</span>
                                            <input type="text" name="rest_end[]"
                                                value="{{ isset($rest->end_at) ? $rest->end_at->format('H:i') : '' }}"
                                                class="border border-[#E1E1E1] rounded px-4 py-1 w-32 text-center focus:outline-none">
                                        </td>
                                    </tr>
                                @endfor
                            @endif
                            {{-- 備考 --}}
                            <tr class="text-lg">
                                <th class="py-4 px-14 text-left align-center">備考</th>
                                <td class="py-4 px-8">
                                    @if ($isPending || $isApproved)
                                        <div class="p-6 h-20 w-full text-gray-900 font-bold">
                                            {{ $isApproved ? $attendance->comment : ($attendanceCorrect->updated_comment ?? '' )}}</div>
                                    @else
                                        <textarea name="comment"
                                            class="border border-[#E1E1E1] rounded p-2 mt-1 h-20 w-full max-w-[306px] text-gray-900 font-bold focus:outline-none resize-none">{{ $attendance->comment ?? '' }}</textarea>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- 修正・承認ボタン --}}
                <div class="mt-6 flex justify-end">
                    @if (Auth::user()->can('admin'))
                        @if ($isPending)
                            <button type="submit"
                                formaction="{{ route('admin.attendance.approve', ['attendance_correct_request_id' => $attendance->id]) }}"
                                class="bg-gray-900 text-white hover:bg-gray-700 inline-block px-12 py-3 text-xl font-bold rounded-md transition cursor-pointer">承認</button>
                        @elseif($isApproved)
                            <span class="bg-[#8B8B8B] text-white px-6 py-3 text-xl font-bold rounded-md cursor-not-allowed">承認済み</span>
                        @else
                            <button type="button"
                                class="bg-gray-900 text-white hover:bg-gray-700 inline-block px-12 py-3 text-xl font-bold rounded-md transition cursor-pointer">
                                修正
                            </button>
                        @endif
                    @else
                        @if ($isApproved)
                            <span class="bg-[#8B8B8B] text-white px-6 py-3 text-xl font-bold rounded-md cursor-not-allowed">承認済み</span>
                        @elseif ($isPending)
                            <span class="text-lg font-bold text-red-400">＊承認待ちのため修正はできません</span>
                        @else
                            <button type="submit"
                                class="bg-gray-900 text-white hover:bg-gray-700 inline-block px-12 py-3 text-xl font-bold rounded-md transition cursor-pointer">
                                修正
                            </button>
                        @endif
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const approveBtn = document.querySelector('button[formaction*="approve"]');
            if (approveBtn) {
                approveBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.formAction;

                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                const messageBox = document.createElement('div');
                                messageBox.id = "flash-message";
                                messageBox.className = "max-w-[900px] bg-green-100 border border-green-400 text-green-700 px-4 py-4 rounded my-2 mx-auto text-center";
                                messageBox.innerText = "承認しました";

                                const header = document.querySelector('h1');
                                header.parentNode.insertBefore(messageBox, header);

                                this.outerHTML = '<button type="button" disabled class="bg-[#8B8B8B] text-white px-6 py-3 text-xl font-bold rounded-md cursor-not-allowed">承認済み</button>';

                                const wrapper = document.querySelector('.border-[#E1E1E1]');
                                if (wrapper) {
                                    wrapper.classList.remove('border', 'border-[#E1E1E1]', 'mt-16');
                                    wrapper.classList.add('mt-12');
                                }

                                document.querySelectorAll('input[type="text"], textarea').forEach(el => {
                                    const val = el.value;
                                    const span = document.createElement('span');
                                    span.className = el.className + " border-none";
                                    span.innerText = val;
                                    el.parentNode.replaceChild(span, el);
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                });
            }
        });
    </script>
@endsection
