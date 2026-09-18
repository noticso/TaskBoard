<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Project;
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


}