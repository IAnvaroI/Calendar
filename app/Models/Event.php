<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'start_datetime',
        'end_datetime',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    /**
     * Get the tags which are assigned to this event.
     *
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @param array $tagsIds
     * @return Collection
     */
    public static function getByTags(array $tagsIds): Collection
    {
        return static::whereHas('tags', function ($query) use ($tagsIds) {
            $query->whereIn('tags.id', $tagsIds);
        })->get();
    }

    /**
     * @param array $dates
     * @return Collection
     */
    public static function getByDates(array $dates): Collection
    {
        return static::all()->filter(function (Event $event) use ($dates) {
            $isPass = false;

            foreach ($dates as $date) {
                if ($event->start_datetime->format('Y-m-d') == $date) {
                    $isPass = true;
                    break;
                }
            }

            return $isPass;
        });
    }

    /**
     * @param string $date
     * @return Collection
     */
    public static function getFromDate(string $date): Collection
    {
        return static::whereDate('start_datetime', '>=', $date)->get();
    }
}
