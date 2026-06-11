/**
 * quen_mat_khau.js
 */
document.addEventListener('DOMContentLoaded', async () => {
  const form = document.getElementById('form-quen-mat-khau');
  if (!form) return;

  try {
    const existingUser = await Chung.ensureUserChecked();
    if (existingUser) {
      window.location.href = '../trang_chu/trang_chu.html';
      return;
    }
  } catch (err) {
    console.warn('Unable to verify session before reset:', err);
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
      const result = await Chung.goiApi('quen_mat_khau.php', {
        method: 'POST',
        body: JSON.stringify(data)
      });

      if (result.success) {
        alert('Hướng dẫn đặt lại mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.');
        window.location.href = '../dang_nhap/dang_nhap.html';
      } else {
        alert(result.message || 'Không thể gửi hướng dẫn. Vui lòng thử lại.');
      }
    } catch (error) {
      console.error('quen_mat_khau error', error);
      alert(error && error.message ? error.message : 'Lỗi kết nối server');
    }
  });
});
