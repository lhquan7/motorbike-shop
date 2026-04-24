<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Auth};

class ProfileController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        $user   = auth()->user();
        $orders = $user->orders()->latest()->paginate(10);
        return view('profile.index', compact('user', 'orders'));
    }

    public function update(Request $request) {
        $user = auth()->user();
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'email'   => 'required|email|unique:users,email,'.$user->id,
        ]);
        $user->update($request->only('name','email','phone','address'));
        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function changePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.'])->withInput();
        }
        auth()->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    public function orderDetail($orderCode) {
        $order = auth()->user()->orders()
            ->where('order_code', $orderCode)
            ->with('items.product')
            ->firstOrFail();
        return view('profile.order-detail', compact('order'));
    }
}