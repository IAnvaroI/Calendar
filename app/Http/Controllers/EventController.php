<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Http\Requests\IndexEventsRequest;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Http\Resources\ResourcePaginator;
use App\Models\Event;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Models\User;
use function PHPUnit\Framework\isEmpty;

class EventController extends Controller
{
    /**
     * Display a listing of the shared events.
     * @throws Exception
     */
    public function sharedIndex(IndexEventsRequest $request): JsonResponse
    {
        try {
            $validatedRequest = $request->safe()->toArray();
            $author = User::findOrFail($request['authorId']);
            $query = $author->events();

            if($request['filters'] && count($request['filters'])){
                $query->where(function (Builder $query) use ($request) {
                    $this->filterEvents($query, $request['filters']);
                });
            }

            $this->filterEvents($query, $validatedRequest);

            return response()->json([
                'status' => 'success',
                'events' => new ResourcePaginator(
                    $query->paginate(config('pagination.per_page')),
                    EventCollection::class
                ),
                'blockingFilters' => $request['filters'],
            ]);
        } catch (Throwable $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'errors' => [
                    'dbError' => ['Database error occurred.'],
                ],
            ], 404);
        }
    }

    /**
     * Display a listing of the events.
     * @throws Exception
     */
    public function index(IndexEventsRequest $request): JsonResponse
    {
        $validatedRequest = $request->safe()->toArray();
        $query = auth()->user()->events();

        $this->filterEvents($query, $validatedRequest);

        return response()->json([
            'status' => 'success',
            'events' => new ResourcePaginator(
                $query->paginate(config('pagination.per_page')),
                EventCollection::class
            ),
        ]);
    }

    /**
     * Store a newly created event in storage.
     *
     * @param EventRequest $request
     * @return JsonResponse
     */
    public function store(EventRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validatedRequest = $request->safe();

            $event = Event::create([
                'title' => $validatedRequest['title'],
                'start' => $validatedRequest['start'],
                'end' => $validatedRequest['end'],
                'user_id' => auth()->user()->id,
            ]);

            $event->tags()->attach($validatedRequest['tags']);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();

            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'errors' => [
                    'dbError' => ['Database error occurred.'],
                ],
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'The event creation completed successfully.',
        ]);
    }

    /**
     * Show the form for editing the specified event.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function edit(string $id): JsonResponse
    {
        try {
            $event = Event::findOrFail($id);
        } catch (Throwable $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'errors' => [
                    'dbError' => ['Specified event was not found.'],
                ],
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'event' => new EventResource($event),
        ]);
    }

    /**
     * Update the specified event in storage.
     *
     * @param EventRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(EventRequest $request, string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $tagsIds = $request->safe()->only('tags')['tags'];
            $baseColumns = $request->safe()->only([
                'title',
                'start',
                'end'
            ]);
            $event = Event::findOrFail($id);

            $event->update($baseColumns);
            $event->tags()->sync($tagsIds);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();

            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'errors' => [
                    'dbError' => ['Database error occurred.'],
                ],
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'The event update completed successfully.',
        ]);
    }

    /**
     * Remove the specified event from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $event = Event::findOrFail($id);

            $event->tags()->detach();
            $event->delete();
        } catch (Throwable $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'errors' => [
                    'dbError' => ['Database error occurred.'],
                ],
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'The event deletion completed successfully.',
        ]);
    }

    /**
     * Use filtering methods on the query with specified filters
     *
     * @param $query
     * @param array $filters
     * @return void
     */
    private function filterEvents($query, array $filters): void
    {
        if (array_key_exists('events', $filters) && $filters['events']) {
            $query->whereIn('id', $filters['events']);
        }
        if (array_key_exists('tags', $filters) && $filters['tags']) {
            $query->whereTags($filters['tags']);
        }
        if (array_key_exists('dates', $filters) && $filters['dates']) {
            $query->whereDates($filters['dates']);
        }
        if (array_key_exists('startFrom', $filters) && $filters['startFrom']) {
            $start = Carbon::parse($filters['startFrom']);
        } else {
            $start = Carbon::now();
        }
        if (array_key_exists('period', $filters) && $filters['period']) {
            $end = match ($filters['period']) {
                'day' => $start->copy()->addDay(),
                'week' => $start->copy()->addWeek(),
                'month' => $start->copy()->addMonth(),
            };
            $query->continueBetween($start, $end);
        } else {
            $query->startFrom($start);
        }
    }
}
