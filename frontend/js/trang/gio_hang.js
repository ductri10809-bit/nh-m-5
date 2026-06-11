/**
 * gio_hang.js (trang)
 */
document.addEventListener('DOMContentLoaded', () => {
  const listEl = document.getElementById('cart-list');
  const emptyEl = document.getElementById('cart-empty');
  const subtotalEl = document.getElementById('cart-subtotal');
  const totalEl = document.getElementById('cart-total');
  const form = document.getElementById('form-dat-hang');

  async function loadUserDataIfLoggedIn() {
    try {
      const userResult = await Chung.goiApi('nguoi_dung.php');
      if (userResult.success && userResult.data) {
        const user = userResult.data;
        if (form) {
          const hoTenInput = form.querySelector('[name="ho_ten"]');
          const sdtInput = form.querySelector('[name="sdt"]');
          const emailInput = form.querySelector('[name="email"]');
          const diaChiInput = form.querySelector('[name="dia_chi"]');

          if (hoTenInput && user.ho_ten) hoTenInput.value = user.ho_ten;
          if (sdtInput && user.sdt) sdtInput.value = user.sdt;
          if (emailInput && user.email) emailInput.value = user.email;
          if (diaChiInput && user.dia_chi) diaChiInput.value = user.dia_chi;
        }
      }
    } catch (err) {
      // User not logged in, which is fine - guest checkout is allowed
    }
  }

  function render() {
    const items = GioHang.lay();
    const total = GioHang.tongTien();

    if (items.length === 0) {
      listEl.innerHTML = '';
      emptyEl.hidden = false;
      if (subtotalEl) subtotalEl.textContent = Chung.formatGia(0);
      if (totalEl) totalEl.textContent = Chung.formatGia(0);
      form?.querySelector('#btn-dat-hang')?.setAttribute('disabled', 'disabled');
      return;
    }

    emptyEl.hidden = true;
    form?.querySelector('#btn-dat-hang')?.removeAttribute('disabled');

    listEl.innerHTML = items.map(item => `
      <article class="cart-item" data-id="${item.id}" data-variant="${item.variant_id || ''}">
        <img class="cart-item__img" src="${item.hinh_anh || ''}" alt="">
        <div>
          <h3 class="cart-item__name">${item.ten}</h3>
          ${item.mau_sac ? `<p class="cart-item__variant">Màu: ${item.mau_sac}</p>` : ''}
          <p class="cart-item__price">${Chung.formatGia(item.gia)}</p>
          <div class="cart-item__qty">
            <button type="button" class="btn-qty-minus" aria-label="Giảm">−</button>
            <span>${item.so_luong}</span>
            <button type="button" class="btn-qty-plus" aria-label="Tăng">+</button>
          </div>
        </div>
        <button type="button" class="cart-item__remove btn-xoa">Xóa</button>
      </article>
    `).join('');

    if (subtotalEl) subtotalEl.textContent = Chung.formatGia(total);
    if (totalEl) totalEl.textContent = Chung.formatGia(total);
  }

  render();
  loadUserDataIfLoggedIn();
  document.addEventListener('cartUpdated', render);

  listEl?.addEventListener('click', async (e) => {
    const row = e.target.closest('.cart-item');
    if (!row) return;
    const id = parseInt(row.dataset.id, 10);
    const variantId = row.dataset.variant ? parseInt(row.dataset.variant, 10) : null;
    // If guest, prompt before allowing cart operations
    await Chung.ensureUserChecked();
    if (!Chung.currentUser) {
      const ok = await Chung.showLoginPrompt('Bạn chưa đăng nhập. Đăng nhập để lưu giỏ hàng hoặc tiếp tục dưới dạng khách.');
      if (!ok) return;
    }

    if (e.target.classList.contains('btn-xoa')) {
      GioHang.xoa(id, variantId);
      render();
      return;
    }
    const item = GioHang.lay().find(i => GioHang.cartKey(i) === `${id}_${variantId || 0}`);
    if (!item) return;
    if (e.target.classList.contains('btn-qty-plus')) {
      GioHang.capNhatSoLuong(id, variantId, item.so_luong + 1);
      render();
    }
    if (e.target.classList.contains('btn-qty-minus') && item.so_luong > 1) {
      GioHang.capNhatSoLuong(id, variantId, item.so_luong - 1);
      render();
    }
  });

  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    // Check user login status first; if guest, show prompt
    await Chung.ensureUserChecked();
    if (!Chung.currentUser) {
      const ok = await Chung.showLoginPrompt(
        'Bạn đang chưa đăng nhập. Đăng nhập để lưu đơn, theo dõi trạng thái và nhận ưu đãi.',
        window.location.href
      );
      if (!ok) return; // if user didn't choose to continue as guest, stop
      // else continue as guest
    }
    const items = GioHang.lay();
    if (!items.length) return;

    const payload = {
      ho_ten: form.ho_ten.value.trim(),
      sdt: form.sdt.value.trim(),
      email: form.email.value.trim(),
      dia_chi: form.dia_chi.value.trim() + (form.ghi_chu?.value ? `\nGhi chú: ${form.ghi_chu.value}` : ''),
      items: items.map(i => ({
        product_id: i.id,
        id: i.id,
        gia: i.gia,
        so_luong: i.so_luong,
      })),
    };

    try {
      const btn = document.getElementById('btn-dat-hang');
      btn.disabled = true;
      btn.textContent = 'Đang xử lý...';
      const result = await Chung.goiApi('dat_hang.php', {
        method: 'POST',
        body: JSON.stringify(payload),
      });
      if (result.success) {
        GioHang.luu([]);
        render();
        
        const orderId = result.data?.order_id;
        const customerEmail = form.email.value.trim();
        const customerName = form.ho_ten.value.trim();
        const paymentMethod = form.querySelector('input[name="payment_method"]:checked')?.value || 'cod';
        const amount = items.reduce((sum, i) => sum + (i.gia * i.so_luong), 0);
        
        // If bank transfer selected, redirect to QR page
        if (paymentMethod === 'bank_transfer') {
          localStorage.setItem('order_id', orderId);
          localStorage.setItem('customer_email', customerEmail);
          localStorage.setItem('customer_name', customerName);
          localStorage.setItem('amount', amount);
          window.location.href = `../qr/qr.html?order_id=${orderId}&customer_email=${encodeURIComponent(customerEmail)}&customer_name=${encodeURIComponent(customerName)}&amount=${amount}`;
          return;
        }
        
        const modal = document.getElementById('order-success');
        const msg = document.getElementById('order-success-msg');
        if (msg) {
          msg.textContent = `Đơn hàng #${orderId || ''} đã được ghi nhận. Chúng tôi sẽ liên hệ sớm.`;
        }
        modal.hidden = false;
        form.reset();
      } else {
        Chung.toast(result.message || 'Đặt hàng thất bại');
      }
      btn.disabled = false;
      btn.textContent = 'Đặt hàng';
    } catch (err) {
      Chung.toast('Không thể đặt hàng. Vui lòng thử lại.');
      console.error(err);
    }
  });
});
