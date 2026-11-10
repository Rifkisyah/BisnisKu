<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id',
        'store_id',
        'contact',
        'status',
        'photo_profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'cashier_id');
    }

    public function serviceRepairs(): HasMany
    {
        return $this->hasMany(ServiceRepair::class, 'technician_id');
    }

    public function isOwner(): bool
    {
        return $this->role->name === 'owner';
    }

    public function isKasir(): bool
    {
        return $this->role->name === 'kasir';
    }

    public function isTeknisi(): bool
    {
        return $this->role->name === 'teknisi';
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role->name, $roles);
    }
}
