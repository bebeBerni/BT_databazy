<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    public function viewAnyForNote(User $user, Note $note): bool
    {
        if (in_array($note->status, ['published', 'archived'])) {
            return true;
        }

        return $note->user_id === $user->id;
    }

    public function createForNote(User $user, Note $note): bool
    {
        return $note->user_id === $user->id;
    }

    public function view(User $user, Task $task): bool
    {
        return $task->note && (
                in_array($task->note->status, ['published', 'archived']) ||
                $task->note->user_id === $user->id
            );
    }

    public function update(User $user, Task $task): bool
    {
        return $task->note && $task->note->user_id === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->note && $task->note->user_id === $user->id;
    }
}
