<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService,
    ) {}

    public function index(Project $project, Request $request)
    {
        Gate::authorize('view', $project);
        $status = $request->query('status');
        $priority = $request->query('priority');
        $tasks = $this->taskService->getByProject($project->id, ['status' => $status, 'priority' => $priority]);

        return response()->json($tasks);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Project $project, StoreTaskRequest $request)
    {
        Gate::authorize('create', [Task::class, $project]);
        $task = $this->taskService->create($project->id, $request->validated());

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Task $task)
    {
        Gate::authorize('view', $task);

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Project $project, UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);
        $updatedTask = $this->taskService->update($task, $request->validated());

        return response()->json($updatedTask);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Task $task)
    {
        Gate::authorize('delete', $task);
        $this->taskService->delete($task);

        return response()->noContent();
    }
}
