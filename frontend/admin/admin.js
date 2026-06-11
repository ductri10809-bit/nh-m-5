document.addEventListener('DOMContentLoaded', () => {
  initAdmin();
});

async function initAdmin() {
  await Chung.ensureUserChecked();
  if (!Chung.currentUser || Chung.currentUser.role !== 'admin') {
    document.body.innerHTML = `
      <main class="admin-dashboard">
        <section class="admin-section">
          <h2>Truy cập bị từ chối</h2>
          <p>Bạn cần đăng nhập bằng tài khoản quản trị viên để sử dụng trang admin.</p>
          <a href="../trang/dang_nhap/dang_nhap.html" class="btn btn-primary">Đăng nhập</a>
        </section>
      </main>`;
    return;
  }

  bindForms();
  bindLogout();
  await Promise.all([loadSummary(), loadProducts(), loadCategories(), loadUsers(), loadOrders(), loadPosts()]);
}

async function adminRequest(action, method = 'GET', payload = null) {
  const options = { method };
  if (payload) {
    options.body = JSON.stringify(payload);
  }
  const response = await Chung.goiApi(`admin.php?action=${action}`, options);
  if (!response.success) {
    throw new Error(response.message || 'Lỗi admin API');
  }
  return response.data;
}

function bindForms() {
  const productForm = document.getElementById('form-add-product');
  const categoryForm = document.getElementById('form-add-category');
  const postForm = document.getElementById('form-add-post');

  productForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = formToJson(productForm);
    data.is_bestseller = productForm.is_bestseller.checked;
    data.is_trend = productForm.is_trend.checked;
    data.is_sale = productForm.is_sale.checked;
    if (data.sale_price === '') data.sale_price = null;
    try {
      await adminRequest('product', 'POST', data);
      await loadProducts();
      productForm.reset();
      Chung.toast('Sản phẩm mới đã được tạo');
    } catch (err) {
      alert(err.message);
    }
  });

  categoryForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
      const data = formToJson(categoryForm);
      await adminRequest('category', 'POST', data);
      await loadCategories();
      categoryForm.reset();
      Chung.toast('Danh mục đã được thêm');
    } catch (err) {
      alert(err.message);
    }
  });

  postForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = formToJson(postForm);
    data.ngay_dang = data.ngay_dang || new Date().toISOString().slice(0, 10);
    try {
      await adminRequest('post', 'POST', data);
      await loadPosts();
      postForm.reset();
      Chung.toast('Bài viết đã được tạo');
    } catch (err) {
      alert(err.message);
    }
  });

  document.body.addEventListener('click', async (e) => {
    const target = e.target;
    if (target.matches('.btn-delete-product')) {
      const id = target.dataset.id;
      if (!confirm('Xác nhận xóa sản phẩm?')) return;
      await adminRequest(`product&id=${id}`, 'DELETE');
      await loadProducts();
      Chung.toast('Sản phẩm đã được xóa');
    }
    if (target.matches('.btn-delete-order')) {
      const id = target.dataset.id;
      if (!confirm('Xác nhận xóa đơn hàng?')) return;
      await adminRequest(`order&id=${id}`, 'DELETE');
      await loadOrders();
      Chung.toast('Đơn hàng đã được xóa');
    }
    if (target.matches('.btn-toggle-trend')) {
      const id = target.dataset.id;
      const currentValue = target.dataset.value === '1' ? 1 : 0;
      await toggleProductFlag(id, 'is_trend', currentValue);
      await loadProducts();
    }
    if (target.matches('.btn-update-sale')) {
      const id = target.dataset.id;
      const row = target.closest('tr');
      const salePrice = row.querySelector('.sale-price-input').value;
      if (!salePrice) {
        alert('Vui lòng nhập giá sale');
        return;
      }
      await updateProductSale(id, parseInt(salePrice));
      await loadProducts();
    }
    if (target.matches('.btn-delete-category')) {
      const id = target.dataset.id;
      if (!confirm('Xác nhận xóa danh mục?')) return;
      await adminRequest(`category&id=${id}`, 'DELETE');
      await loadCategories();
      Chung.toast('Danh mục đã được xóa');
    }
    if (target.matches('.btn-delete-user')) {
      const id = target.dataset.id;
      if (!confirm('Xác nhận xóa người dùng?')) return;
      await adminRequest(`user&id=${id}`, 'DELETE');
      await loadUsers();
      Chung.toast('Người dùng đã được xóa');
    }
    if (target.matches('.btn-delete-post')) {
      const id = target.dataset.id;
      if (!confirm('Xác nhận xóa bài viết?')) return;
      await adminRequest(`post&id=${id}`, 'DELETE');
      await loadPosts();
      Chung.toast('Bài viết đã được xóa');
    }
    if (target.matches('.btn-update-user')) {
      const row = target.closest('tr');
      const id = row.dataset.id;
      const role = row.querySelector('.user-role-select').value;
      await adminRequest('user', 'PUT', { id, role });
      Chung.toast('Quyền người dùng đã được cập nhật');
      await loadUsers();
    }
    if (target.matches('.btn-update-order')) {
      const row = target.closest('tr');
      const id = row.dataset.id;
      const status = row.querySelector('.order-status-select').value;
      await adminRequest('order_status', 'POST', { id, status });
      Chung.toast('Trạng thái đơn hàng đã được cập nhật');
      await loadOrders();
    }
  });
}

function bindLogout() {
  const logoutBtn = document.getElementById('btn-logout');
  if (!logoutBtn) {
    console.warn('Logout button not found');
    return;
  }
  
  logoutBtn.addEventListener('click', async (e) => {
    console.log('Logout button clicked');
    if (!confirm('Bạn chắc chắn muốn đăng xuất?')) return;
    try {
      console.log('Calling logout API...');
      await Chung.goiApi('dang_xuat.php', { method: 'POST' });
      console.log('Logout successful');
      
      // Clear all client-side session data
      sessionStorage.removeItem('currentUser');
      localStorage.removeItem('currentUser');
      Chung.currentUser = null;
      
      Chung.toast('Đã đăng xuất thành công');
      
      // Redirect after short delay to ensure toast is visible
      setTimeout(() => {
        window.location.href = '../trang/dang_nhap/dang_nhap.html';
      }, 800);
    } catch (err) {
      console.error('Logout error:', err);
      Chung.toast('Lỗi: ' + err.message);
    }
  });
}

async function toggleProductFlag(productId, field, currentValue) {
  try {
    const newValue = currentValue ? 0 : 1;
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('field', field);
    formData.append('value', newValue);
    
    console.log('Toggling:', field, 'Product:', productId, 'NewValue:', newValue);
    
    const response = await fetch('/luxurious-fashion-store/backend/api/cap_nhat_san_pham_dac_biet.php', {
      method: 'POST',
      body: formData
    });
    const data = await response.json();
    console.log('Response:', data);
    
    if (data.success) {
      Chung.toast(`${field === 'is_trend' ? 'Xu hướng' : 'Sale'} đã được cập nhật`);
      await loadProducts();
    } else {
      throw new Error(data.message || 'Lỗi cập nhật');
    }
  } catch (err) {
    console.error('Error:', err);
    alert('Lỗi: ' + err.message);
  }
}

async function updateProductSale(productId, salePrice) {
  try {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('field', 'sale_price');
    formData.append('value', salePrice);
    formData.append('is_sale', 1);
    
    console.log('Updating sale price:', productId, salePrice);
    
    const response = await fetch('/luxurious-fashion-store/backend/api/cap_nhat_san_pham_dac_biet.php', {
      method: 'POST',
      body: formData
    });
    const data = await response.json();
    console.log('Response:', data);
    
    if (data.success) {
      Chung.toast('Giá sale đã được cập nhật');
    } else {
      throw new Error(data.message || 'Lỗi cập nhật');
    }
  } catch (err) {
    console.error('Error:', err);
    alert('Lỗi: ' + err.message);
  }
}

function formToJson(form) {
  const data = {};
  new FormData(form).forEach((value, key) => {
    data[key] = value;
  });
  return data;
}

async function loadSummary() {
  try {
    const summary = await adminRequest('summary');
    document.getElementById('summary-products').textContent = summary.products;
    document.getElementById('summary-categories').textContent = summary.categories;
    document.getElementById('summary-users').textContent = summary.users;
    document.getElementById('summary-orders').textContent = summary.orders;
    document.getElementById('summary-posts').textContent = summary.posts;
  } catch (err) {
    console.error(err);
  }
}

async function loadProducts() {
  const table = document.querySelector('#products-table tbody');
  if (!table) return;
  table.innerHTML = '<tr><td colspan="8">Đang tải...</td></tr>';
  try {
    const products = await adminRequest('products');
    table.innerHTML = products.map(product => `
      <tr data-id="${product.id}">
        <td>${product.id}</td>
        <td>${product.ten}</td>
        <td>${Chung.formatGia(product.gia)}</td>
        <td>
          <div style="display: flex; gap: 5px;">
            <input type="number" class="sale-price-input" value="${product.sale_price || ''}" placeholder="Giá sale" data-id="${product.id}" style="width: 100px; padding: 5px;">
            <button type="button" class="btn btn-update-sale" data-id="${product.id}">Cập nhập</button>
          </div>
        </td>
        <td>${product.stock_quantity || 0}</td>
        <td><button type="button" class="btn btn-toggle-trend ${product.is_trend ? 'btn-primary' : 'btn-secondary'}" data-id="${product.id}" data-value="${product.is_trend || 0}">${product.is_trend ? '✓ Có' : 'Không'}</button></td>
        <td><button type="button" class="btn btn-secondary btn-delete-product" data-id="${product.id}">Xóa</button></td>
      </tr>
    `).join('');
  } catch (err) {
    table.innerHTML = `<tr><td colspan="8">Lỗi: ${err.message}</td></tr>`;
  }
}

async function loadCategories() {
  const table = document.querySelector('#categories-table tbody');
  if (!table) return;
  table.innerHTML = '<tr><td colspan="3">Đang tải...</td></tr>';
  try {
    const categories = await adminRequest('categories');
    table.innerHTML = categories.map(category => `
      <tr data-id="${category.id}">
        <td>${category.id}</td>
        <td>${category.ten || category.category_name || 'Không có tên'}</td>
        <td><button type="button" class="btn btn-secondary btn-delete-category" data-id="${category.id}">Xóa</button></td>
      </tr>
    `).join('');
  } catch (err) {
    table.innerHTML = `<tr><td colspan="3">Lỗi: ${err.message}</td></tr>`;
  }
}

async function loadUsers() {
  const table = document.querySelector('#users-table tbody');
  if (!table) return;
  table.innerHTML = '<tr><td colspan="5">Đang tải...</td></tr>';
  try {
    const users = await adminRequest('users');
    table.innerHTML = users.map(user => `
      <tr data-id="${user.id}">
        <td>${user.id}</td>
        <td>${user.ho_ten}</td>
        <td>${user.email}</td>
        <td>
          <select class="user-role-select">
            <option value="customer" ${user.role === 'customer' ? 'selected' : ''}>Customer</option>
            <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
          </select>
        </td>
        <td><button type="button" class="btn btn-secondary btn-update-user">Lưu</button> <button type="button" class="btn btn-secondary btn-delete-user" data-id="${user.id}">Xóa</button></td>
      </tr>
    `).join('');
  } catch (err) {
    table.innerHTML = `<tr><td colspan="5">Lỗi: ${err.message}</td></tr>`;
  }
}

async function loadOrders() {
  const table = document.querySelector('#orders-table tbody');
  if (!table) return;
  table.innerHTML = '<tr><td colspan="5">Đang tải...</td></tr>';
  try {
    const orders = await adminRequest('orders');
    table.innerHTML = orders.map(order => {
      const isProcessedOrCancelled = order.order_status === 'da_xu_ly' || order.order_status === 'huy';
      return `
      <tr data-id="${order.order_id}">
        <td>${order.order_id}</td>
        <td>${order.customer_name || order.customer_email || 'Khách vãng lai'}</td>
        <td>${Chung.formatGia(order.total_amount)}</td>
        <td>
          <select class="order-status-select">
            <option value="cho_xu_ly" ${order.order_status === 'cho_xu_ly' ? 'selected' : ''}>Chờ xử lý</option>
            <option value="da_xu_ly" ${order.order_status === 'da_xu_ly' ? 'selected' : ''}>Đã xử lý</option>
            <option value="huy" ${order.order_status === 'huy' ? 'selected' : ''}>Hủy</option>
          </select>
        </td>
        <td>
          <button type="button" class="btn btn-secondary btn-update-order">Cập nhật</button>
          ${isProcessedOrCancelled ? `<button type="button" class="btn btn-danger btn-delete-order" data-id="${order.order_id}">Xóa</button>` : ''}
        </td>
      </tr>
    `}).join('');
  } catch (err) {
    table.innerHTML = `<tr><td colspan="5">Lỗi: ${err.message}</td></tr>`;
  }
}

async function loadPosts() {
  const table = document.querySelector('#posts-table tbody');
  if (!table) return;
  table.innerHTML = '<tr><td colspan="4">Đang tải...</td></tr>';
  try {
    const posts = await adminRequest('posts');
    table.innerHTML = posts.map(post => `
      <tr data-id="${post.id}">
        <td>${post.id}</td>
        <td>${post.tieu_de}</td>
        <td>${post.ngay_dang}</td>
        <td><button type="button" class="btn btn-secondary btn-delete-post" data-id="${post.id}">Xóa</button></td>
      </tr>
    `).join('');
  } catch (err) {
    table.innerHTML = `<tr><td colspan="4">Lỗi: ${err.message}</td></tr>`;
  }
}
