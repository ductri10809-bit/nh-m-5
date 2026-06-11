/**
 * san_pham.js
 */
document.addEventListener('DOMContentLoaded', async () => {
  const grid = document.getElementById('danh-sach-san-pham');
  const countEl = document.getElementById('products-count');
  if (!grid) return;

  let allProducts = [];

  async function taiBoLoc() {
    try {
      const [dm, mau] = await Promise.all([
        Chung.goiApi('danh_muc.php'),
        Chung.goiApi('san_pham.php?mau_sac_list=1'),
      ]);
      const selCat = document.getElementById('danh-muc');
      const selMau = document.getElementById('mau-sac');
      if (selCat && dm.success) {
        dm.data.forEach(c => {
          selCat.innerHTML += `<option value="${c.id}">${c.ten}</option>`;
        });
      }
      if (selMau && mau.success) {
        mau.data.forEach(c => {
          selMau.innerHTML += `<option value="${c.color_name}">${c.color_name}</option>`;
        });
      }
    } catch (e) {
      console.error(e);
    }
  }

  function sapXepClient(data, sort) {
    const list = [...data];
    if (sort === 'gia_tang') list.sort((a, b) => a.gia - b.gia);
    else if (sort === 'gia_giam') list.sort((a, b) => b.gia - a.gia);
    return list;
  }

  async function taiSanPham(query = '') {
    try {
      const result = await Chung.goiApi(`san_pham.php${query}`);
      if (!result.success || !result.data) {
        grid.innerHTML = '<p class="products-empty">Không có sản phẩm.</p>';
        return;
      }
      const sort = document.getElementById('sap-xep')?.value || 'moi_nhat';
      allProducts = sapXepClient(result.data, sort);
      grid.innerHTML = allProducts.map(renderTheSanPham).join('');
      grid.querySelectorAll('.product-card').forEach(card => {
        const sp = allProducts.find(p => p.id === parseInt(card.dataset.id, 10));
        if (sp) {
          card.dataset.gia = sp.gia;
          card.__sp = sp;
        }
      });
      if (countEl) countEl.textContent = `${allProducts.length} sản phẩm`;
    } catch (error) {
      console.error('Loi tai san pham:', error);
    }
  }

  function buildQuery() {
    const cat = document.getElementById('danh-muc')?.value;
    const mau = document.getElementById('mau-sac')?.value;
    const isSale = document.getElementById('is_sale')?.checked;
    const isTrend = document.getElementById('is_trend')?.checked;
    const params = new URLSearchParams();
    
    if (cat) params.set('category_id', cat);
    if (mau) params.set('mau_sac', mau);
    if (isSale) params.set('is_sale', '1');
    if (isTrend) params.set('is_trend', '1');
    
    const q = params.toString();
    const query = q ? `?${q}` : '';
    console.log('Filter Query:', query);
    return query;
  }

  function setupFilterForm() {
    const form = document.getElementById('form-bo-loc');
    const resetBtn = document.getElementById('bo-loc-reset');
    
    console.log('setupFilterForm called - form:', !!form, 'resetBtn:', !!resetBtn);
    
    if (form) {
      form.onsubmit = (e) => {
        e.preventDefault();
        console.log('Form submit prevented, loading with query:', buildQuery());
        taiSanPham(buildQuery());
        return false;
      };
    }
    
    if (resetBtn) {
      resetBtn.onclick = (e) => {
        e.preventDefault();
        console.log('Reset clicked');
        if (form) form.reset();
        taiSanPham();
        return false;
      };
    }
  }

  // Load filter component
  await taiBoLoc();
  
  // Setup filter listeners - try multiple ways to ensure it works
  // Method 1: Wait for component to load
  document.addEventListener('componentLoaded', (e) => {
    if (e.detail?.selector === '#bo-loc') {
      console.log('componentLoaded event fired for bo-loc');
      setTimeout(() => setupFilterForm(), 100);
    }
  });
  
  // Method 2: Fallback - try immediately and again after delay
  setTimeout(() => {
    console.log('Timeout - checking if form exists:', !!document.getElementById('form-bo-loc'));
    setupFilterForm();
  }, 500);
  
  // Check if search keyword in URL
  const urlParams = new URLSearchParams(window.location.search);
  const searchKeyword = urlParams.get('search') || urlParams.get('tim');
  
  if (searchKeyword) {
    await taiSanPham(`?tim=${encodeURIComponent(searchKeyword)}`);
  } else {
    await taiSanPham();
  }

  document.addEventListener('searchQuery', (e) => {
    taiSanPham(`?tim=${encodeURIComponent(e.detail.q)}`);
  });
});
