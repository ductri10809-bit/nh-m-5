/**
 * doi_mat_khau.js
 */
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('form-doi-mk');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(form).entries());
    if (data.new_password !== data.confirm_password) {
      alert('Mật khẩu xác nhận không khớp');
      return;
    }

    try {
      const result = await Chung.goiApi('doi_mat_khau.php', {
        method: 'POST',
        body: JSON.stringify({ old_password: data.old_password, new_password: data.new_password })
      });

      if (result.success) {
        alert(result.message || 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại.');
        if (typeof Chung.logout === 'function') Chung.logout();
        else window.location.href = '../dang_nhap/dang_nhap.html';
      } else {
        alert(result.message || 'Đổi mật khẩu thất bại');
      }
    } catch (err) {
      console.error('doi_mat_khau error', err);
      alert(err && err.message ? err.message : 'Lỗi kết nối đến server');
    }
  });
});
