<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'status',
        'requested_by',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function scopeRegistered(Builder $query): Builder
    {
        return $query->where('status', 'registered');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function isRegistered(): bool
    {
        return $this->status === 'registered';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
