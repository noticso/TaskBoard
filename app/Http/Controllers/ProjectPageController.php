<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ProjectPageController extends Controller
{
    public function __construct(
        private ProjectService $projectService
    ) {}

    /**
     * Mostra la pagina con l'elenco dei progetti dell'utente autenticato.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Projects/Index', [
            'projects' => $this->projectService->getByUser($request->user()->id),
        ]);
    }

    /**
     * Mostra la pagina per creare un nuovo progetto.
     */
    public function create(): Response
    {
        return Inertia::render('Projects/Create');
    }

    /**
     * Crea il progetto e torna alla lista.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $this->projectService->create($request->validated(), $request->user());

        return redirect()->route('pages.projects.index');
    }

    /**
     * Mostra la pagina di dettaglio di un progetto.
     */
    public function show(Project $project): Response
    {
        Gate::authorize('view', $project);

        return Inertia::render('Projects/Show', [
            'project' => $project,
        ]);
    }

    /**
     * Mostra la pagina per modificare un progetto.
     */
    public function edit(Project $project): Response
    {
        Gate::authorize('update', $project);

        return Inertia::render('Projects/Edit', [
            'project' => $project,
        ]);
    }

    /**
     * Aggiorna il progetto e torna al dettaglio.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $this->projectService->update($project, $request->validated());

        return redirect()->route('pages.projects.show', $project);
    }

    /**
     * Elimina il progetto e torna alla lista.
     */
    public function destroy(Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);

        $this->projectService->delete($project);

        return redirect()->route('pages.projects.index');
    }
}
