/**
 * chi_tiet_san_pham.js - Céline-style PDP
 */
document.addEventListener('DOMContentLoaded', async () => {
  const id = Chung.layQueryParam('id');
  if (!id) return;

  let sp = null;
  let variantIndex = 0;

  try {
    const result = await Chung.goiApi(`san_pham.php?id=${id}`);
    if (!result.success || !result.data) return;
    sp = result.data;
    render(sp);
  } catch (error) {
    console.error(error);
  }

  function render(product) {
    document.title = `${product.ten} — LUXURIOUS STORE`;
    document.getElementById('ten-san-pham').textContent = product.ten;
    document.getElementById('gia-san-pham').textContent = Chung.formatGia(product.gia);
    document.getElementById('mo-ta-san-pham').textContent = product.mo_ta || '';
    document.getElementById('ten-danh-muc').textContent = product.ten_danh_muc || '';

    const variants = product.bien_the?.length ? product.bien_the : [{
      id: 0, mau_sac: 'Mặc định', ma_mau: '#1a1a1a', hinh_anh: product.hinh_anh,
    }];

    const colorWrap = document.getElementById('color-options');
    const thumbs = document.getElementById('gallery-thumbs');
    colorWrap.innerHTML = '';
    thumbs.innerHTML = '';

    variants.forEach((v, i) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'color-option' + (i === 0 ? ' is-active' : '');
      btn.innerHTML = `<span class="color-option__dot" style="background:${v.ma_mau}"></span>${v.mau_sac}`;
      btn.addEventListener('click', () => chonMau(i, variants));
      colorWrap.appendChild(btn);

      const thumb = document.createElement('button');
      thumb.type = 'button';
      thumb.className = 'product-detail__thumb' + (i === 0 ? ' is-active' : '');
      thumb.innerHTML = `<img src="${v.hinh_anh || product.hinh_anh}" alt="${v.mau_sac}">`;
      thumb.addEventListener('click', () => chonMau(i, variants));
      thumbs.appendChild(thumb);
    });

    chonMau(0, variants);

    const btnWish = document.getElementById('btn-yeu-thich');
    if (YeuThich.co(product.id)) btnWish.classList.add('is-active');

    btnWish?.addEventListener('click', () => {
      const item = layItem(product, variants[variantIndex]);
      if (YeuThich.co(product.id)) {
        YeuThich.xoa(product.id);
        btnWish.classList.remove('is-active');
        Chung.toast('Đã xóa khỏi yêu thích');
      } else {
        YeuThich.them(item);
        btnWish.classList.add('is-active');
        Chung.toast('Đã thêm yêu thích');
      }
    });

    document.getElementById('btn-them-gio')?.addEventListener('click', () => {
      GioHang.them(layItem(product, variants[variantIndex]), 1);
      Chung.toast('Đã thêm vào giỏ hàng');
    });
  }

  function chonMau(index, variants) {
    variantIndex = index;
    const v = variants[index];
    const img = document.getElementById('hinh-san-pham');
    img.src = v.hinh_anh || sp.hinh_anh;
    img.alt = `${sp.ten} — ${v.mau_sac}`;

    document.querySelectorAll('.color-option').forEach((el, i) => {
      el.classList.toggle('is-active', i === index);
    });
    document.querySelectorAll('.product-detail__thumb').forEach((el, i) => {
      el.classList.toggle('is-active', i === index);
    });
  }

  function layItem(product, variant) {
    return {
      id: product.id,
      variant_id: variant.id || null,
      mau_sac: variant.mau_sac || '',
      ten: product.ten + (variant.mau_sac ? ` — ${variant.mau_sac}` : ''),
      gia: product.gia,
      hinh_anh: variant.hinh_anh || product.hinh_anh,
    };
  }
});
