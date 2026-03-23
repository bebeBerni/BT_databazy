<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Note $note)
    {
        $tasks = $note->tasks()
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'tasks' => $tasks,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Note $note)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_done' => ['sometimes', 'boolean'],
            'due_at' => ['nullable', 'date'],
        ]);

        $task = $note->tasks()->create([
            'title' => $validated['title'],
            'is_done' => $validated['is_done'] ?? false,
            'due_at' => $validated['due_at'] ?? null,
        ]);

        return response()->json([
            'message' => 'Úloha bola úspešne vytvorená.',
            'task' => $task,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $noteId, int $taskId)
    {
        $note = Note::find($noteId);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $task = $note->tasks()->find($taskId);

        if (!$task) {
            return response()->json([
                'message' => 'Úloha nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'task' => $task
        ], Response::HTTP_OK);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note, Task $task)
    {
        if ($task->note_id !== $note->id) {
            return response()->json([
                'message' => 'Úloha nenájdená.',
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_done' => ['sometimes', 'boolean'],
            'due_at' => ['nullable', 'date'],
        ]);

        $task->update($validated);

        return response()->json([
            'message' => 'Úloha bola úspešne aktualizovaná.',
            'task' => $task,
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note, Task $task)
    {
        if ($task->note_id !== $note->id) {
            return response()->json([
                'message' => 'Úloha nenájdená.',
            ], Response::HTTP_NOT_FOUND);
        }

        $task->delete();

        return response()->json([
            'message' => 'Úloha bola úspešne odstránená.',
        ], Response::HTTP_OK);
    }
}
