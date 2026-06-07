<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{
    // Danh sách địa chỉ
    public function index()
    {
        $addresses = Auth::user()->addresses()->latest()->get();
        return view('pages.addresses.index', compact('addresses'));
    }

    // Form thêm địa chỉ
    public function create()
    {
        return view('pages.addresses.create');
    }

    // Lưu địa chỉ mới
    public function store(Request $request)
    {
        $request->validate([
            'receiver_name'  => 'required|string|max:100',
            'receiver_phone' => 'required|string|max:15',
            'province'       => 'required|string|max:100',
            'district'       => 'required|string|max:100',
            'ward'           => 'required|string|max:100',
            'address_detail' => 'required|string',
        ]);

        $isFirst = Auth::user()->addresses()->count() === 0;

        Auth::user()->addresses()->create([
            'receiver_name'  => $request->receiver_name,
            'receiver_phone' => $request->receiver_phone,
            'province'       => $request->province,
            'district'       => $request->district,
            'ward'           => $request->ward,
            'address_detail' => $request->address_detail,
            'is_default'     => $isFirst ? true : $request->boolean('is_default'),
        ]);

        $redirectTo = request()->input('redirect_to', route('addresses.index'));
return redirect($redirectTo)->with('success', 'Thêm địa chỉ thành công!');
    }

    // Form sửa địa chỉ
    public function edit(UserAddress $address)
    {
        $this->authorize($address);
        return view('pages.addresses.edit', compact('address'));
    }

    // Cập nhật địa chỉ
    public function update(Request $request, UserAddress $address)
    {
        $this->authorize($address);

        $request->validate([
            'receiver_name'  => 'required|string|max:100',
            'receiver_phone' => 'required|string|max:15',
            'province'       => 'required|string|max:100',
            'district'       => 'required|string|max:100',
            'ward'           => 'required|string|max:100',
            'address_detail' => 'required|string',
        ]);

        $address->update($request->only([
            'receiver_name', 'receiver_phone',
            'province', 'district', 'ward', 'address_detail',
        ]));

        $redirectTo = request()->input('redirect_to', route('addresses.index'));
return redirect($redirectTo)->with('success', 'Cập nhật địa chỉ thành công!');
    }

    // Xóa địa chỉ
    public function destroy(UserAddress $address)
    {
        $this->authorize($address);

        $count = Auth::user()->addresses()->count();
        if ($count <= 1) {
            return back()->with('error', 'Không thể xóa địa chỉ duy nhất!');
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            Auth::user()->addresses()->first()?->setAsDefault();
        }

        return back()->with('success', 'Xóa địa chỉ thành công!');
    }

    // Đặt làm mặc định
    public function setDefault(UserAddress $address)
    {
        $this->authorize($address);
        $address->setAsDefault();
        return back()->with('success', 'Đã đặt làm địa chỉ mặc định!');
    }

    // Kiểm tra địa chỉ thuộc về user đang đăng nhập
    private function authorize(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
    }
}