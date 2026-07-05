<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'status',
        'source',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Scope for active subscribers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if email is already subscribed
     */
    public static function isSubscribed(string $email): bool
    {
        return self::where('email', $email)->where('status', 'active')->exists();
    }

    /**
     * Subscribe an email
     */
    public static function subscribe(string $email, ?string $source = null): self
    {
        $subscriber = self::firstOrNew(['email' => $email]);
        $subscriber->status = 'active';
        $subscriber->source = $source ?? $subscriber->source;
        $subscriber->subscribed_at = now();
        $subscriber->unsubscribed_at = null;
        $subscriber->save();
        
        return $subscriber;
    }

    /**
     * Unsubscribe an email
     */
    public function unsubscribe(): void
    {
        $this->status = 'unsubscribed';
        $this->unsubscribed_at = now();
        $this->save();
    }
}
