<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Interaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'interactable_type',
        'interactable_id',
        'type',
        'subject',
        'notes',
        'outcome',
        'follow_up_at',
        'follow_up_completed',
        'user_id',
    ];

    protected $casts = [
        'follow_up_at' => 'datetime',
        'follow_up_completed' => 'boolean',
    ];

    public const TYPE_OPTIONS = [
        'call' => 'Phone Call',
        'whatsapp' => 'WhatsApp',
        'email' => 'Email',
        'meeting' => 'Meeting',
        'video_call' => 'Video Call',
        'other' => 'Other',
    ];

    public const OUTCOME_OPTIONS = [
        'positive' => 'Positive',
        'neutral' => 'Neutral',
        'negative' => 'Negative',
        'no_response' => 'No Response',
    ];

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'call' => 'info',
            'whatsapp' => 'success',
            'email' => 'primary',
            'meeting' => 'warning',
            'video_call' => 'purple',
            'other' => 'gray',
            default => 'gray',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'call' => 'heroicon-o-phone',
            'whatsapp' => 'heroicon-o-chat-bubble-left-ellipsis',
            'email' => 'heroicon-o-envelope',
            'meeting' => 'heroicon-o-user-group',
            'video_call' => 'heroicon-o-video-camera',
            'other' => 'heroicon-o-chat-bubble-bottom-center-text',
            default => 'heroicon-o-chat-bubble-bottom-center-text',
        };
    }

    public function getOutcomeColorAttribute(): string
    {
        return match ($this->outcome) {
            'positive' => 'success',
            'neutral' => 'gray',
            'negative' => 'danger',
            'no_response' => 'warning',
            default => 'gray',
        };
    }

    /**
     * Polymorphic relation to Lead or Client
     */
    public function interactable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * User who created the interaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if follow-up is due
     */
    public function getIsFollowUpDueAttribute(): bool
    {
        if (!$this->follow_up_at || $this->follow_up_completed) {
            return false;
        }
        return $this->follow_up_at->isPast() || $this->follow_up_at->isToday();
    }

    /**
     * Scope for pending follow-ups
     */
    public function scopePendingFollowUp($query)
    {
        return $query->whereNotNull('follow_up_at')
            ->where('follow_up_completed', false)
            ->where('follow_up_at', '<=', now()->endOfDay());
    }
}
