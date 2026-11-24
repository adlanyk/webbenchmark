<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AuditDetail extends Model
{
    use HasFactory;

   protected $fillable = [
    'audit_id',
    'checklist_item_id',
    'score',
    'finding_note',
    'finding_photo',
    'action_plan',
    'due_date',
    'fix_photo',
    'fixed_at',
    'status',
    'verified_by',
    'verified_at',
];

    protected $casts = [
        'due_date' => 'date',
        'fixed_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    // Relasi: Detail ini milik Audit mana
    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    // Relasi: Detail ini merujuk ke Item Checklist mana
    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    // Relasi: Siapa yang memverifikasi perbaikan ini
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    protected function findingPhoto(): Attribute
{
    return Attribute::make(
        get: fn (?string $value) => $value ? preg_replace('#^/?storage/#', '', $value) : null,
    );
}

protected function fixPhoto(): Attribute
{
    return Attribute::make(
        get: fn (?string $value) => $value ? preg_replace('#^/?storage/#', '', $value) : null,
    );
}
}