<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display the categories listing page.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $categories = $user->categories()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Categories', [
            'auth'       => ['user' => $user],
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new category.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $user = Auth::user();

        $category = Category::firstOrCreate([
            'user_id' => $user->id,
            'name'    => $request->validated('name'),
        ]);

        return response()->json([
            'id'   => $category->id,
            'name' => $category->name,
        ]);
    }

    /**
     * Update an existing category.
     *
     * @param  UpdateCategoryRequest $request the validated request data
     * @param  Category              $category the category to update
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $category->update(['name' => $request->validated('name')]);

        return response()->json([
            'id'   => $category->id,
            'name' => $category->name,
        ]);
    }

    /**
     * Delete an existing category.
     *
     * @param  Category $category the category to delete
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted.');
    }
}
