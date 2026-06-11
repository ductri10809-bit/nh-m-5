/**
 * chi_tiet_tin_tuc.js
 */
document.addEventListener('DOMContentLoaded', async () => {
  const id = Chung.layQueryParam('id');
  if (!id) return;

  try {
    const response = await fetch('../../tai_nguyen/du_lieu/tin_tuc/danh_sach.json');
    const danhSach = await response.json();
    const tin = danhSach.find(t => t.id === parseInt(id));
    if (!tin) return;

    document.getElementById('tieu-de').textContent = tin.tieu_de;
    document.getElementById('ngay-dang').textContent = tin.ngay_dang;
    document.getElementById('noi-dung').textContent = tin.noi_dung;
    if (tin.hinh_anh) {
      document.getElementById('hinh-anh').src = tin.hinh_anh;
    }
  } catch (error) {
    console.error('Loi tai chi tiet tin tuc:', error);
  }
});
