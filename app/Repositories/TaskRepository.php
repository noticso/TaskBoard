<?php

namespace App\Repositories;

use App\Enums\Status;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function find(int $id): Task
    {
        return Task::findOrFail($id);
    }

    public function getByProject(int $projectId, array $filters = []): Collection
    {
        return Task::where('project_id', $projectId)
            ->when($filters['status'] ?? null, function (Builder $query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when($filters['priority'] ?? null, function (Builder $query) use ($filters) {
                $query->where('priority', $filters['priority']);
            })
            ->get();
    }

    public function countIncompleteByProject(int $projectId): int
    {
        return Task::where('project_id', $projectId)
            ->where('status', '!=', Status::Completed->value)
            ->count();
    }
}
