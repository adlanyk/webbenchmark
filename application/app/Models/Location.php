<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'code',
        'name',
        'pic_user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi: Lokasi ini milik Departemen mana
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // Relasi: Siapa PIC dari lokasi ini
    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    // Relasi: Lokasi ini punya banyak item checklist
    public function checklistItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class);
    }

    // Relasi: Lokasi ini memiliki banyak riwayat audit
    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }
}