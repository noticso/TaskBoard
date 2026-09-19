<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class TaskPageController extends Controller
{
    public function __construct(
        private TaskService $taskService,
    ) {}

    /**
     * Mostra la pagina con l'elenco delle task del progetto, filtrabile per status/priority.
     */
    public function index(Project $project, Request $request): Response
    {
        Gate::authorize('view', $project);

        $status = $request->query('status');
        $priority = $request->query('priority');

        return Inertia::render('Tasks/Index', [
            'project' => $project,
            'tasks' => $this->taskService->getByProject($project->id, [
                'status' => $status,
                'priority' => $priority,
            ]),
            'filters' => [
                'status' => $status,
                'priority' => $priority,
            ],
        ]);
    }

    /**
     * Mostra la pagina per creare una nuova task nel progetto.
     */
    public function create(Project $project): Response
    {
        Gate::authorize('create', [Task::class, $project]);

        return Inertia::render('Tasks/Create', [
            'project' => $project,
        ]);
    }

    /**
     * Crea la task e torna alla lista.
     */
    public function store(Project $project, StoreTaskRequest $request): RedirectResponse
    {
        Gate::authorize('create', [Task::class, $project]);

        $this->taskService->create($project->id, $request->validated());

        return redirect()->route('pages.projects.tasks.index', $project);
    }

    /**
     * Mostra la pagina per modificare una task.
     */
    public function edit(Project $project, Task $task): Response
    {
        Gate::authorize('update', $task);

        return Inertia::render('Tasks/Edit', [
            'project' => $project,
            'task' => $task,
        ]);
    }

    /**
     * Aggiorna la task e torna alla lista.
     */
    public function update(Project $project, UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);

        $this->taskService->update($task, $request->validated());

        return redirect()->route('pages.projects.tasks.index', $project);
    }

    /**
     * Elimina la task e torna alla lista.
     */
    public function destroy(Project $project, Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        $this->taskService->delete($task);

        return redirect()->route('pages.projects.tasks.index', $project);
    }

    /**
     * Segna la task come completata.
     */
    public function complete(Project $project, Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);

        $this->taskService->complete($task);

        return redirect()->route('pages.projects.tasks.index', $project);
    }
}
