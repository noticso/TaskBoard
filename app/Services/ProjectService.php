<?php

namespace App\Services;
use App\Interfaces\ProjectRepositoryInterface;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
class ProjectService{
    public function __construct(private ProjectRepositoryInterface $projectRepository){

    }
    public function create(array $data, User $user): Project{
        $data['user_id'] = $user->id;
        return $this->projectRepository->create($data);
    }
    public function update(Project $project, array $data): Project{
        return $this->projectRepository->update($project, $data);
    }
    public function delete(Project $project): bool{
        return $this->projectRepository->delete($project);
    }
    public function find(int $id): Project{
        return $this->projectRepository->find($id);
    }
    public function getByUser(int $userId): Collection{
        return $this->projectRepository->getByUser($userId);
    }
}