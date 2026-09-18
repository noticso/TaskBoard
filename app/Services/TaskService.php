<?php

namespace App\Services;

use App\Enums\Status;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    public function create(int $projectId, array $data): Task
    {
        $incompleteTasks = $this->taskRepository
            ->countIncompleteByProject($projectId);

        if ($incompleteTasks >= 20) {
            throw new \InvalidArgumentException(
                'Il progetto non può avere più di 20 task non completate.'
            );
        }

        $data['project_id'] = $projectId;

        return $this->taskRepository->create($data);
    }

    public function update(Task $task, array $data): Task
    {
        if (
            $task->status === Status::Completed &&
            isset($data['status']) &&
            $data['status'] === Status::InProgress->value
        ) {
            throw new \InvalidArgumentException(
                'Una task completata non può tornare in progress.'
            );
        }

        return $this->taskRepository->update($task, $data);
    }

    public function delete(Task $task): bool
    {
        return $this->taskRepository->delete($task);
    }

    public function find(int $id): Task
    {
        return $this->taskRepository->find($id);
    }

    public function getByProject(int $projectId): Collection
    {
        return $this->taskRepository->getByProject($projectId);
    }
}