<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller {
    public function index() {
        $posts = Post::with('user')->latest()->paginate(15);
        return view('admin.posts.index', compact('posts'));
    }

    public function create() {
        return view('admin.posts.create');
    }

    public function store(Request $request) {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        $data = [
            'title'        => $request->title,
            'slug'         => Str::slug($request->title).'-'.time(),
            'content'      => $request->content,
            'is_published' => $request->boolean('is_published'),
            'user_id'      => auth()->id(),
        ];
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('posts', 'public');
        }
        Post::create($data);
        return redirect()->route('admin.posts.index')->with('success', 'Thêm bài viết thành công!');
    }

    public function edit(Post $post) {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post) {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        $data = [
            'title'        => $request->title,
            'content'      => $request->content,
            'is_published' => $request->boolean('is_published'),
        ];
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('posts', 'public');
        }
        $post->update($data);
        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Post $post) {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Đã xóa bài viết!');
    }
}