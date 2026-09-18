<?php
namespace App\Repositories;
use App\Models\Project;
use App\Interfaces\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository implements ProjectRepositoryInterface{
    public function create(array $data): Project{
        return Project::create($data);
    }
    public function update(Project $project, array $data): Project{
        $project->update($data);
        return $project;
    }
    public function delete(Project $project): bool{
        return $project->delete();
    }
    public function find(int $id): Project{
        return Project::findOrFail($id);
    }
    public function getByUser(int $userId): Collection{
        return Project::where("user_id", $userId)->get();
    }
}