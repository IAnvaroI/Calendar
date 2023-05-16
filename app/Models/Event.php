<?php

namespace App\Models;

use App\Queries\EventQueryBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder as DatabaseBuilder;

/**
 * Class Event.
 *
 * @property string $title
 * @property Carbon $start
 * @property Carbon $end
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static EventQueryBuilder query()
 */
class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'start',
        'end',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    /**
     * @param DatabaseBuilder $query
     *
     * @return EventQueryBuilder
     */
    public function newEloquentBuilder($query): EventQueryBuilder
    {
        return new EventQueryBuilder($query);
    }

    /**
     * Get the author of the event.
     *
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tags which are assigned to this event.
     *
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }
}
