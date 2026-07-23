<section class="space-y-8">
    <header>
        <h2 class="text-lg font-extrabold text-slate-800">
            {{ __('Perbarui Kata Sandi') }}
        </h2>

        <p class="mt-2 text-sm text-slate-400 font-semibold">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-8">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Kata Sandi Saat Ini')" class="text-sm font-bold text-slate-700 mb-2" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" 
                class="mt-1 block w-full bg-slate-100 border border-slate-300 rounded-xl py-2.5 px-4 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" 
                autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-sm text-rose-600 font-bold" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Kata Sandi Baru')" class="text-sm font-bold text-slate-700 mb-2" />
            <x-text-input id="update_password_password" name="password" type="password" 
                class="mt-1 block w-full bg-slate-100 border border-slate-300 rounded-xl py-2.5 px-4 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" 
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-sm text-rose-600 font-bold" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Kata Sandi Baru')" class="text-sm font-bold text-slate-700 mb-2" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                class="mt-1 block w-full bg-slate-100 border border-slate-300 rounded-xl py-2.5 px-4 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" 
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-sm text-rose-600 font-bold" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition-all shadow-md shadow-blue-100">
                {{ __('Simpan Kata Sandi') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-bold text-emerald-600"
                >{{ __('Berhasil disimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
