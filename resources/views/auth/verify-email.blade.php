@extends('layouts.app', ['bodyClass' => 'bg-white'])

@section('content')
    <div class="max-w-[1400px] mx-auto flex flex-col items-center justify-center min-h-[80vh] px-4">
        <div class="text-center mb-10">
            <h1 class="text-2xl font-bold leading-relaxed text-gray-800">
                登録していただいたメールアドレスに認証メールを送信しました。<br>
                メール認証を完了してください。
            </h1>
        </div>
        <div class="flex justify-center">
            <a href="https://mailtrap.io/inboxes" target="_blank"
                class="inline-block mx-auto bg-gray-300 text-black text-xl font-bold py-4 px-8 rounded-md hover:bg-gray-400 transition border border-gray-500">
                認証はこちらから
            </a>
        </div>
        <div class="mt-10">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="text-blue-500 mt-2 hover:underline text-lg font-medium">
                    認証メールを再送する
                </button>
            </form>
        </div>
        @if (session('status') == 'verification-link-sent')
            <div id="flash-message" class="mt-6 text-green-600 text-center font-bold">
                新しい認証メールを送信しました。
            </div>
        @endif
    </div>
@endsection
