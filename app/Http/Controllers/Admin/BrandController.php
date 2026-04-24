<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller {
    public function index() {
        $brands = Brand::withCount('products')->latest()->get();
        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request) {
        $request->validate([
            'name'  => 'required|string|max:100|unique:brands,name',
            'logo'  => 'nullable|image|max:1024',
        ]);
        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name).'-'.time(),
        ];
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }
        Brand::create($data);
        return back()->with('success', 'Thêm hãng xe thành công!');
    }

    public function update(Request $request, Brand $brand) {
        $request->validate(['name' => 'required|string|max:100|unique:brands,name,'.$brand->id]);
        $data = ['name' => $request->name];
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }
        $brand->update($data);
        return back()->with('success', 'Cập nhật hãng xe thành công!');
    }

    public function destroy(Brand $brand) {
        if ($brand->products()->count() > 0) {
            return back()->with('error', 'Không thể xóa! Hãng đang có '.$brand->products()->count().' xe.');
        }
        $brand->delete();
        return back()->with('success', 'Đã xóa hãng xe!');
    }
}