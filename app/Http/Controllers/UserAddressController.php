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
            'receiver_phone' => 'required|string|max:10',
            'province'       => 'required|string|max:100',
            'district'       => 'nullable|string|max:100',
            'ward'           => 'required|string|max:100',
            'address_detail' => 'required|string',
        ], [
            'receiver_name.required'  => 'Vui lòng nhập họ tên người nhận.',
            'receiver_name.max'       => 'Họ tên không được vượt quá 100 ký tự.',
            'receiver_phone.required' => 'Vui lòng nhập số điện thoại.',
            'receiver_phone.max'      => 'Số điện thoại không được vượt quá 10 ký tự.',
            'province.required'       => 'Vui lòng chọn Tỉnh/Thành phố.',
            'district.max'            => 'Quận/Huyện không được vượt quá 100 ký tự.',
            'ward.required'           => 'Vui lòng chọn Phường/Xã.',
            'address_detail.required' => 'Vui lòng nhập địa chỉ chi tiết.',
        ]);

        // Quận/Huyện không bắt buộc — nếu để trống thì lưu chuỗi rỗng thay vì null
        // (giữ nguyên cột DB hiện tại, không cần migration)
        $request->merge(['district' => $request->district ?? '']);

        $isFirst = Auth::user()->addresses()->count() === 0;

        $address = Auth::user()->addresses()->create([
            'receiver_name'  => $request->receiver_name,
            'receiver_phone' => $request->receiver_phone,
            'province'       => $request->province,
            'district'       => $request->district,
            'ward'           => $request->ward,
            'address_detail' => $request->address_detail,
            'is_default'     => false,
        ]);

        // Nếu là địa chỉ đầu tiên hoặc user chọn làm mặc định
        if ($isFirst || $request->boolean('is_default')) {
            $address->setAsDefault();
        }

        // Nếu request gọi bằng AJAX (Accept: application/json) → trả JSON, không redirect
        // Trang "dia-chi" submit form thường nên KHÔNG rơi vào nhánh này, không bị ảnh hưởng
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm địa chỉ thành công!',
                'address' => $address,
            ]);
        }

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
            'district'       => 'nullable|string|max:100',
            'ward'           => 'required|string|max:100',
            'address_detail' => 'required|string',
        ], [
            'receiver_name.required'  => 'Vui lòng nhập họ tên người nhận.',
            'receiver_name.max'       => 'Họ tên không được vượt quá 100 ký tự.',
            'receiver_phone.required' => 'Vui lòng nhập số điện thoại.',
            'receiver_phone.max'      => 'Số điện thoại không được vượt quá 15 ký tự.',
            'province.required'       => 'Vui lòng chọn Tỉnh/Thành phố.',
            'district.max'            => 'Quận/Huyện không được vượt quá 100 ký tự.',
            'ward.required'           => 'Vui lòng chọn Phường/Xã.',
            'address_detail.required' => 'Vui lòng nhập địa chỉ chi tiết.',
        ]);

        // Quận/Huyện không bắt buộc — nếu để trống thì lưu chuỗi rỗng thay vì null
        $request->merge(['district' => $request->district ?? '']);

        $address->update($request->only([
            'receiver_name',
            'receiver_phone',
            'province',
            'district',
            'ward',
            'address_detail',
        ]));

        // Nếu user chọn làm mặc định → bỏ mặc định cũ
        if ($request->boolean('is_default')) {
            $address->setAsDefault();
        }

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