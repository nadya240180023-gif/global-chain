@extends('layouts.app')

@section('title', 'Pengaturan Akun')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
            <i class="fa-solid fa-gear text-slate-800 text-base"></i>
        </span>
        <div>
            <h1 class="text-2xl font-black text-slate-800">Pengaturan</h1>
            <p class="text-sm text-slate-400 font-semibold mt-1">Ubah informasi akun, ganti kata sandi, dan kelola profil Anda.</p>
        </div>
    </div>

    {{-- Update Profile Info Card --}}
    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 shadow-sm">
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Update Password Card --}}
    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 shadow-sm">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    {{-- Delete Account Card --}}
    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 shadow-sm">
        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection
