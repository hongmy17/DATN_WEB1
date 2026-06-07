@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-2xl font-semibold mb-6">Thêm địa chỉ mới</h1>

    <form action="{{ route('addresses.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Họ tên người nhận</label>
            <input type="text" name="receiver_name"
                   value="{{ old('receiver_name') }}"
                   class="w-full border rounded px-3 py-2 @error('receiver_name') border-red-500 @enderror">
            @error('receiver_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Số điện thoại</label>
            <input type="text" name="receiver_phone"
                   value="{{ old('receiver_phone') }}"
                   class="w-full border rounded px-3 py-2 @error('receiver_phone') border-red-500 @enderror">
            @error('receiver_phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Tỉnh / Thành phố</label>
            <input type="text" name="province"
                   value="{{ old('province') }}"
                   class="w-full border rounded px-3 py-2 @error('province') border-red-500 @enderror">
            @error('province')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Quận / Huyện</label>
            <input type="text" name="district"
                   value="{{ old('district') }}"
                   class="w-full border rounded px-3 py-2 @error('district') border-red-500 @enderror">
            @error('district')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Phường / Xã</label>
            <input type="text" name="ward"
                   value="{{ old('ward') }}"
                   class="w-full border rounded px-3 py-2 @error('ward') border-red-500 @enderror">
            @error('ward')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Địa chỉ chi tiết</label>
            <textarea name="address_detail" rows="2"
                      class="w-full border rounded px-3 py-2 @error('address_detail') border-red-500 @enderror">{{ old('address_detail') }}</textarea>
            @error('address_detail')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_default" value="1"
                       {{ old('is_default') ? 'checked' : '' }}>
                <span class="text-sm">Đặt làm địa chỉ mặc định</span>
            </label>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-black text-white px-6 py-2 rounded hover:bg-gray-800">
                Lưu địa chỉ
            </button>
            <a href="{{ route('addresses.index') }}"
               class="border px-6 py-2 rounded hover:bg-gray-50">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
