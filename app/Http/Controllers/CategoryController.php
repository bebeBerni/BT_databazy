<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::query()
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'categories' => $categories
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validačná chyba.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $category = Category::create([
            'name' => $request->name,
            'color' => $request->color,
        ]);

        return response()->json([
            'message' => 'Kategória bola úspešne vytvorená.',
            'category' => $category
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Kategória nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'category' => $category
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Kategória nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
            'color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validačná chyba.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $category->update([
            'name' => $request->name,
            'color' => $request->color,
        ]);

        return response()->json([
            'message' => 'Kategória bola úspešne aktualizovaná.',
            'category' => $category
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Kategória nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $category->delete();

        return response()->json([
            'message' => 'Kategória bola úspešne odstránená.'
        ], Response::HTTP_OK);
    }
}
