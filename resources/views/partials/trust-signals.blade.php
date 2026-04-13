@php $isEn = app()->getLocale() === 'en'; @endphp
<div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs text-slate-500 mt-6">
    <span class="flex items-center gap-1.5">
        <i data-lucide="lock" class="w-4 h-4 text-brand-500"></i>
        {{ $isEn ? 'SSL encrypted' : 'SSL-verschlüsselt' }}
    </span>
    <span class="flex items-center gap-1.5">
        <i data-lucide="globe" class="w-4 h-4 text-brand-500"></i>
        {{ $isEn ? 'Servers in Europe' : 'Server in Europa' }}
    </span>
    <span class="flex items-center gap-1.5">
        <i data-lucide="timer" class="w-4 h-4 text-brand-500"></i>
        {{ $isEn ? 'Auto-deletion after 1h' : 'Auto-Löschung nach 1h' }}
    </span>
    <span class="flex items-center gap-1.5">
        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
        {{ $isEn ? 'No watermark' : 'Kein Wasserzeichen' }}
    </span>
</div>
