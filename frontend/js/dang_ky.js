/**
 * dang_ky.js
 */
document.addEventListener('DOMContentLoaded', async () => {
  const formCustomer = document.getElementById('form-dang-ky');
  const formAdmin = document.getElementById('form-dang-ky-admin');
  const tabButtons = document.querySelectorAll('.tab-btn');
  
  if (!formCustomer || !formAdmin) return;

  try {
    const existingUser = await Chung.ensureUserChecked();
    if (existingUser) {
      window.location.href = '../trang_chu/trang_chu.html';
      return;
    }
  } catch (err) {
    console.warn('Unable to verify session before registration:', err);
  }

  // Tab switching
  tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const tab = btn.dataset.tab;
      
      tabButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      
      if (tab === 'customer') {
        formCustomer.style.display = 'block';
        formAdmin.style.display = 'none';
      } else {
        formCustomer.style.display = 'none';
        formAdmin.style.display = 'block';
      }
    });
  });

  // Customer registration
  formCustomer.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(formCustomer);
    const data = Object.fromEntries(formData.entries());

    if (data.password !== data.confirm_password) {
      alert('Mật khẩu xác nhận không khớp');
      return;
    }

    try {
      const result = await Chung.goiApi('dang_ky.php', {
        method: 'POST',
        body: JSON.stringify(data)
      });

      if (result.success) {
        alert(result.message || 'Đăng ký thành công! ');
        if (result.data?.requires_otp) {
          window.location.href = '../dang_nhap/dang_nhap.html';
        } else {
          window.location.href = '../dang_nhap/dang_nhap.html';
        }
      } else {
        alert(result.message || 'Đăng ký thất bại');
      }
    } catch (error) {
      console.error('dang_ky error', error);
      alert(error && error.message ? error.message : 'Lỗi kết nối server');
    }
  });

  // Admin registration
  formAdmin.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(formAdmin);
    const data = Object.fromEntries(formData.entries());

    if (data.password !== data.confirm_password) {
      alert('Mật khẩu xác nhận không khớp');
      return;
    }

    if (!data.admin_code || data.admin_code.trim() === '') {
      alert('Vui lòng nhập mã code quản trị');
      return;
    }

    try {
      const result = await Chung.goiApi('dang_ky_admin.php', {
        method: 'POST',
        body: JSON.stringify(data)
      });

      if (result.success) {
        alert('Đăng ký admin thành công! Vui lòng đăng nhập.');
        window.location.href = '../dang_nhap/dang_nhap.html';
      } else {
        alert(result.message || 'Đăng ký admin thất bại');
      }
    } catch (error) {
      console.error('dang_ky_admin error', error);
      alert(error && error.message ? error.message : 'Lỗi kết nối server');
    }
  });
});

