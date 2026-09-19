<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_project(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/projects', [
                'name' => 'Progetto Laravel',
                'description' => 'Il mio primo progetto',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('projects', [
            'name' => 'Progetto Laravel',
            'description' => 'Il mio primo progetto',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_only_see_his_projects(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $myProject = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $otherProject = Project::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson('/projects');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $myProject->id,
        ]);

        $response->assertJsonMissing([
            'id' => $otherProject->id,
        ]);
    }

    public function test_user_can_see_his_own_project(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson("/projects/{$project->id}");

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $project->id,
        ]);
    }

    public function test_owner_can_update_own_project(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Nome originale',
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/projects/{$project->id}", [
                'name' => 'Nome aggiornato',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Nome aggiornato',
        ]);
    }

    public function test_user_cannot_update_another_users_project(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherProject = Project::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Nome originale',
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/projects/{$otherProject->id}", [
                'name' => 'Nome intruso',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('projects', [
            'id' => $otherProject->id,
            'name' => 'Nome originale',
        ]);
    }

    public function test_owner_can_delete_own_project(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->deleteJson("/projects/{$project->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_project(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherProject = Project::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->deleteJson("/projects/{$otherProject->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('projects', [
            'id' => $otherProject->id,
        ]);
    }

    public function test_creating_a_project_requires_a_name(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/projects', [
                'description' => 'Manca il nome',
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('name');
    }
}
