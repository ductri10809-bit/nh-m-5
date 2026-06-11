/**
 * trang_chu.js
 */
document.addEventListener('DOMContentLoaded', async () => {
  const grid = document.getElementById('san-pham-noi-bat');
  if (!grid) return;

  try {
    const result = await Chung.goiApi('san_pham.php?noi_bat=1');
    if (result.success && result.data) {
      grid.innerHTML = result.data.map(renderTheSanPham).join('');
      grid.querySelectorAll('.product-card').forEach(card => {
        const sp = result.data.find(p => p.id === parseInt(card.dataset.id, 10));
        if (sp) {
          card.dataset.gia = sp.gia;
          card.__sp = sp;
        }
      });
    }
  } catch (error) {
    console.error('Loi tai san pham noi bat:', error);
  }
});
