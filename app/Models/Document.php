<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $table = 'documents';

    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'service',
        'file_path',
        'source_name',
        'source_extension',
        'target_name',
        'target_extension',
        'targer_url',
        'customer_id',
        'download',
        'task_id',
        'website_id',
        'document_status_id',
    ];

    protected $casts = [
        'create_time' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Decoded source filename (stored as base64 in the shared schema).
     */
    public function getOriginalFilenameAttribute(): string
    {
        $decoded = base64_decode($this->name ?? '', true);
        return $decoded ?: ($this->name ?? '');
    }

    /**
     * Tool slug derived from service + extensions.
     */
    public function getToolSlugAttribute(): string
    {
        $service = $this->service ?? '';
        $src = strtolower($this->source_extension ?? '');
        $tgt = strtolower($this->target_extension ?? '');

        if ($service === 'merge') return 'merge';
        if ($service === 'compress') return 'compress';
        if ($service === 'sign-pdf') return 'sign';
        if ($service === 'split') return 'split';
        if ($src && $tgt) return "{$src}-to-{$tgt}";

        return $service ?: 'unknown';
    }

    /**
     * Status string for UI.
     */
    public function getStatusAttribute(): string
    {
        // document_status_id: 1=pending, 2=processing, 3=completed, 4=failed
        switch ($this->document_status_id) {
            case 3: return 'completed';
            case 4: return 'failed';
            case 2: return 'processing';
            default: return 'pending';
        }
    }
}
