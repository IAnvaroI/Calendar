<?php

namespace App\Queries;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Class EventQueryBuilder.
 */
class EventQueryBuilder extends BaseQueryBuilder
{
    /**
     * Only events that are continued between the specified dates.
     *
     * @param CarbonInterface $start
     * @param CarbonInterface $end
     * @return static
     */
    public function continueBetween(CarbonInterface $start, CarbonInterface $end): static
    {
        $this->whereDate('start', '<=', $end)
            ->whereDate('end', '>=', $start);

        return $this;
    }

    /**
     * Only events that are starting after the specified date.
     *
     * @param CarbonInterface $date
     * @return static
     */
    public function startFrom(CarbonInterface $date): static
    {
        $this->whereDate('start', '>=', $date);

        return $this;
    }

    /**
     * Only events that have specified tags.
     *
     * @param array $tagsIds
     * @return static
     */
    public function whereTags(array $tagsIds): static
    {
        $this->whereHas('tags', function ($query) use ($tagsIds) {
            $query->whereIn('tags.id', $tagsIds);
        });

        return $this;
    }

    /**
     * Only events that started in specified dates.
     *
     * @param array $dates
     * @return static
     */
    public function whereDates(array $dates): static
    {
        $this->whereIn(DB::raw('DATE(start)'), $dates);

        return $this;
    }
}
