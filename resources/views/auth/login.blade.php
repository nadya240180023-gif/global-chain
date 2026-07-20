<x-guest-layout>
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-slate-800 mb-2">Selamat Datang Kembali</h3>
        <p class="text-slate-500 text-sm">Masuk untuk mengakses dasbor intelijen risiko Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-600 mb-1.5">{{ __('Alamat Email') }}</label>
            <input id="email" class="block w-full bg-white/50 border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300 py-2.5 px-4 shadow-sm" type="email" name="email" value="admin@gsc.com" required autofocus autocomplete="username" placeholder="admin@gsc.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-600 mb-1.5">{{ __('Kata Sandi') }}</label>
            <input id="password" class="block w-full bg-white/50 border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300 py-2.5 px-4 shadow-sm"
                            type="password"
                            name="password"
                            value="password"
                            required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-2">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-slate-300 bg-white text-blue-500 shadow-sm focus:ring-blue-500/30 focus:ring-offset-0 transition-colors cursor-pointer" name="remember">
                <span class="ms-2 text-sm text-slate-500 group-hover:text-slate-700 transition-colors">{{ __('Ingat Saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-blue-600 hover:text-blue-700 transition-colors font-medium" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full relative inline-flex items-center justify-center px-8 py-3.5 text-sm font-bold text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-500 hover:to-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:ring-offset-2 focus:ring-offset-slate-900 shadow-[0_0_20px_rgba(79,70,229,0.3)] hover:shadow-[0_0_30px_rgba(79,70,229,0.5)] transform hover:-translate-y-0.5">
                {{ __('Masuk ke Dasbor') }}
                <svg class="w-4 h-4 ml-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </div>
        
        <div class="text-center mt-6">
            <p class="text-sm text-slate-500">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium transition-colors">Request Access</a>
            </p>
        </div>
    </form>
</x-guest-layout>
