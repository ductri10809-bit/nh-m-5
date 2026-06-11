/**
 * tin_tuc.js
 */
document.addEventListener('DOMContentLoaded', async () => {
  const grid = document.getElementById('danh-sach-tin-tuc');
  if (!grid) return;

  try {
    const response = await fetch('../../tai_nguyen/du_lieu/tin_tuc/danh_sach.json');
    const danhSach = await response.json();
    grid.innerHTML = danhSach.map(tin => `
      <article class="news-card">
        <a href="../chi_tiet_tin_tuc/chi_tiet_tin_tuc.html?id=${tin.id}">
          <img src="${tin.hinh_anh}" alt="${tin.tieu_de}">
          <div class="p-1">
            <h3>${tin.tieu_de}</h3>
            <p>${tin.tom_tat}</p>
            <small>${tin.ngay_dang}</small>
          </div>
        </a>
      </article>
    `).join('');
  } catch (error) {
    console.error('Loi tai tin tuc:', error);
  }
});
