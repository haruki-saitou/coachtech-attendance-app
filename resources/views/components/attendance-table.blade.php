@props(['attendances', 'showUser' => false, 'routeName' => 'attendance.detail'])

<div>
    <table class="w-full max-w-[900px] bg-white rounded-lg overflow-hidden mt-6 text-[#737373] table-fixed">
        <thead>
            <tr class="font-bold border-b-3 border-[#E1E1E1]">
                {{-- コーチのアドバイス通り、割合（%）で幅を決めます --}}
                <th class="py-2 px-2 text-left w-[20%] pl-12">{{ $showUser ? '名前' : '日付' }}</th>
                <th class="py-2 px-2 w-[15%] text-center text-sm lg:text-base">出勤</th>
                <th class="py-2 px-2 w-[15%] text-center text-sm lg:text-base">退勤</th>
                <th class="py-2 px-2 w-[15%] text-center text-sm lg:text-base">休憩</th>
                <th class="py-2 px-2 w-[15%] text-center text-sm lg:text-base">合計</th>
                <th class="py-2 px-2 w-[15%] text-left pl-12 text-sm lg:text-base">詳細</th>
                <th class="w-auto"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr class="font-bold border-t-2 border-[#E1E1E1] hover:bg-[#F9F9F9]">
                    <td class="py-2 px-2 text-left pl-12">
                        {{ $showUser ? $attendance->user->name : $attendance->check_in_at->isoFormat('MM/DD(ddd)') }}
                    </td>
                    <td class="py-2 px-2 text-center">{{ $attendance->check_in_at->format('H:i') }}</td>
                    <td class="py-2 px-2 text-center">
                        {{ $attendance->check_out_at ? $attendance->check_out_at->format('H:i') : '-' }}
                    </td>
                    <td class="py-2 px-2 text-center">{{ $attendance->formatted_total_rest_time }}</td>
                    <td class="py-2 px-2 text-center">{{ $attendance->formatted_total_worked_time }}</td>
                    <td class="py-2 px-2 text-left pl-12">
                        <a href="{{ route($routeName, [$attendance->id]) }}"
                            class="text-gray-900 cursor-pointer hover:text-gray-700">詳細</a>
                    </td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
