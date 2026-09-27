<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EmailVerification extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'email_verifications';

    protected $fillable = [
        'user_id',
        'token_hash',
        'expires_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a cryptographically secure token and persist its SHA-256 hash.
     */
    public static function createTokenFor(User $user, int $lifetimeMinutes = 30): string
    {
        // Delete any existing verification tokens for this user
        static::where('user_id', $user->id)->delete();

        $rawToken = Str::random(64);

        static::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $rawToken),
            'expires_at' => now()->addMinutes($lifetimeMinutes),
        ]);

        return $rawToken;
    }

    /**
     * Verify a raw token, mark user email as verified, and consume token.
     */
    public static function verify(string $rawToken): ?User
    {
        $tokenHash = hash('sha256', trim($rawToken));

        /** @var self|null $verification */
        $verification = static::with('user')
            ->where('token_hash', $tokenHash)
            ->where('expires_at', '>=', now())
            ->first();

        if (! $verification || ! $verification->user) {
            return null;
        }

        $user = $verification->user;
        $user->email_verified_at = now();
        $user->save();

        // Invalidate all tokens for this user
        static::where('user_id', $user->id)->delete();

        return $user;
    }
}
