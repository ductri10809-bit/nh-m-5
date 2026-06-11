/**
 * the_san_pham.js - Luxury product card
 */
function renderTheSanPham(sanPham) {
  const variants = sanPham.bien_the || [];
  const swatches = variants.slice(0, 5).map(v =>
    `<span class="swatch" style="background:${v.ma_mau}" title="${v.mau_sac}"></span>`
  ).join('');
  const img = sanPham.hinh_anh || '';
  const detailUrl = `../chi_tiet_san_pham/chi_tiet_san_pham.html?id=${sanPham.id}`;
  const inWishlist = typeof YeuThich !== 'undefined' && YeuThich.co(sanPham.id);

  // Build badges for sale and trend
  let badges = '';
  let priceHtml = '';
  
  if (sanPham.is_sale && sanPham.sale_price) {
    const originalPrice = parseFloat(sanPham.gia);
    const salePrice = parseFloat(sanPham.sale_price);
    const discount = Math.round(((originalPrice - salePrice) / originalPrice) * 100);
    badges += `<span class="sale-badge">🔥 -${discount}%</span>`;
    priceHtml = `
      <div class="product-card__price-container">
        <span class="product-card__price-original">${Chung.formatGia(sanPham.gia)}</span>
        <span class="product-card__price">${Chung.formatGia(sanPham.sale_price)}</span>
      </div>
    `;
  } else {
    priceHtml = `<p class="product-card__price">${Chung.formatGia(sanPham.gia)}</p>`;
  }
  
  if (sanPham.is_trend) {
    badges += '<span class="trend-badge">✨ TREND</span>';
  }

  return `
    <article class="product-card" data-id="${sanPham.id}">
      <div class="product-card__media">
        ${badges ? `<div class="product-card__badges">${badges}</div>` : ''}
        <a href="${detailUrl}">
          <img class="product-card__image" src="${img}" alt="${sanPham.ten}" loading="lazy">
        </a>
        <div class="product-card__actions">
          <button type="button" class="product-card__btn btn-wishlist ${inWishlist ? 'is-active' : ''}" data-id="${sanPham.id}" title="Yêu thích" aria-label="Yêu thích">
            <svg viewBox="0 0 24 24"><path d="M12 21s-7-4.5-9.5-9A5.5 5.5 0 0 1 12 6a5.5 5.5 0 0 1 9.5 6c-2.5 4.5-9.5 9-9.5 9z"/></svg>
          </button>
          <button type="button" class="product-card__btn btn-cart" data-id="${sanPham.id}" title="Giỏ hàng" aria-label="Thêm giỏ hàng">
            <svg viewBox="0 0 24 24"><path d="M6 6h15l-1.5 9H8L6 6zm0 0L5 3H2"/></svg>
          </button>
          <a href="${detailUrl}" class="product-card__btn" title="Chi tiết" aria-label="Xem chi tiết">
            <svg viewBox="0 0 24 24"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
          </a>
        </div>
      </div>
      <div class="product-card__body">
        ${sanPham.ten_danh_muc ? `<p class="product-card__category">${sanPham.ten_danh_muc}</p>` : ''}
        <h3 class="product-card__name"><a href="${detailUrl}">${sanPham.ten}</a></h3>
        ${priceHtml}
        ${swatches ? `<div class="product-card__swatches">${swatches}</div>` : ''}
      </div>
    </article>
  `;
}

function layDuLieuThe(card) {
  const id = parseInt(card.dataset.id, 10);
  const img = card.querySelector('.product-card__image');
  return {
    id,
    ten: card.querySelector('.product-card__name')?.textContent?.trim() || '',
    gia: parseInt(card.dataset.gia || '0', 10),
    hinh_anh: img?.src || '',
    variant_id: null,
    mau_sac: '',
  };
}

document.addEventListener('click', (e) => {
  const card = e.target.closest('.product-card');
  if (!card) return;

  if (e.target.closest('.btn-cart')) {
    e.preventDefault();
    const data = layDuLieuThe(card);
    if (card.__sp) Object.assign(data, { gia: card.__sp.gia, hinh_anh: card.__sp.hinh_anh });
    GioHang.themTuCard(card, data);
    Chung.toast('Đã thêm vào giỏ hàng');
    return;
  }

  if (e.target.closest('.btn-wishlist')) {
    e.preventDefault();
    const data = layDuLieuThe(card);
    if (YeuThich.co(data.id)) {
      YeuThich.xoa(data.id);
      e.target.closest('.btn-wishlist')?.classList.remove('is-active');
      Chung.toast('Đã xóa khỏi yêu thích');
    } else {
      YeuThich.them(data);
      e.target.closest('.btn-wishlist')?.classList.add('is-active');
      Chung.toast('Đã thêm yêu thích');
    }
  }
});
