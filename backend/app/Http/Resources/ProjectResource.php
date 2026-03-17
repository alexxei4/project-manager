<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'project',
            'attributes' => [
                'name' => $this->name,
                'description' => $this->description,
                'status' => $this->status,
                'deadline' => $this->deadline?->format('Y-m-d'),
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
            ],
            'relationships' => [
                'tasks' => [
                    'data' => TaskResource::collection($this->whenLoaded('tasks')),
                ],
            ],
            'links' => [
                'self' => route('projects.show', $this->id),
            ],
        ];
    }
}