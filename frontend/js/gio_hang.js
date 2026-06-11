/**
 * gio_hang.js
 */
const GioHang = {
  KEY: 'lux_cart',

  cartKey(item) {
    const vid = item.variant_id || 0;
    return `${item.id}_${vid}`;
  },

  lay() {
    return JSON.parse(localStorage.getItem(this.KEY) || '[]');
  },

  luu(items) {
    localStorage.setItem(this.KEY, JSON.stringify(items));
    document.dispatchEvent(new CustomEvent('cartUpdated'));
  },

  them(sanPham, soLuong = 1) {
    const items = this.lay();
    const key = this.cartKey(sanPham);
    const index = items.findIndex(i => this.cartKey(i) === key);
    if (index >= 0) {
      items[index].so_luong += soLuong;
    } else {
      items.push({
        id: sanPham.id,
        variant_id: sanPham.variant_id || null,
        mau_sac: sanPham.mau_sac || '',
        ten: sanPham.ten,
        gia: sanPham.gia || 0,
        hinh_anh: sanPham.hinh_anh || '',
        so_luong: soLuong,
      });
    }
    this.luu(items);
  },

  themTuCard(card, data) {
    const gia = parseInt(card.dataset.gia, 10) || data.gia || 0;
    this.them({ ...data, gia }, 1);
  },

  xoa(id, variantId = null) {
    const key = `${id}_${variantId || 0}`;
    this.luu(this.lay().filter(i => this.cartKey(i) !== key));
  },

  capNhatSoLuong(id, variantId, soLuong) {
    const items = this.lay();
    const key = `${id}_${variantId || 0}`;
    const item = items.find(i => this.cartKey(i) === key);
    if (item) {
      item.so_luong = Math.max(1, soLuong);
      this.luu(items);
    }
  },

  dem() {
    return this.lay().reduce((t, i) => t + i.so_luong, 0);
  },

  tongTien() {
    return this.lay().reduce((t, i) => t + (i.gia || 0) * i.so_luong, 0);
  },
};
/**
 * popup_close.js
 * Chức năng: đóng thông báo "Cảm ơn bạn!" bằng nút ×
 * Tương thích với mọi cách gọi hiển thị popup (kể cả hiển thị động)
 */
(function() {
  function initCloseButton() {
    const modal = document.getElementById('order-success');
    if (!modal) return;

    const closeBtn = modal.querySelector('.close-success-btn');
    if (!closeBtn) return;

    // Gắn sự kiện click (tránh trùng lặp)
    closeBtn.removeEventListener('click', closeHandler);
    closeBtn.addEventListener('click', closeHandler);

    function closeHandler(e) {
      e.preventDefault();
      modal.hidden = true;
    }
  }

  // Khởi tạo lần đầu khi DOM sẵn sàng
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCloseButton);
  } else {
    initCloseButton();
  }

  // Dùng MutationObserver để đảm bảo nút đóng vẫn hoạt động ngay cả khi
  // popup được tạo lại hoặc hiển thị sau khi đã có sự thay đổi DOM.
  const observer = new MutationObserver(function(mutations) {
    const modal = document.getElementById('order-success');
    if (modal && !modal.hidden) {
      const btn = modal.querySelector('.close-success-btn');
      if (btn && !btn.hasAttribute('data-popup-listener')) {
        btn.setAttribute('data-popup-listener', 'true');
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          modal.hidden = true;
        });
      }
    }
  });

  const targetNode = document.getElementById('order-success');
  if (targetNode) {
    observer.observe(targetNode, { attributes: true, attributeFilter: ['hidden'] });
  }
})();