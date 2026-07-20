<x-guest-layout>
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-slate-800 mb-2">Buat Akun</h3>
        <p class="text-slate-500 text-sm">Bergabunglah dengan platform intelijen risiko untuk memantau rantai pasok Anda.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-slate-600 mb-1.5">{{ __('Nama Lengkap') }}</label>
            <input id="name" class="block w-full bg-white/50 border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300 py-2.5 px-4 shadow-sm" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-600 mb-1.5">{{ __('Alamat Email') }}</label>
            <input id="email" class="block w-full bg-white/50 border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300 py-2.5 px-4 shadow-sm" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="name@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-600 mb-1.5">{{ __('Kata Sandi') }}</label>
            <input id="password" class="block w-full bg-white/50 border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300 py-2.5 px-4 shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-600 mb-1.5">{{ __('Konfirmasi Kata Sandi') }}</label>
            <input id="password_confirmation" class="block w-full bg-white/50 border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300 py-2.5 px-4 shadow-sm"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full relative inline-flex items-center justify-center px-8 py-3.5 text-sm font-bold text-white transition-all duration-300 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl hover:from-emerald-500 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/50 focus:ring-offset-2 focus:ring-offset-slate-900 shadow-[0_0_20px_rgba(20,184,166,0.3)] hover:shadow-[0_0_30px_rgba(20,184,166,0.5)] transform hover:-translate-y-0.5">
                {{ __('Buat Akun') }}
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </button>
        </div>

        <div class="text-center mt-6">
            <p class="text-sm text-slate-500">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium transition-colors">Masuk</a>
            </p>
        </div>
    </form>
</x-guest-layout>
