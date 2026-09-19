<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProjectPageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/app/projects');

        $response->assertRedirect(route('login'));
    }

    public function test_index_page_shows_only_own_projects(): void
    {
        $user = User::factory()->create();
        $ownProject = Project::factory()->for($user)->create();
        Project::factory()->for(User::factory())->create();

        $response = $this->actingAs($user)->get('/app/projects');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Projects/Index')
            ->has('projects', 1)
            ->where('projects.0.id', $ownProject->id)
        );
    }

    public function test_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/app/projects/create');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page->component('Projects/Create'));
    }

    public function test_user_can_create_a_project(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/app/projects', [
            'name' => 'Nuovo progetto',
            'description' => 'Descrizione',
        ]);

        $response->assertRedirect(route('pages.projects.index'));
        $this->assertDatabaseHas('projects', [
            'user_id' => $user->id,
            'name' => 'Nuovo progetto',
        ]);
    }

    public function test_user_can_view_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $response = $this->actingAs($user)->get("/app/projects/{$project->id}");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Projects/Show')
            ->where('project.id', $project->id)
        );
    }

    public function test_user_cannot_view_another_users_project(): void
    {
        $user = User::factory()->create();
        $otherProject = Project::factory()->for(User::factory())->create();

        $response = $this->actingAs($user)->get("/app/projects/{$otherProject->id}");

        $response->assertForbidden();
    }

    public function test_user_can_update_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create(['name' => 'Titolo originale']);

        $response = $this->actingAs($user)->put("/app/projects/{$project->id}", [
            'name' => 'Titolo aggiornato',
        ]);

        $response->assertRedirect(route('pages.projects.show', $project));
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Titolo aggiornato',
        ]);
    }

    public function test_user_can_delete_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete("/app/projects/{$project->id}");

        $response->assertRedirect(route('pages.projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
