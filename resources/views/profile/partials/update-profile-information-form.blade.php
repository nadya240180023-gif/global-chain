<section class="space-y-8">
    <header>
        <h2 class="text-lg font-extrabold text-slate-800">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-2 text-sm text-slate-400 font-semibold">
            {{ __("Perbarui nama lengkap akun dan alamat email Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-8">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="text-sm font-bold text-slate-700 mb-2" />
            <x-text-input id="name" name="name" type="text" 
                class="mt-1 block w-full bg-slate-100 border border-slate-300 rounded-xl py-2.5 px-4 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" 
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-sm text-rose-600 font-bold" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" class="text-sm font-bold text-slate-700 mb-2" />
            <x-text-input id="email" name="email" type="email" 
                class="mt-1 block w-full bg-slate-100 border border-slate-300 rounded-xl py-2.5 px-4 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" 
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2 text-sm text-rose-600 font-bold" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3">
                    <p class="text-sm text-slate-800">
                        {{ __('Alamat email Anda belum diverifikasi.') }}

                        <button form="send-verification" class="underline text-sm font-bold text-blue-600 hover:text-blue-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-bold text-sm text-emerald-600">
                            {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-blue-600 hover:bg-blue-700 text-slate-800 font-bold px-6 py-2.5 rounded-xl text-sm transition-all shadow-md shadow-blue-100">
                {{ __('Simpan Perubahan') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
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
