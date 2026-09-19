<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\Priority;
use App\Enums\Status;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TaskPageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_shows_tasks_of_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create();

        $response = $this->actingAs($user)->get("/app/projects/{$project->id}/tasks");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Tasks/Index')
            ->has('tasks', 1)
            ->where('tasks.0.id', $task->id)
        );
    }

    public function test_user_cannot_view_tasks_of_another_users_project(): void
    {
        $user = User::factory()->create();
        $otherProject = Project::factory()->for(User::factory())->create();

        $response = $this->actingAs($user)->get("/app/projects/{$otherProject->id}/tasks");

        $response->assertForbidden();
    }

    public function test_index_page_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $todoTask = Task::factory()->for($project)->create(['status' => Status::Todo]);
        Task::factory()->for($project)->create(['status' => Status::Completed]);

        $response = $this->actingAs($user)->get("/app/projects/{$project->id}/tasks?status=todo");

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Tasks/Index')
            ->has('tasks', 1)
            ->where('tasks.0.id', $todoTask->id)
        );
    }

    public function test_user_can_create_a_task(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $response = $this->actingAs($user)->post("/app/projects/{$project->id}/tasks", [
            'title' => 'Nuova task',
            'priority' => Priority::Medium->value,
            'status' => Status::Todo->value,
        ]);

        $response->assertRedirect(route('pages.projects.tasks.index', $project));
        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'title' => 'Nuova task',
        ]);
    }

    public function test_user_can_update_own_task(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create(['title' => 'Titolo originale']);

        $response = $this->actingAs($user)->put("/app/projects/{$project->id}/tasks/{$task->id}", [
            'title' => 'Titolo aggiornato',
        ]);

        $response->assertRedirect(route('pages.projects.tasks.index', $project));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Titolo aggiornato',
        ]);
    }

    public function test_user_can_delete_own_task(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create();

        $response = $this->actingAs($user)->delete("/app/projects/{$project->id}/tasks/{$task->id}");

        $response->assertRedirect(route('pages.projects.tasks.index', $project));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_can_complete_own_task(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create(['status' => Status::Todo]);

        $response = $this->actingAs($user)->patch("/app/projects/{$project->id}/tasks/{$task->id}/complete");

        $response->assertRedirect(route('pages.projects.tasks.index', $project));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => Status::Completed->value,
        ]);
    }
}
