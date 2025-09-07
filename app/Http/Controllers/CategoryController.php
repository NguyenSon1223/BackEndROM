<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Lấy danh sách tất cả category
     */
    public function index()
    {
        $cate = Category::all();
        return response()->json($cate, 200);
    }

    /**
     * Tạo mới một category
     */

    public function store(Request $request)
    {

        $category = Category::create([
            'name' => $request->name
        ]);

        return response()->json($category);
    }

    /**
     * Lấy chi tiết một category
     */
    public function show(int $id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category, 200);
    }

    /**
     * Cập nhật một category
     */
    public function update(Request $request, int $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);

        return response()->json($category, 200);
    }

    /**
     * Xoá một category
     */
    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
