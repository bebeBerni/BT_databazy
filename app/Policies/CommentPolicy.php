<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Note;
use App\Models\Task;
use App\Models\User;

class CommentPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
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

    public function viewAnyForTask(User $user, Task $task): bool
    {
        $note = $task->note;

        if (!$note) {
            return false;
        }

        if (in_array($note->status, ['published', 'archived'])) {
            return true;
        }

        return $note->user_id === $user->id;
    }

    public function createForNote(User $user, Note $note): bool
    {
        if (in_array($note->status, ['published', 'archived'])) {
            return true;
        }

        return $note->user_id === $user->id;
    }

    public function createForTask(User $user, Task $task): bool
    {
        $note = $task->note;

        if (!$note) {
            return false;
        }

        if (in_array($note->status, ['published', 'archived'])) {
            return true;
        }

        return $note->user_id === $user->id;
    }

    public function update(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }
}
