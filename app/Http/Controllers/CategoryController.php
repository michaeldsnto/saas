<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('inventory.categories', ['categories' => Category::query()->latest()->get()]);
    }

    public function store(CategoryRequest $request, AuditLogService $auditLogService): RedirectResponse
    {
        $category = Category::create($request->validated());
        $auditLogService->record('category.created', $category, $request->validated(), $request);
        return back()->with('status', 'Category created.');
    }

    public function update(CategoryRequest $request, Category $category, AuditLogService $auditLogService): RedirectResponse
    {
        $category->update($request->validated());
        $auditLogService->record('category.updated', $category, $request->validated(), $request);
        return back()->with('status', 'Category updated.');
    }

    public function destroy(Category $category, AuditLogService $auditLogService): RedirectResponse
    {
        $auditLogService->record('category.deleted', $category, [], request());
        $category->delete();
        return back()->with('status', 'Category deleted.');
    }
}
