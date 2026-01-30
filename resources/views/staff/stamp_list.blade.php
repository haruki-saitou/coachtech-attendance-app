@extends('layouts.app')

@section('content')
    <div class="container max-w-[1400px] mx-auto px-2 py-2 flex flex-col items-center min-h-[calc(100vh-80px)]">
        <div class="w-full max-w-[800px] py-10 rounded-lg">
            <h1 class="border-l-8 border-black pl-4 text-3xl font-bold mt-6 mb-6">申請一覧</h1>
            <div class="mt-12 py-2 px-4 border-b-1 border-gray-900 flex items-center gap-10 mb-8">
                <a href="?tab=pending" class="px-8 {{ $tab === 'pending' ? 'font-bold text-gray-900' : '' }}">承認待ち</a>
                <a href="?tab=approved" class="px-8 {{ $tab === 'approved' ? 'font-bold text-gray-900' : '' }}">承認済み</a>
            </div>
            <div class="w-full overflow-hidden"></div>
            <table class="w-full max-w-[900px] bg-white rounded-lg overflow-hidden mt-2 text-[#737373]">
                <thead>
                    <tr class="font-bold border-b-3 border-[#E1E1E1]">
                        <th class="py-2 pl-2">状態</th>
                        <th class="py-2 px-2 text-left">名前</th>
                        <th class="py-2 px-2 text-left">対象日時</th>
                        <th class="py-2 px-2 text-left">申請理由</th>
                        <th class="py-2 px-2 text-left">申請日時</th>
                        <th class="py-2 px-2 text-left">詳細</th>
                    </tr>
                </thead>
                <tbody class="text-[#737373] text-normal border-t-2 border-[#E1E1E1]">
                    @foreach ($correct_requests as $request)
                        <tr class="font-bold border-b-2 border-[#E1E1E1]">
                            <td class="pl-12 py-2 px-2">{{ $request->status }}</td>
                            <td class="py-2 px-2">{{ Auth::user()->name }}</td>
                            <td class="py-2 px-2">{{ $request->check_in_at->format('Y/m/d') }}</td>
                            <td class="py-2 px-2">{{ $request->attendanceCorrect->updated_comment ?? '' }}</td>
                            <td class="py-2 px-2">{{ $request->attendanceCorrect?->created_at?->format('Y/m/d') }}</td>
                            <td class="py-2 px-2">
                                <a href="{{ route('attendance.detail', ['id' => $request->id]) }}" class="font-bold text-gray-900 cursor-pointer hover:text-gray-700">詳細</a>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection
