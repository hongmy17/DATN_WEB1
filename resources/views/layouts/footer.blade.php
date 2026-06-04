<footer class="bg-white border-t border-gray-200 mt-12">
    <div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Nexus Store</h3>
                <p class="mt-3 text-sm text-gray-600 leading-relaxed">
                    Cửa hàng trực tuyến Nexus Store cung cấp sản phẩm công nghệ chất lượng và dịch vụ chăm sóc khách hàng tận tâm.
                </p>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Liên kết nhanh</h4>
                <ul class="mt-4 space-y-2 text-sm text-gray-600">
                    <li><a href="{{ route('home') }}" class="hover:text-gray-900">Trang chủ</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-gray-900">Sản phẩm</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-gray-900">Liên hệ</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Hỗ trợ</h4>
                <p class="mt-4 text-sm text-gray-600">
                    Email: support@nexusstore.vn<br>
                    Hotline: 1900-1234
                </p>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-gray-200 text-sm text-gray-500 text-center">
            © {{ date('Y') }} Nexus Store. Bảo lưu mọi quyền.
        </div>
    </div>
</footer>
