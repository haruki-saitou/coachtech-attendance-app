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
            <x-stamp-list-table :correct_requests="$correct_requests" :showUser="true" />
        </div>
    </div>
@endsection
