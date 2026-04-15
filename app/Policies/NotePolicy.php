<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;

class NotePolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Note $note): bool
    {
        if (in_array($note->status, ['published', 'archived'])) {
            return true;
        }

        return $note->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Note $note): bool
    {
        return $note->user_id === $user->id;
    }

    public function delete(User $user, Note $note): bool
    {
        return $note->user_id === $user->id;
    }

    public function restore(User $user, Note $note): bool
    {
        return $note->user_id === $user->id;
    }

    public function forceDelete(User $user, Note $note): bool
    {
        return false;
    }

    public function archiveOldDrafts(User $user): bool
    {
        return false;
    }

    public function viewUserNotesWithCategories(User $user): bool
    {
        return false;
    }
}
