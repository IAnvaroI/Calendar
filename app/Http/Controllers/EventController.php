<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     * @throws Exception
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'events' => EventResource::collection(auth()->user()->events),]);
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
}
