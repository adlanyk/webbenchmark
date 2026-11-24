<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


#[Fillable(['name', 'email', 'password','department_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable,HasRoles;

    public function managedLocations(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(Location::class, 'pic_user_id');
}

public function isAuditorOrAdmin(): bool
{
    return in_array($this->role, ['admin', 'auditor']);
}

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
      // Relasi: User ini menjadi PIC di lokasi mana saja
    public function locationsAsPic(): HasMany
    {
        return $this->hasMany(Location::class, 'pic_user_id');
    }

    // Relasi: User ini pernah melakukan audit apa saja
    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class, 'auditor_id');
    }

    // Relasi: User ini pernah memverifikasi perbaikan apa saja
    public function verifiedAudits(): HasMany
    {
        return $this->hasMany(AuditDetail::class, 'verified_by');
    }
    public function department(): BelongsTo
{
    return $this->belongsTo(Department::class);
}
}
