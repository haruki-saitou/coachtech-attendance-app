@extends('layouts.app', ['bodyClass' => 'bg-white'])

@section('content')
    <div class="max-w-[1400px] mx-auto flex flex-col items-center justify-center py-20 px-4">
        <h1 class="text-3xl font-bold mb-16">会員登録</h1>
        <form method="POST" action="{{ route('register') }}" class="w-full max-w-[600px]" novalidate>
            @csrf
            <div class="mb-8">
                <label for="name" class="block text-lg font-bold mb-2">名前</label>
                <input type="text" name="name" id="name"
                    class="w-full border-[1.5px] border-gray-400 p-3 rounded focus:outline-none focus:border-gray-700"
                    value="{{ old('name') }}" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-8">
                <label for="email" class="block text-lg font-bold mb-2">メールアドレス</label>
                <input type="email" name="email" id="email"
                    class="w-full border-[1.5px] border-gray-400 p-3 rounded focus:outline-none focus:border-gray-700"
                    value="{{ old('email') }}" required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-8">
                <label for="password" class="block text-lg font-bold mb-2">パスワード</label>
                <input type="password" name="password" id="password"
                    class="w-full border-[1.5px] border-gray-400 p-3 rounded focus:outline-none focus:border-gray-700" required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-8">
                <label for="password_confirmation" class="block text-lg font-bold mb-2">パスワード確認</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full border-[1.5px] border-gray-400 p-3 rounded focus:outline-none focus:border-gray-700" required>
                @error('password_confirmation')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="w-full bg-black text-white text-lg font-bold mt-10 py-3 rounded hover:bg-gray-700 transition duration-200">
                登録する
            </button>
            <div class="mt-8 text-center">
                <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-700 hover:underline">ログインはこちら</a>
            </div>
        </form>
    </div>
@endsection
