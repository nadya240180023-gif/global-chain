<section class="space-y-8">
    <header>
        <h2 class="text-lg font-extrabold text-slate-800">
            {{ __('Hapus Akun') }}
        </h2>

        <p class="mt-2 text-sm text-slate-400 font-semibold">
            {{ __('Setelah akun Anda dihapus, semua sumber daya dan data di dalamnya akan dihapus secara permanen. Sebelum menghapus akun, silakan unduh data atau informasi apa pun yang ingin Anda simpan.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-rose-600 hover:bg-rose-700 text-slate-800 font-bold px-6 py-2.5 rounded-xl text-sm transition-all shadow-md shadow-rose-100"
    >{{ __('Hapus Akun Anda') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-extrabold text-slate-800">
                {{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}
            </h2>

            <p class="mt-2 text-sm text-slate-400 font-semibold">
                {{ __('Setelah akun Anda dihapus, semua data akan hilang secara permanen. Silakan masukkan kata sandi Anda untuk mengonfirmasi penghapusan permanen.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Kata Sandi') }}" class="text-sm font-bold text-slate-700 mb-2" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full bg-slate-100 border border-slate-300 rounded-xl py-2.5 px-4 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                    placeholder="{{ __('Masukkan Kata Sandi Anda') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-sm text-rose-600 font-bold" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-2.5 rounded-xl text-sm transition-all">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button class="bg-rose-600 hover:bg-rose-700 text-slate-800 font-bold px-5 py-2.5 rounded-xl text-sm transition-all shadow-md shadow-rose-100">
                    {{ __('Ya, Hapus Akun') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
