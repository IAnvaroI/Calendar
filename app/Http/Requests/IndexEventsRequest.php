<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexEventsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'events' => 'nullable|array',
            'events.*' => 'numeric|exists:events,id',
            'tags' => 'nullable|array',
            'tags.*' => 'numeric|exists:tags,id',
            'dates' => 'nullable|array',
            'dates.*' => 'date',
            'startFrom' => 'nullable|date',
            'period' => 'nullable|in:day,week,month',
        ];
    }
}
