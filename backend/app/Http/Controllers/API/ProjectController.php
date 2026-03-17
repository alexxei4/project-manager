<?php

namespace App\Http\Controllers\API;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $projects = Project::with('tasks')->latest()->paginate(10);
        return ProjectResource::collection($projects);
    }

    public function store(Request $request): ProjectResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:planning,active,completed,on-hold',
            'deadline' => 'nullable|date',
        ]);

        $project = Project::create($validated);
        
        return new ProjectResource($project->load('tasks'));
    }

    public function show(Project $project): ProjectResource
    {
        return new ProjectResource($project->load('tasks'));
        
    }

    public function update(Request $request, Project $project): ProjectResource
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:planning,active,completed,on-hold',
            'deadline' => 'nullable|date',
        ]);

        $project->update($validated);
        
        return new ProjectResource($project->load('tasks'));
    }

    public function destroy(Project $project)
    {
        $project->delete();
        
        return response()->json(null, 204);
    }
}