<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Audit extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_number',
        'location_id',
        'reporter_name',
        'audit_date',
        'total_score',
        'total_items',
        'passed_items',
        'findings_count',
    ];

    protected $casts = [
        'audit_date' => 'date',
        'total_score' => 'decimal:2',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(AuditDetail::class);
    }

    public function recalculateScore(): void
    {
        $this->total_items = $this->details()->count();
        $this->passed_items = $this->details()->where('score', 1)->count();
        $this->findings_count = $this->details()->where('score', 0)->count();

        $this->total_score = $this->total_items > 0
            ? ($this->passed_items / $this->total_items)
            : 0;

        $this->save();
    }
}