<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $this->authorize('viewAny', Note::class);

        $notes = Note::query()
            ->select(['id', 'user_id', 'title', 'body', 'status', 'is_pinned', 'created_at'])
            ->with([
                'user:id,first_name,last_name',
                'categories:id,name,color',
            ])
            ->whereIn('status', ['published', 'archived'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(5);

        return response()->json([
            'notes' => $notes,
        ], Response::HTTP_OK);
    }

    public function myNotes(Request $request)
    {
        $this->authorize('viewAny', Note::class);

        $notes = $request->user()
            ->notes()
            ->select(['id', 'user_id', 'title', 'body', 'status', 'is_pinned', 'created_at'])
            ->with([
                'categories:id,name,color',
            ])
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(5);

        return response()->json([
            'notes' => $notes,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Note::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'body'  => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'is_pinned' => ['sometimes', 'boolean'],
            'categories' => ['sometimes', 'array', 'max:3'],
            'categories.*' => ['integer', 'distinct', 'exists:categories,id'],
        ]);

        $note = $request->user()->notes()->create([
            'title'     => $validated['title'],
            'body'      => $validated['body'] ?? null,
            'status'    => $validated['status'] ?? 'draft',
            'is_pinned' => $validated['is_pinned'] ?? false,
        ]);

        if (!empty($validated['categories'])) {
            $note->categories()->sync($validated['categories']);
        }

        return response()->json([
            'message' => 'Poznámka bola úspešne vytvorená.',
            'note' => $note->load([
                'user:id,first_name,last_name',
                'categories:id,name,color',
            ]),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $note = Note::query()
            ->with([
                'user:id,first_name,last_name,email',
                'categories:id,name,color',
                'tasks',
                'comments',
                'tasks.comments',
            ])
            ->find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('view', $note);

        return response()->json([
            'note' => $note
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json(
                ['message' => 'Poznámka nenájdená.'],
                Response::HTTP_NOT_FOUND
            );
        }

        $this->authorize('update', $note);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body'  => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'is_pinned' => ['sometimes', 'boolean'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['integer', 'distinct', 'exists:categories,id'],
        ]);

        $note->update([
            'title' => $validated['title'],
            'body' => $validated['body'] ?? null,
            'status' => $validated['status'] ?? $note->status,
            'is_pinned' => $validated['is_pinned'] ?? $note->is_pinned,
        ]);

        if (array_key_exists('categories', $validated)) {
            $note->categories()->sync($validated['categories']);
        }

        return response()->json([
            'message' => 'Poznámka bola aktualizovaná.',
            'note' => $note->load([
                'user:id,first_name,last_name',
                'categories:id,name,color',
            ]),
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('delete', $note);

        $note->delete();

        return response()->json([
            'message' => 'Poznámka bola úspešne odstránená.'
        ], Response::HTTP_OK);
    }

    public function statsByStatus()
    {
        $this->authorize('viewAny', Note::class);

        $stats = DB::table('notes')
            ->whereNull('deleted_at')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        return response()->json(['stats' => $stats], Response::HTTP_OK);
    }

    public function archiveOldDrafts()
    {
        $this->authorize('archiveOldDrafts', Note::class);

        $affected = DB::table('notes')
            ->whereNull('deleted_at')
            ->where('status', 'draft')
            ->where('updated_at', '<', now()->subDays(30))
            ->update([
                'status' => 'archived',
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Staré koncepty boli archivované.',
            'affected_rows' => $affected,
        ], Response::HTTP_OK);
    }

    public function userNotesWithCategories(string $userId)
    {
        $this->authorize('viewUserNotesWithCategories', Note::class);

        $rows = DB::table('notes')
            ->join('note_category', 'notes.id', '=', 'note_category.note_id')
            ->join('categories', 'note_category.category_id', '=', 'categories.id')
            ->where('notes.user_id', $userId)
            ->whereNull('notes.deleted_at')
            ->orderBy('notes.updated_at', 'desc')
            ->select('notes.id', 'notes.title', 'categories.name as category')
            ->get();

        return response()->json(['notes' => $rows], Response::HTTP_OK);
    }

    public function pin(string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $note);

        $note->pin();

        return response()->json([
            'message' => 'Poznámka bola pripnutá.',
            'note' => $note
        ], Response::HTTP_OK);
    }

    public function unpin(string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $note);

        $note->unpin();

        return response()->json([
            'message' => 'Poznámka bola odopnutá.',
            'note' => $note
        ], Response::HTTP_OK);
    }

    public function publish(string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $note);

        $note->publish();

        return response()->json([
            'message' => 'Poznámka bola publikovaná.',
            'note' => $note
        ], Response::HTTP_OK);
    }

    public function archive(string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $note);

        $note->archive();

        return response()->json([
            'message' => 'Poznámka bola archivovaná.',
            'note' => $note
        ], Response::HTTP_OK);
    }

    public function draft(string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $note);

        $note->draft();

        return response()->json([
            'message' => 'Poznámka bola nastavená ako draft.',
            'note' => $note
        ], Response::HTTP_OK);
    }
}
