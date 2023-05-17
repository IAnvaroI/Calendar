<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class Tag
 *
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Tag extends Model
{
    /**
     * Get the events to which is assigned this tag.
     *
     * @return BelongsToMany
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class);
    }
}
