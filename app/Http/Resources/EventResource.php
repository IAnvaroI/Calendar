<?php

namespace App\Http\Resources;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tags = $this->tags->map(function (Tag $tag) {
            return ['id' => $tag->id, 'name' => $tag->name];
        });

        return [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->start->format('Y-m-d H:i'),
            'end' => $this->end->format('Y-m-d H:i'),
            'tags' => $tags,
        ];
    }
}
