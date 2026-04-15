<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Note;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    /**
     * GET /notes/{note}/comments
     */
    public function indexForNote(Note $note)
    {
        $this->authorize('viewAnyForNote', [Comment::class, $note]);

        $comments = $note->comments()
            ->with('user:id,first_name,last_name')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'comments' => $comments,
        ], Response::HTTP_OK);
    }

    /**
     * GET /notes/{note}/tasks/{task}/comments
     */
    public function indexForTask(Note $note, Task $task)
    {
        if ($task->note_id !== $note->id) {
            return response()->json([
                'message' => 'Úloha nenájdená.',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('viewAnyForTask', [Comment::class, $task]);

        $comments = $task->comments()
            ->with('user:id,first_name,last_name')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'comments' => $comments,
        ], Response::HTTP_OK);
    }

    /**
     * POST /notes/{note}/comments
     */
    public function storeForNote(Request $request, Note $note)
    {
        $this->authorize('createForNote', [Comment::class, $note]);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $comment = $note->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return response()->json([
            'message' => 'Komentár bol úspešne vytvorený.',
            'comment' => $comment->load('user:id,first_name,last_name'),
        ], Response::HTTP_CREATED);
    }

    /**
     * POST /notes/{note}/tasks/{task}/comments
     */
    public function storeForTask(Request $request, Note $note, Task $task)
    {
        if ($task->note_id !== $note->id) {
            return response()->json([
                'message' => 'Úloha nenájdená.',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('createForTask', [Comment::class, $task]);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return response()->json([
            'message' => 'Komentár bol úspešne vytvorený.',
            'comment' => $comment->load('user:id,first_name,last_name'),
        ], Response::HTTP_CREATED);
    }

    /**
     * PATCH /comments/{comment}
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $comment->update([
            'body' => $validated['body'],
        ]);

        return response()->json([
            'message' => 'Komentár bol úspešne aktualizovaný.',
            'comment' => $comment->load('user:id,first_name,last_name'),
        ], Response::HTTP_OK);
    }

    /**
     * DELETE /comments/{comment}
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'message' => 'Komentár bol úspešne odstránený.',
        ], Response::HTTP_OK);
    }
}
