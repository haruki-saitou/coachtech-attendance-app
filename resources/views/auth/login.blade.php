@extends('layouts.app', ['bodyClass' => 'bg-white'])

@section('content')
    <div class="max-w-[1400px] mx-auto flex flex-col items-center justify-center py-20 px-4">
        <h1 class="text-2xl font-bold mb-10">ログイン</h1>
        <form method="POST" action="{{ route('login') }}" class="w-full max-w-[600px]" novalidate>
            @csrf
            <div class="mb-6">
                <label for="email" class="block text-lg font-bold mb-2">メールアドレス</label>
                <input type="email" name="email" id="email"
                    class="w-full border-[1.5px] border-gray-400 p-3 rounded focus:outline-none focus:border-gray-700"
                    value="{{ old('email') }}" required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-10">
                <label for="password" class="block text-lg font-bold mb-2">パスワード</label>
                <input type="password" name="password" id="password"
                    class="w-full border-[1.5px] border-gray-400 p-3 rounded focus:outline-none focus:border-gray-700"
                    required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="w-full bg-black text-lg text-white font-bold mt-6 py-3 rounded-md hover:bg-gray-700 transition duration-200">
                ログインする
            </button>
            <div class="mt-6 text-center">
                <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-700 hover:underline">会員登録はこちら</a>
            </div>
        </form>
    </div>
@endsection
