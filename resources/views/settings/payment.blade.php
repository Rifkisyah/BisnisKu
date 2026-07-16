@extends('layouts.app')
@section('page-title', 'Pengaturan Pembayaran')
@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div class="mb-2 -ml-2">
        <a href="{{ route('settings.index') }}" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
    </div>

    <div class="card-feature p-8" x-data="{ mode: '{{ old('qris_mode', $paymentSetting->qris_mode) }}' }">
        <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-[var(--color-primary)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
            Pengaturan QRIS
        </h3>
        <form method="POST" action="{{ route('settings.payment.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="space-y-3">
                <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Mode QRIS</label>
                
                <label class="flex items-start gap-3 p-4 border border-[var(--color-hairline-soft)] rounded-[var(--radius-xl)] cursor-pointer hover:bg-[var(--color-surface-soft)] transition-colors" :class="mode === 'manual' ? 'border-[var(--color-primary)] bg-[var(--color-primary)]/5' : ''">
                    <input type="radio" name="qris_mode" value="manual" x-model="mode" class="mt-1">
                    <div>
                        <p class="type-body-sm-bold text-[var(--color-ink)]">QRIS Manual / Statis</p>
                        <p class="type-caption text-[var(--color-slate)] mt-1">Gunakan gambar QRIS yang sudah ada, seperti QRIS DANA Business cetak. Pembayaran perlu dikonfirmasi manual oleh kasir.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-4 border border-[var(--color-hairline-soft)] rounded-[var(--radius-xl)] cursor-pointer hover:bg-[var(--color-surface-soft)] transition-colors" :class="mode === 'dynamic' ? 'border-[var(--color-primary)] bg-[var(--color-primary)]/5' : ''">
                    <input type="radio" name="qris_mode" value="dynamic" x-model="mode" class="mt-1">
                    <div>
                        <p class="type-body-sm-bold text-[var(--color-ink)]">QRIS Otomatis / Dinamis</p>
                        <p class="type-caption text-[var(--color-slate)] mt-1">QRIS dibuat otomatis untuk setiap transaksi. Pembayaran akan dikonfirmasi otomatis melalui webhook payment gateway.</p>
                    </div>
                </label>
            </div>

            {{-- Manual QRIS Config --}}
            <div x-show="mode === 'manual'" class="space-y-4 pt-4 border-t border-[var(--color-hairline-soft)]" x-cloak>
                <h4 class="type-body-sm-bold text-[var(--color-ink)]">Konfigurasi Manual</h4>
                <div>
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">Gambar QRIS Statis</label>
                    @if(!empty($paymentSetting->manual_qris_image))
                        <img src="{{ asset($paymentSetting->manual_qris_image) }}" alt="QRIS" class="w-32 h-32 mb-2 rounded border border-gray-200 object-cover">
                    @endif
                    <input type="file" name="manual_qris_image" accept="image/*" class="input-field !p-1.5">
                    <p class="type-caption text-[var(--color-stone)] mt-1">Upload gambar barcode QRIS yang akan discan oleh customer.</p>
                </div>
            </div>

            {{-- Dynamic QRIS Config --}}
            <div x-show="mode === 'dynamic'" class="space-y-4 pt-4 border-t border-[var(--color-hairline-soft)]" x-cloak>
                <h4 class="type-body-sm-bold text-[var(--color-ink)]">Konfigurasi API / Payment Gateway</h4>
                <div class="mb-4 p-3 bg-blue-50 text-blue-800 rounded-lg text-sm border border-blue-200">
                    <strong>Catatan:</strong> Untuk keamanan, Client Key, Server Key, dan Webhook URL diatur secara teknis oleh developer di konfigurasi backend (file .env). Anda hanya perlu memilih provider dan memasukkan Merchant ID.
                </div>
                <div>
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">Provider QRIS</label>
                    <select name="qris_provider" class="input-field">
                        <option value="">Pilih Provider</option>
                        <option value="midtrans" {{ old('qris_provider', $paymentSetting->qris_provider) == 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                        <option value="xendit" {{ old('qris_provider', $paymentSetting->qris_provider) == 'xendit' ? 'selected' : '' }}>Xendit</option>
                        <option value="dana" {{ old('qris_provider', $paymentSetting->qris_provider) == 'dana' ? 'selected' : '' }}>DANA API</option>
                    </select>
                </div>
                <div>
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">Merchant ID</label>
                    <input type="text" name="merchant_id" value="{{ old('merchant_id', $paymentSetting->merchant_id) }}" class="input-field">
                </div>
            </div>

            {{-- Bank Config --}}
            <div class="space-y-4 pt-6 border-t border-[var(--color-hairline-soft)] mt-6">
                <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-2 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-[var(--color-primary)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" /></svg>
                    Pengaturan Rekening Bank (Transfer)
                </h3>
                <p class="type-caption text-[var(--color-slate)] mb-4">Informasi rekening bank yang akan ditampilkan saat kasir memilih metode pembayaran Transfer.</p>
                
                <div>
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">Nama Bank</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $paymentSetting->bank_name) }}" class="input-field" placeholder="Contoh: BCA / Mandiri / BRI">
                </div>
                <div>
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">Nomor Rekening</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $paymentSetting->bank_account_number) }}" class="input-field" placeholder="Contoh: 1234567890">
                </div>
                <div>
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">Atas Nama</label>
                    <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $paymentSetting->bank_account_name) }}" class="input-field" placeholder="Contoh: Toko BisnisKu">
                </div>
            </div>

            <button type="submit" class="btn-primary !py-2.5 !px-5 w-full mt-4">{{ __('messages.save') }} Pengaturan</button>
        </form>
    </div>
</div>
@endsection
