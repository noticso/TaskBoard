<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'description', 'priority', 'status', 'due_date'])]
class Task extends Model
{
    protected function casts(): array
    {
        return [
            'priority' => Priority::class,
            'status' => Status::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
