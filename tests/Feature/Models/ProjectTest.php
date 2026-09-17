<?php

use App\Models\Project;
use App\Models\User;

use function Pest\Laravel\assertDatabaseCount;

it('deletes its tasks when the project is deleted', function () {
    $project = Project::factory()->hasTasks(3)->for(User::factory())->create();
    $project->delete();
    assertDatabaseCount('tasks', 0);
});


it('deletes its projects when the user is deleted', function () {
    $user = User::factory()->hasProjects(3)->create();
    $user->delete();
    assertDatabaseCount('projects', 0);
});