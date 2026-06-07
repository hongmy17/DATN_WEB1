@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Địa chỉ của tôi</h1>
        <a href="{{ route('addresses.create') }}"
           class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800">
            + Thêm địa chỉ
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @forelse($addresses as $address)
        <div class="border rounded-lg p-4 mb-4 {{ $address->is_default ? 'border-black' : 'border-gray-200' }}">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-semibold">{{ $address->receiver_name }}</span>
                        <span class="text-gray-500">{{ $address->receiver_phone }}</span>
                        @if($address->is_default)
                            <span class="text-xs border border-black text-black px-2 py-0.5 rounded">
                                Mặc định
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-600 text-sm">
                        {{ $address->address_detail }},
                        {{ $address->ward }},
                        {{ $address->district }},
                        {{ $address->province }}
                    </p>
                </div>

                <div class="flex flex-col gap-2 text-sm ml-4">
                    <a href="{{ route('addresses.edit', $address) }}"
                       class="text-blue-600 hover:underline">Sửa</a>

                    @if(!$address->is_default)
                        <form action="{{ route('addresses.setDefault', $address) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="text-gray-600 hover:underline">Đặt mặc định</button>
                        </form>

                        <form action="{{ route('addresses.destroy', $address) }}" method="POST"
                              onsubmit="return confirm('Xóa địa chỉ này?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline">Xóa</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-gray-500 py-12">
            Bạn chưa có địa chỉ nào.
        </div>
    @endforelse
</div>
@endsection
