<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'sort_order',
        'question',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi: Item ini milik Lokasi mana
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    // Relasi: Item ini bisa muncul di banyak detail audit
    public function auditDetails(): HasMany
    {
        return $this->hasMany(AuditDetail::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}