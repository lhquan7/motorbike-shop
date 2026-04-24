<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller {
    public function index() {
        $categories = Category::withCount('products')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:100|unique:categories,name']);
        Category::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name).'-'.time(),
            'description' => $request->description,
        ]);
        return back()->with('success', 'Thêm danh mục thành công!');
    }

    public function update(Request $request, Category $category) {
        $request->validate(['name' => 'required|string|max:100|unique:categories,name,'.$category->id]);
        $category->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);
        return back()->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(Category $category) {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Không thể xóa! Danh mục đang có '.$category->products()->count().' xe.');
        }
        $category->delete();
        return back()->with('success', 'Đã xóa danh mục!');
    }
}