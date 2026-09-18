<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService
    ) {
    }

    /**
     * Mostra tutti i progetti dell'utente autenticato.
     */
    public function index(Request $request)
    {
        $projects = $this->projectService->getByUser(
            $request->user()->id
        );

        return response()->json($projects);
    }

    /**
     * Crea un nuovo progetto.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = $this->projectService->create(
            $request->validated(),
            $request->user()
        );

        return response()->json($project, 201);
    }

    /**
     * Mostra un singolo progetto.
     */
    public function show(Project $project)
    {
        Gate::authorize('view', $project);

        return response()->json($project);
    }

    /**
     * Aggiorna un progetto.
     */
    public function update(
        UpdateProjectRequest $request,
        Project $project
    ) {
        Gate::authorize('update', $project);

        $project = $this->projectService->update(
            $project,
            $request->validated()
        );

        return response()->json($project);
    }

    /**
     * Elimina un progetto.
     */
    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);

        $this->projectService->delete($project);

        return response()->noContent();
    }
}