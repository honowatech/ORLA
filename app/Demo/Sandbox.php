<?php

namespace App\Demo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Un bac démo : une ligne du registre (base principale) et un fichier SQLite.
 *
 * @property int $id
 * @property string $name
 * @property int $seed
 * @property \Illuminate\Support\Carbon|null $built_on
 * @property \Illuminate\Support\Carbon|null $built_at
 * @property \Illuminate\Support\Carbon|null $claimed_at
 * @property string|null $claim_token
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon|null $ended_at
 */
class Sandbox extends Model
{
    protected $table = 'demo_sandboxes';

    // Rien n'est assignable en masse : chaque colonne est posée explicitement.
    protected $guarded = ['*'];

    protected $casts = [
        'built_on' => 'date',
        'built_at' => 'datetime',
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
        'ends_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Le registre est toujours lu dans la base principale, même quand la
     * connexion par défaut pointe sur un bac.
     */
    public function getConnectionName()
    {
        return Demo::mainConnection();
    }

    public function path(): string
    {
        return Demo::sandboxPath($this->name);
    }

    /** Remis à un visiteur, ni terminé ni expiré. */
    public function isOpen(): bool
    {
        return $this->claimed_at !== null
            && $this->ended_at === null
            && $this->expires_at !== null && $this->expires_at->isFuture()
            && $this->ends_at !== null && $this->ends_at->isFuture();
    }

    /** Prolonge l'inactivité, au plus une fois par minute, sans dépasser la durée maximale. */
    public function touchActivity(): void
    {
        $expires = now()->addMinutes(config('demo.idle_minutes'));
        if ($expires->greaterThan($this->ends_at)) {
            $expires = $this->ends_at->copy();
        }
        if ($this->expires_at !== null && $expires->diffInSeconds($this->expires_at, true) < 60) {
            return;
        }
        $this->expires_at = $expires;
        $this->save();
    }

    /** Bacs remis à un visiteur et encore ouverts : ceux que compte le plafond. */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotNull('claimed_at')
            ->whereNull('ended_at')
            ->where('expires_at', '>', now())
            ->where('ends_at', '>', now());
    }
}
