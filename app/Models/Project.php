<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'type',
        'deadline',
        'progress',
        'status',
        'budget',
        'description',
        'contract_file',
        'report_file',
    ];

    protected $casts = [
        'deadline' => 'date',
        'budget' => 'decimal:2',
        'progress' => 'integer',

        'contract_file' => 'array',
        'report_file' => 'array',
    ];

    public const TYPE_OPTIONS = [
        'development' => 'Development',
        'marketing' => 'Marketing',
        'execution' => 'Execution',
        'design' => 'Design',
        'consulting' => 'Consulting',
        'research' => 'Market Research',
    ];

    public const STATUS_OPTIONS = [
        'active' => 'Active',
        'completed' => 'Completed',
        'on_hold' => 'On Hold',
        'cancelled' => 'Cancelled',
    ];

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'development' => 'info',
            'marketing' => 'warning',
            'execution' => 'success',
            'design' => 'primary',
            'consulting' => 'gray',
            'research' => 'purple',
            default => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'completed' => 'info',
            'on_hold' => 'warning',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_OPTIONS[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? $this->status;
    }

    /**
     * Client that owns this project
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->deadline) {
            return null;
        }
        return now()->diffInDays($this->deadline, false);
    }

    /**
     * Check if project is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->deadline || $this->status === 'completed') {
            return false;
        }
        return $this->deadline->isPast();
    }
}
