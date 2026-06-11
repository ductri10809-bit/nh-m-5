/**
 * yeu_thich.js (trang)
 */
document.addEventListener('DOMContentLoaded', async () => {
  const grid = document.getElementById('wishlist-grid');
  const empty = document.getElementById('wishlist-empty');
  if (!grid) return;

  async function render() {
    const ids = YeuThich.lay().map(i => i.id);
    if (!ids.length) {
      grid.innerHTML = '';
      empty.hidden = false;
      return;
    }
    empty.hidden = true;

    try {
      const result = await Chung.goiApi('san_pham.php');
      if (!result.success) return;
      const products = result.data.filter(p => ids.includes(p.id));
      grid.innerHTML = products.map(renderTheSanPham).join('');
      grid.querySelectorAll('.product-card').forEach(card => {
        const sp = products.find(p => p.id === parseInt(card.dataset.id, 10));
        if (sp) {
          card.dataset.gia = sp.gia;
          card.__sp = sp;
        }
      });
    } catch (e) {
      console.error(e);
      const local = YeuThich.lay();
      grid.innerHTML = local.map(sp => renderTheSanPham({
        id: sp.id, ten: sp.ten, gia: sp.gia, hinh_anh: sp.hinh_anh, bien_the: [],
      })).join('');
    }
  }

  render();
  document.addEventListener('wishlistUpdated', render);
});
