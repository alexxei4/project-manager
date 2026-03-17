<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'task',
            'attributes' => [
                'title' => $this->title,
                'description' => $this->description,
                'priority' => $this->priority,
                'completed' => $this->completed,
                'due_date' => $this->due_date?->format('Y-m-d'),
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
            ],
            'relationships' => [
                'project' => [
                    'data' => [
                        'id' => $this->project_id,
                        'type' => 'project',
                    ],
                ],
            ],
            'links' => [
                'self' => route('tasks.show', $this->id),
            ],
        ];
    }
}