@php
    $size = $size ?? 'w-6 h-6';
    // Map size class to pixel value for Lucide
    if (strpos($size, 'w-8') !== false) { $px = 32; }
    elseif (strpos($size, 'w-6') !== false) { $px = 24; }
    elseif (strpos($size, 'w-5') !== false) { $px = 20; }
    elseif (strpos($size, 'w-4') !== false) { $px = 16; }
    else { $px = 24; }
    $colorMap = [
        'merge'        => 'text-brand-500',
        'compress'     => 'text-amber-500',
        'image'        => 'text-emerald-500',
        'word'         => 'text-blue-600',
        'split'        => 'text-violet-500',
        'edit'         => 'text-orange-500',
        'sign'         => 'text-rose-500',
        'excel'        => 'text-green-600',
        'rotate'       => 'text-sky-500',
        'lock'         => 'text-red-600',
        'unlock'       => 'text-teal-500',
        'watermark'    => 'text-cyan-500',
        'page-numbers' => 'text-indigo-500',
        'presentation' => 'text-orange-600',
        'ocr'          => 'text-purple-500',
        'remove-pages' => 'text-red-500',
        'extract-pages'=> 'text-blue-500',
        'code'         => 'text-gray-600',
        'optimize'     => 'text-yellow-500',
        'default'      => 'text-slate-400',
    ];
    $iconMap = [
        'merge'        => 'merge',
        'compress'     => 'minimize-2',
        'image'        => 'image',
        'word'         => 'file-text',
        'split'        => 'scissors',
        'edit'         => 'pencil',
        'sign'         => 'pen-tool',
        'excel'        => 'table',
        'rotate'       => 'rotate-cw',
        'lock'         => 'lock',
        'unlock'       => 'unlock',
        'watermark'    => 'droplets',
        'page-numbers' => 'hash',
        'presentation' => 'monitor',
        'ocr'          => 'scan-text',
        'remove-pages' => 'trash-2',
        'extract-pages'=> 'file-output',
        'code'         => 'code',
        'optimize'     => 'zap',
        'default'      => 'file',
    ];
    $iconName = $iconMap[$icon ?? 'default'] ?? $iconMap['default'];
    $color = $colorMap[$icon ?? 'default'] ?? $colorMap['default'];
@endphp
<i data-lucide="{{ $iconName }}" class="{{ $size }} {{ $color }}" style="width:{{ $px }}px;height:{{ $px }}px"></i>
