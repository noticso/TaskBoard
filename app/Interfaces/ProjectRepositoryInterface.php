<?php

namespace App\Interfaces;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

interface ProjectRepositoryInterface
{
    public function create(array $data): Project;
    public function update(Project $project, array $data): Project;
    public function delete(Project $project): bool;
    public function find(int $id): Project;
    public function getByUser(int $userId): Collection;
}
