@props(['correct_requests', 'showUser' => false])

<table class="w-full max-w-[900px] bg-white rounded-lg overflow-hidden mt-2 text-[#737373]">
    <thead>
        <tr class="font-bold border-b-3 border-[#E1E1E1]">
            <th class="py-2 px-2 text-left pl-12 w-[20%]">状態</th>
            <th class="py-2 px-2 text-left w-[14%]">名前</th>
            <th class="py-2 px-2 text-left w-[18%]">対象日時</th>
            <th class="py-2 px-2 text-left w-[16%]">申請理由</th>
            <th class="py-2 px-2 text-left w-[18%]">申請日時</th>
            <th class="py-2 px-2 text-left w-[10%]">詳細</th>
            <th class="w-[5%]"></th>
        </tr>
    </thead>
    <tbody class="text-[#737373] border-t-2 border-[#E1E1E1]">
        @foreach ($correct_requests as $request)
            <tr class="font-bold border-b-2 border-[#E1E1E1] hover:bg-gray-100">
                <td class="pl-12 py-2 px-2">{{ $request->attendance?->status ?? '' }}</td>
                <td class="py-2 px-2">{{ $showUser ? ($request->attendance?->user?->name ?? '') : Auth::user()->name }}</td>
                <td class="py-2 px-2">{{ $request->attendance?->check_in_at?->format('Y/m/d') ?? '' }}</td>
                <td class="py-2 px-2">
                    <div class="max-w-[10ch] truncate"
                        title="申請理由:{{ $request->updated_comment }}">
                        {{ $request->updated_comment }}
                    </div>
                </td>
                <td class="py-2 px-2">{{ ($request->created_at?->format('Y/m/d')) }}</td>
                <td class="py-2 px-2">
                    @php
                        $detailId = $request->attendance_id ?? null;
                        $detailRoute = ($detailId) ?
                        (Auth::user()->can('admin')
                        ? route('admin.attendance.detail', ['id' => $detailId])
                        : route('attendance.detail', ['id' => $detailId])) : '';
                    @endphp
                    @if($detailId)
                    <a href="{{ $detailRoute }}"
                        class="font-bold text-gray-900 cursor-pointer hover:text-gray-700">詳細</a>
                    @else
                    -
                    @endif
                </td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
