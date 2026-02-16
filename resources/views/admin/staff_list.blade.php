@extends('layouts.app')

@section('content')
    @if (session('status'))
        <div id="flash-message"
            class="max-w-[600px] bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded my-2 mx-auto text-center">
            {{ session('status') }}
        </div>
    @endif
    <div class="container max-w-[1400px] mx-auto px-2 py-2 flex flex-col items-center min-h-[calc(100vh-80px)]">
        <div class="w-full max-w-[900px] py-14 rounded-lg">
            <h1 class="border-l-8 border-black pl-4 text-3xl font-bold mt-6 mb-6">スタッフ一覧</h1>
            <div class="mt-12 py-2 px-4 border-gray-900 flex items-center gap-10 mb-8">
                <table class="w-full max-w-[900px] bg-white rounded-lg overflow-hidden text-[#737373] table-fixed">
                    <thead>
                        <tr class="font-bold border-b-3 border-[#E1E1E1]">
                            <th class="py-2 px-2 text-center w-[30%] pl-12">名前</th>
                            <th class="py-2 px-2 w-[40%] text-center text-sm lg:text-base">メールアドレス</th>
                            <th class="py-2 px-2 w-[20%] text-center pl-12 text-sm lg:text-base">月次勤怠</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="font-bold border-t-2 border-[#E1E1E1] hover:bg-[#F9F9F9]">
                                <td class="py-2 px-2 text-center pl-12">{{ $user->name }}</td>
                                <td class="py-2 px-2 text-center">{{ $user->email }}</td>
                                <td class="py-2 px-2 text-center pl-12">
                                    <a href="{{ route('admin.staff.attendance.list', ['id' => $user->id]) }}"
                                        class="text-gray-900 cursor-pointer hover:text-gray-700">詳細</a>
                                </td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endsection
