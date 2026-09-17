<?php

namespace App\Enums;

enum Status: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
