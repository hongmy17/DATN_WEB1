<script>
(function () {
  const CSRF = '{{ csrf_token() }}';

  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.js-retry-payment');
    if (!btn) return;

    const orderId = btn.dataset.orderId;
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Đang chuyển hướng...';

    try {
      const res = await fetch('{{ route("vnpay.create") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF,
        },
        body: JSON.stringify({ order_id: orderId }),
      });
      const data = await res.json();

      if (data.success && data.payment_url) {
        window.location.href = data.payment_url;
        return;
      }

      if (window.Toast) {
        Toast.show(data.message || 'Không thể tạo phiên thanh toán, vui lòng tải lại trang.', 'error');
      } else {
        alert(data.message || 'Không thể tạo phiên thanh toán, vui lòng tải lại trang.');
      }
    } catch (err) {
      if (window.Toast) {
        Toast.show('Có lỗi xảy ra, vui lòng thử lại.', 'error');
      } else {
        alert('Có lỗi xảy ra, vui lòng thử lại.');
      }
    } finally {
      btn.disabled = false;
      btn.textContent = originalText;
    }
  });
})();
</script>