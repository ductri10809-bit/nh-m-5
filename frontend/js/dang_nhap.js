/**
 * dang_nhap.js
 */
document.addEventListener('DOMContentLoaded', async () => {
  const formCustomer = document.getElementById('form-dang-nhap');
  const formAdmin = document.getElementById('form-dang-nhap-admin');
  const tabButtons = document.querySelectorAll('.tab-btn');
  
  if (!formCustomer || !formAdmin) return;

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

  try {
    const existingUser = await Chung.ensureUserChecked();
    if (existingUser) {
      if (existingUser.role === 'admin') {
        window.location.href = '../../admin/dashboard.html';
      } else {
        window.location.href = '../trang_chu/trang_chu.html';
      }
      return;
    }
  } catch (err) {
    console.warn('Unable to verify session before login:', err);
  }

  // Customer login
  formCustomer.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(formCustomer);
    const data = Object.fromEntries(formData.entries());
    data.role = 'customer';

    try {
      const result = await Chung.goiApi('dang_nhap.php', {
        method: 'POST',
        body: JSON.stringify(data)
      });

      if (result.success) {
        try { if (result.data) sessionStorage.setItem('currentUser', JSON.stringify(result.data)); } catch(e){}
        window.location.href = '../trang_chu/trang_chu.html';
      } else {
        alert(result.message || 'Đăng nhập thất bại');
      }
    } catch (error) {
      console.error('dang_nhap error', error);
      alert(error && error.message ? error.message : 'Lỗi kết nối server');
    }
  });

  // Admin login
  formAdmin.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(formAdmin);
    const data = Object.fromEntries(formData.entries());
    data.role = 'admin';

    try {
      const result = await Chung.goiApi('dang_nhap.php', {
        method: 'POST',
        body: JSON.stringify(data)
      });

      if (result.success) {
        if (result.data?.role === 'admin') {
          try { if (result.data) sessionStorage.setItem('currentUser', JSON.stringify(result.data)); } catch(e){}
          window.location.href = '../../admin/dashboard.html';
        } else {
          alert('Tài khoản này không có quyền quản trị');
        }
      } else {
        alert(result.message || 'Đăng nhập thất bại');
      }
    } catch (error) {
      console.error('admin login error', error);
      alert(error && error.message ? error.message : 'Lỗi kết nối server');
    }
  });
});

