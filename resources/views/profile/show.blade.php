@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Header & Breadcrumbs Card -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 sm:p-6 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white font-extrabold text-2xl flex items-center justify-center shadow-md shadow-emerald-500/20">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ $user->name }}
                    </h1>
                    <span class="text-[10px] font-mono px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider {{ $user->isAdmin() ? 'bg-slate-900 text-white dark:bg-slate-800' : ($user->isOperator() ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300') }}">
                        {{ $user->role }}
                    </span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-full border border-emerald-200/50 dark:border-emerald-800/50">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span data-i18n="profile_active_badge">Akun Aktif</span>
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-3 flex-wrap">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $user->email }}
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span data-i18n="profile_member_since">Bergabung sejak:</span>
                        <span class="font-mono font-medium">{{ $user->created_at->format('d M Y') }}</span>
                    </span>
                </p>
            </div>
        </div>

        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-[#1A273D] dark:hover:bg-[#233550] text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-200 dark:border-[#233550] transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span data-i18n="back_to_dashboard">Kembali ke Dashboard</span>
        </a>
    </div>

    <!-- Role-Based Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @if ($user->isCustomer())
            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 shadow-sm">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" data-i18n="profile_metric_total_shipments">Total Konsinyasi</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $stats['shipments_count'] }}</div>
                <div class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-1" data-i18n="profile_metric_total_sub">Konsinyasi kargo terdaftar atas nama Anda</div>
            </div>

            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 shadow-sm">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" data-i18n="profile_metric_active_transit">Dalam Perjalanan</div>
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['active_shipments_count'] }}</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1" data-i18n="profile_metric_active_sub">Konsinyasi aktif terpantau di radar</div>
            </div>

            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 shadow-sm">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" data-i18n="profile_metric_reported_incidents">Insiden &amp; Kendala</div>
                <div class="text-2xl font-black text-amber-500 mt-1">{{ $stats['exceptions_reported'] }}</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1" data-i18n="profile_metric_incidents_sub">Laporan kendala operasional terkait kargo Anda</div>
            </div>
        @elseif ($user->isOperator())
            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 shadow-sm">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" data-i18n="profile_metric_telemetry_logged">Pembaruan Telemetri</div>
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['tracking_events_count'] }}</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1" data-i18n="profile_metric_telemetry_sub">Log titik koordinat AIS &amp; ADS-B tercatat</div>
            </div>

            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 shadow-sm">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" data-i18n="profile_metric_exceptions_logged">Insiden Ditangani</div>
                <div class="text-2xl font-black text-amber-500 mt-1">{{ $stats['exceptions_reported'] }}</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1" data-i18n="profile_metric_exceptions_sub">Kasus kendala cuaca/pelabuhan dicatat</div>
            </div>

            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 shadow-sm">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" data-i18n="profile_metric_active_corridors">Armada Koridor Aktif</div>
                <div class="text-2xl font-black text-cyan-600 dark:text-cyan-400 mt-1">{{ $stats['active_shipments_count'] }}</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1" data-i18n="profile_metric_corridors_sub">Konsinyasi aktif dalam pantauan radar</div>
            </div>
        @else
            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 shadow-sm">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" data-i18n="profile_metric_system_shipments">Konsinyasi Sistem</div>
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['shipments_count'] }}</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1" data-i18n="profile_metric_system_shipments_sub">Total manifest terdaftar di CargoTrace</div>
            </div>

            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 shadow-sm">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" data-i18n="profile_metric_audit_logs">Log Audit Keamanan</div>
                <div class="text-2xl font-black text-cyan-600 dark:text-cyan-400 mt-1">{{ $stats['audit_logs_count'] }}</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1" data-i18n="profile_metric_audit_sub">Entri riwayat perubahan tercatat aman</div>
            </div>

            <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 shadow-sm">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" data-i18n="profile_metric_active_consignments">Konsinyasi Berjalan</div>
                <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $stats['active_shipments_count'] }}</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1" data-i18n="profile_metric_active_consignments_sub">Kapal &amp; pesawat aktif bertransportasi</div>
            </div>
        @endif
    </div>

    <!-- Forms Layout (Edit Profile + Security Settings) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Form 1: Edit Profile Details -->
        <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-6 sm:p-7 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-[#1E293B]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white" data-i18n="profile_form_title">
                                Informasi Data Diri
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400" data-i18n="profile_form_sub">
                                Perbarui nama, alamat email kontak, dan nomor telepon Anda.
                            </p>
                        </div>
                    </div>
                </div>

                <form id="profile-update-form" action="{{ route('profile.update') }}" method="POST" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" data-i18n="profile_label_name">
                            Nama Lengkap
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full bg-slate-50 dark:bg-[#090D15] border @error('name') border-red-500 @else border-slate-200 dark:border-[#1E293B] @enderror rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                   placeholder="Contoh: Budi Santoso">
                        </div>
                        @error('name')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" data-i18n="profile_label_email">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full bg-slate-50 dark:bg-[#090D15] border @error('email') border-red-500 @else border-slate-200 dark:border-[#1E293B] @enderror rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                   placeholder="nama@perusahaan.com">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number / WhatsApp -->
                    <div>
                        <label for="phone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" data-i18n="profile_label_phone">
                            Nomor Telepon / WhatsApp
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                   class="w-full bg-slate-50 dark:bg-[#090D15] border @error('phone') border-red-500 @else border-slate-200 dark:border-[#1E293B] @enderror rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                   placeholder="+62 812-3456-7890">
                        </div>
                        @error('phone')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role Badge (Read-Only) -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" data-i18n="profile_label_role">
                            Hak Akses / Peran Akun
                        </label>
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B]">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $user->isAdmin() ? 'bg-indigo-500' : ($user->isOperator() ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                <span class="font-mono font-bold text-xs uppercase text-slate-800 dark:text-slate-200">{{ $user->role }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-medium" data-i18n="profile_role_readonly_tip">Dikelola oleh Administrator</span>
                        </div>
                    </div>
                </form>
            </div>

            <div class="pt-6 border-t border-slate-100 dark:border-[#1E293B] mt-6 flex justify-end">
                <button type="submit" form="profile-update-form" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span data-i18n="profile_btn_save">Simpan Perubahan</span>
                </button>
            </div>
        </div>

        <!-- Form 2: Change Password -->
        <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-6 sm:p-7 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-[#1E293B]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white" data-i18n="profile_password_title">
                                Keamanan &amp; Kata Sandi
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400" data-i18n="profile_password_sub">
                                Ubah kata sandi akun untuk menjaga keamanan akses portal logistik.
                            </p>
                        </div>
                    </div>
                </div>

                <form id="password-update-form" action="{{ route('profile.password.update') }}" method="POST" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" data-i18n="profile_label_current_pwd">
                            Kata Sandi Saat Ini
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </div>
                            <input type="password" name="current_password" id="current_password" required
                                   class="w-full bg-slate-50 dark:bg-[#090D15] border @error('current_password') border-red-500 @else border-slate-200 dark:border-[#1E293B] @enderror rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                                   placeholder="••••••••">
                        </div>
                        @error('current_password')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" data-i18n="profile_label_new_pwd">
                            Kata Sandi Baru
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input type="password" name="password" id="password" required
                                   class="w-full bg-slate-50 dark:bg-[#090D15] border @error('password') border-red-500 @else border-slate-200 dark:border-[#1E293B] @enderror rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                                   placeholder="••••••••">
                        </div>
                        @error('password')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[10px] text-slate-400 mt-1" data-i18n="profile_pwd_hint">
                            Minimal 8 karakter kombinasi huruf dan angka.
                        </p>
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" data-i18n="profile_label_confirm_pwd">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="w-full bg-slate-50 dark:bg-[#090D15] border border-slate-200 dark:border-[#1E293B] rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                                   placeholder="••••••••">
                        </div>
                    </div>
                </form>
            </div>

            <div class="pt-6 border-t border-slate-100 dark:border-[#1E293B] mt-6 flex justify-end">
                <button type="submit" form="password-update-form" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span data-i18n="profile_btn_change_pwd">Perbarui Kata Sandi</span>
                </button>
            </div>
        </div>

    </div>

    <!-- Section 3: Recent Activity / Activity Logs -->
    @if ($user->isCustomer() && $recentShipments->isNotEmpty())
        <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white" data-i18n="profile_recent_shipments_title">
                        Konsinyasi Kargo Terbaru Anda
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400" data-i18n="profile_recent_shipments_sub">
                        Daftar 5 pengiriman terbaru yang terdaftar atas nama akun Anda.
                    </p>
                </div>
                <a href="{{ route('shipments.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline" data-i18n="profile_view_all_shipments">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-sans">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-[#1E293B] text-[10px] font-mono text-slate-400 uppercase tracking-wider">
                            <th class="py-2.5 px-3" data-i18n="tracking_id">Nomor Resi</th>
                            <th class="py-2.5 px-3" data-i18n="mode">Moda</th>
                            <th class="py-2.5 px-3" data-i18n="corridor_route">Rute Koridor</th>
                            <th class="py-2.5 px-3" data-i18n="status">Status</th>
                            <th class="py-2.5 px-3 text-right" data-i18n="action">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#182338]">
                        @foreach($recentShipments as $shipment)
                        <tr class="hover:bg-slate-50 dark:hover:bg-[#162338] transition">
                            <td class="py-3 px-3 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $shipment->shipment_number }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold {{ $shipment->transport_mode === 'AIR' ? 'bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                    {{ $shipment->transport_mode }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-slate-700 dark:text-slate-200">
                                {{ $shipment->origin->name ?? '-' }} &rarr; {{ $shipment->destination->name ?? '-' }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ strtoupper($shipment->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('shipments.show', $shipment) }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline" data-i18n="details_btn">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif (($user->isOperator() || $user->isAdmin()) && $recentTracking->isNotEmpty())
        <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white" data-i18n="profile_recent_telemetry_title">
                        Log Telemetri &amp; Pembaruan Titik Terakhir Anda
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400" data-i18n="profile_recent_telemetry_sub">
                        Pembaruan status GPS/AIS yang baru saja Anda inputkan ke sistem.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-mono">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-[#1E293B] text-[10px] text-slate-400 uppercase tracking-wider">
                            <th class="py-2.5 px-3">Waktu</th>
                            <th class="py-2.5 px-3">Konsinyasi</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3">Deskripsi / Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#182338]">
                        @foreach($recentTracking as $event)
                        <tr class="hover:bg-slate-50 dark:hover:bg-[#162338] transition">
                            <td class="py-3 px-3 text-slate-400">{{ $event->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-3 font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $event->shipment->shipment_number ?? '-' }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $event->status }}
                                </span>
                            </td>
                            <td class="py-3 px-3 font-sans text-slate-600 dark:text-slate-300">
                                {{ $event->description }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif ($recentAuditLogs->isNotEmpty())
        <div class="bg-white dark:bg-[#121A28] border border-slate-200/80 dark:border-[#1F2C42] rounded-3xl p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white" data-i18n="profile_recent_audit_title">
                        Catatan Aktivitas Keamanan Akun
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400" data-i18n="profile_recent_audit_sub">
                        Audit trail keamanan atas aksi yang dilakukan akun ini.
                    </p>
                </div>
                @if ($user->isAdmin())
                <a href="{{ route('audit.index') }}" class="text-xs font-semibold text-cyan-600 dark:text-cyan-400 hover:underline" data-i18n="audit_title">
                    Lihat Seluruh Log Audit &rarr;
                </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-mono">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-[#1E293B] text-[10px] text-slate-400 uppercase tracking-wider">
                            <th class="py-2.5 px-3">Waktu</th>
                            <th class="py-2.5 px-3">Aksi</th>
                            <th class="py-2.5 px-3">Entitas</th>
                            <th class="py-2.5 px-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#182338]">
                        @foreach($recentAuditLogs as $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-[#162338] transition">
                            <td class="py-2.5 px-3 text-slate-400">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-[#1A273D] text-cyan-700 dark:text-cyan-300">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-700 dark:text-slate-300">
                                {{ $log->entity_type }} #{{ $log->entity_id }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-400">{{ $log->ip_address }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
