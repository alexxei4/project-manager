<?php

namespace App\Http\Controllers\API;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with('project');
        
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        $tasks = $query->latest()->paginate(10);
        
        return TaskResource::collection($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $validated['completed'] = false;
        
        $task = Task::create($validated);
        
        return new TaskResource($task->load('project'));
    }

    public function show(Task $task)
    {
        return new TaskResource($task->load('project'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'sometimes|in:low,medium,high',
            'completed' => 'sometimes|boolean',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);
        
        return new TaskResource($task->load('project'));
    }

    public function destroy(Task $task)
    {
        $task->delete();
        
        return response()->json(null, 204);
    }
}