<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventsFiltersRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class EventsFiltersController extends Controller
{
    /**
     * @param EventsFiltersRequest $request
     * @return JsonResponse
     */
    public function getDates(EventsFiltersRequest $request): JsonResponse
    {
        $validatedRequest = $request->safe();

        $dates = new Collection;
        $start = ($validatedRequest['startFrom'] != '') ? Carbon::parse($validatedRequest['startFrom']) : Carbon::now();
        $end = match ($validatedRequest['period']) {
            'day' => $start->copy()->addDay(),
            'week' => $start->copy()->addWeek(),
            'month' => $start->copy()->addMonth(),
        };

        while ($start->lessThan($end)) {
            $dates->push($start->copy()->format('Y-m-d'));
            $start->addDay();
        }

        return response()->json([
            'status' => 'success',
            'dates' => $dates,
        ]);
    }

    /**
     * @param EventsFiltersRequest $request
     * @return JsonResponse
     */
    public function getAuthEvents(EventsFiltersRequest $request): JsonResponse
    {
        $validatedRequest = $request->safe();

        $start = Carbon::parse($validatedRequest['startFrom']);
        $end = match ($validatedRequest['period']) {
            'day' => $start->copy()->addDay(),
            'week' => $start->copy()->addWeek(),
            'month' => $start->copy()->addMonth(),
        };

        return response()->json([
            'status' => 'success',
            'events' => auth()->user()->events()->continueBetween($start, $end)->get(['id', 'title']),
        ]);
    }

    /**
     * @param EventsFiltersRequest $request
     * @return JsonResponse
     */
    public function getSharedEvents(EventsFiltersRequest $request): JsonResponse
    {
        try {
            $validatedRequest = $request->safe();
            $author = User::findOrFail($request['authorId']);

            $start = Carbon::parse($validatedRequest['startFrom']);
            $end = match ($validatedRequest['period']) {
                'day' => $start->copy()->addDay(),
                'week' => $start->copy()->addWeek(),
                'month' => $start->copy()->addMonth(),
            };

            return response()->json([
                'status' => 'success',
                'events' => $author->events()->continueBetween($start, $end)->get(['id', 'title']),
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
}
