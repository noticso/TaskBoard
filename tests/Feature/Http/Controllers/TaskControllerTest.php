<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\Priority;
use App\Enums\Status;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_task_in_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $response = $this
            ->actingAs($user)
            ->postJson("/projects/{$project->id}/tasks", [
                'title' => 'Scrivere i test',
                'description' => 'Coprire il TaskController',
                'priority' => Priority::Medium->value,
                'status' => Status::Todo->value,
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'title' => 'Scrivere i test',
        ]);
    }

    public function test_user_cannot_create_a_task_in_another_users_project(): void
    {
        $user = User::factory()->create();
        $otherProject = Project::factory()->for(User::factory())->create();

        $response = $this
            ->actingAs($user)
            ->postJson("/projects/{$otherProject->id}/tasks", [
                'title' => 'Task intrusa',
                'priority' => Priority::Low->value,
                'status' => Status::Todo->value,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $otherProject->id,
        ]);
    }

    public function test_creating_a_task_requires_a_title(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $response = $this
            ->actingAs($user)
            ->postJson("/projects/{$project->id}/tasks", [
                'priority' => Priority::Low->value,
                'status' => Status::Todo->value,
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('title');
    }

    public function test_cannot_create_more_than_20_incomplete_tasks_in_a_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        Task::factory()->for($project)->count(20)->create([
            'status' => Status::Todo,
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson("/projects/{$project->id}/tasks", [
                'title' => 'Task numero 21',
                'priority' => Priority::Low->value,
                'status' => Status::Todo->value,
            ]);

        $response->assertUnprocessable();

        $this->assertSame(20, Task::where('project_id', $project->id)->count());
    }

    public function test_user_can_list_tasks_of_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create();

        $response = $this
            ->actingAs($user)
            ->getJson("/projects/{$project->id}/tasks");

        $response->assertOk();
        $response->assertJsonFragment(['id' => $task->id]);
    }

    public function test_user_cannot_list_tasks_of_another_users_project(): void
    {
        $user = User::factory()->create();
        $otherProject = Project::factory()->for(User::factory())->create();
        Task::factory()->for($otherProject)->create();

        $response = $this
            ->actingAs($user)
            ->getJson("/projects/{$otherProject->id}/tasks");

        $response->assertForbidden();
    }

    public function test_task_list_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $todoTask = Task::factory()->for($project)->create(['status' => Status::Todo]);
        $completedTask = Task::factory()->for($project)->create(['status' => Status::Completed]);

        $response = $this
            ->actingAs($user)
            ->getJson("/projects/{$project->id}/tasks?status=".Status::Completed->value);

        $response->assertOk();
        $response->assertJsonFragment(['id' => $completedTask->id]);
        $response->assertJsonMissing(['id' => $todoTask->id]);
    }

    public function test_task_list_can_be_filtered_by_priority(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $highPriorityTask = Task::factory()->for($project)->create(['priority' => Priority::High]);
        $lowPriorityTask = Task::factory()->for($project)->create(['priority' => Priority::Low]);

        $response = $this
            ->actingAs($user)
            ->getJson("/projects/{$project->id}/tasks?priority=".Priority::High->value);

        $response->assertOk();
        $response->assertJsonFragment(['id' => $highPriorityTask->id]);
        $response->assertJsonMissing(['id' => $lowPriorityTask->id]);
    }

    public function test_user_can_view_a_single_task_of_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create();

        $response = $this
            ->actingAs($user)
            ->getJson("/projects/{$project->id}/tasks/{$task->id}");

        $response->assertOk();
        $response->assertJsonFragment(['id' => $task->id]);
    }

    public function test_user_cannot_view_a_task_of_another_users_project(): void
    {
        $user = User::factory()->create();
        $otherProject = Project::factory()->for(User::factory())->create();
        $task = Task::factory()->for($otherProject)->create();

        $response = $this
            ->actingAs($user)
            ->getJson("/projects/{$otherProject->id}/tasks/{$task->id}");

        $response->assertForbidden();
    }

    public function test_task_not_belonging_to_project_in_url_returns_not_found(): void
    {
        $user = User::factory()->create();
        $projectA = Project::factory()->for($user)->create();
        $projectB = Project::factory()->for($user)->create();
        $taskOfProjectB = Task::factory()->for($projectB)->create();

        $response = $this
            ->actingAs($user)
            ->getJson("/projects/{$projectA->id}/tasks/{$taskOfProjectB->id}");

        $response->assertNotFound();
    }

    public function test_user_can_update_a_task_of_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create(['title' => 'Titolo originale']);

        $response = $this
            ->actingAs($user)
            ->patchJson("/projects/{$project->id}/tasks/{$task->id}", [
                'title' => 'Titolo aggiornato',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Titolo aggiornato',
        ]);
    }

    public function test_user_cannot_update_a_task_of_another_users_project(): void
    {
        $user = User::factory()->create();
        $otherProject = Project::factory()->for(User::factory())->create();
        $task = Task::factory()->for($otherProject)->create(['title' => 'Titolo originale']);

        $response = $this
            ->actingAs($user)
            ->patchJson("/projects/{$otherProject->id}/tasks/{$task->id}", [
                'title' => 'Titolo modificato',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Titolo originale',
        ]);
    }

    public function test_completed_task_cannot_be_reverted_to_in_progress(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create(['status' => Status::Completed]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/projects/{$project->id}/tasks/{$task->id}", [
                'status' => Status::InProgress->value,
            ]);

        $response->assertUnprocessable();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => Status::Completed->value,
        ]);
    }

    public function test_user_can_delete_a_task_of_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create();

        $response = $this
            ->actingAs($user)
            ->deleteJson("/projects/{$project->id}/tasks/{$task->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_delete_a_task_of_another_users_project(): void
    {
        $user = User::factory()->create();
        $otherProject = Project::factory()->for(User::factory())->create();
        $task = Task::factory()->for($otherProject)->create();

        $response = $this
            ->actingAs($user)
            ->deleteJson("/projects/{$otherProject->id}/tasks/{$task->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_user_can_complete_a_task_of_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create(['title' => 'Titolo originale']);

        $response = $this
            ->actingAs($user)
            ->patchJson("/projects/{$project->id}/tasks/{$task->id}/complete");

        $response->assertOk();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => Status::Completed->value,
        ]);
    }

    public function test_user_cannot_complete_a_task_of_another_users_project(): void
    {
        $user = User::factory()->create();
        $otherProject = Project::factory()->for(User::factory())->create();
        $task = Task::factory()->for($otherProject)->create();

        $response = $this
            ->actingAs($user)
            ->patchJson("/projects/{$otherProject->id}/tasks/{$task->id}/complete");

        $response->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_completing_an_already_completed_task_returns_error(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        $task = Task::factory()->for($project)->create(['title' => 'Titolo originale', 'status' => Status::Completed->value]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/projects/{$project->id}/tasks/{$task->id}/complete");

        $response->assertUnprocessable();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => Status::Completed->value,
        ]);
    }
}
