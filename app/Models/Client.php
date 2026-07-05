<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'industry',
        'address',
        'website',
        'pic_name',
        'pic_phone',
        'pic_email',
        'pic_position',
        'contract_value',
        'contract_start',
        'contract_end',
        'status',
        'converted_from_lead_id',
        'notes',
    ];

    protected $casts = [
        'contract_value' => 'decimal:2',
        'contract_start' => 'date',
        'contract_end' => 'date',
    ];

    public const STATUS_OPTIONS = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'churned' => 'Churned',
    ];

    public const INDUSTRY_OPTIONS = [
        'technology' => 'Technology',
        'finance' => 'Finance & Banking',
        'healthcare' => 'Healthcare',
        'retail' => 'Retail & E-commerce',
        'manufacturing' => 'Manufacturing',
        'education' => 'Education',
        'government' => 'Government',
        'fmcg' => 'FMCG',
        'property' => 'Property & Real Estate',
        'energy' => 'Energy & Mining',
        'logistics' => 'Logistics & Transportation',
        'media' => 'Media & Entertainment',
        'telco' => 'Telecommunications',
        'other' => 'Other',
    ];

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            'churned' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Lead yang dikonversi menjadi client ini
     */
    public function convertedFromLead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'converted_from_lead_id');
    }

    /**
     * Project yang dimiliki client
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Semua interaksi dengan client
     */
    public function interactions(): MorphMany
    {
        return $this->morphMany(Interaction::class, 'interactable');
    }

    /**
     * Get total project value
     */
    public function getTotalProjectValueAttribute(): float
    {
        return $this->projects()->sum('budget') ?? 0;
    }

    /**
     * Get active projects count
     */
    public function getActiveProjectsCountAttribute(): int
    {
        return $this->projects()->where('status', 'active')->count();
    }
}
