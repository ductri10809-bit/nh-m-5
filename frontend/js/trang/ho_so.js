/**
 * ho_so.js - User Profile (Customer)
 */
document.addEventListener('DOMContentLoaded', async () => {
  const form = document.getElementById('form-ho-so');
  const orderCountEl = document.getElementById('order-count');
  const orderItemsCountEl = document.getElementById('order-items-count');
  const orderTotalEl = document.getElementById('order-total');
  const orderListEl = document.getElementById('don-hang');
  const orderSearchInput = document.getElementById('order-search');
  const sidebarName = document.getElementById('sidebar-name');
  const sidebarEmail = document.getElementById('sidebar-email');
  const logoutBtn = document.getElementById('logout-btn');

  let orders = [];

  function formatDate(value) {
    try {
      const date = new Date(value);
      return date.toLocaleDateString('vi-VN', {
        day: '2-digit', month: '2-digit', year: 'numeric'
      });
    } catch (err) {
      return value;
    }
  }

  function getStatusLabel(status) {
    if (!status) return 'Chờ xử lý';
    return status.replace(/_/g, ' ');
  }

  function formatMoney(amount) {
    return Chung.formatGia(amount);
  }

  function filterOrders(query) {
    if (!query) return orders;
    const normalized = query.toLowerCase().trim();
    return orders.filter(order => {
      return String(order.order_id).includes(normalized)
        || String(order.order_status || '').toLowerCase().includes(normalized)
        || String(order.address || '').toLowerCase().includes(normalized)
        || String(order.phone || '').toLowerCase().includes(normalized)
        || String(order.customer_name || '').toLowerCase().includes(normalized);
    });
  }

  function renderOrderDetails(details, order = {}) {
    if (!Array.isArray(details) || details.length === 0) {
      return '<div class="order-message">Không có chi tiết cho đơn hàng này.</div>';
    }

    const rows = details.map(item => {
      let hinh = '../../tai_nguyen/default.png';
      if (item.hinh_anh) {
        if (item.hinh_anh.startsWith('http://') || item.hinh_anh.startsWith('https://')) {
          hinh = item.hinh_anh;
        } else {
          hinh = `../../uploads/san_pham/${item.hinh_anh}`;
        }
      }
      return `
        <tr>
          <td style="text-align: center;">
            <img src="${hinh}" alt="${item.ten || 'Sản phẩm'}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
          </td>
          <td>${item.ten || 'Sản phẩm'}</td>
          <td>${item.quantity || 0}</td>
          <td>${formatMoney(item.price || 0)}</td>
          <td>${formatMoney((item.quantity || 0) * (item.price || 0))}</td>
        </tr>
      `;
    }).join('');

    return `
      <div class="order-detail">
        <table class="order-detail-table">
          <thead>
            <tr>
              <th>Hình ảnh</th>
              <th>Sản phẩm</th>
              <th>Số lượng</th>
              <th>Giá</th>
              <th>Thành tiền</th>
            </tr>
          </thead>
          <tbody>${rows}</tbody>
        </table>
      </div>
    `;
  }

  function renderOrders(list) {
    if (!orderListEl) return;
    if (!Array.isArray(list) || list.length === 0) {
      orderListEl.innerHTML = '<div class="order-message">Bạn chưa có đơn hàng nào. Mua sắm ngay để tạo đơn mới.</div>';
      return;
    }

    orderListEl.innerHTML = list.map(order => {
      return `
        <article class="order-item">
          <div class="order-item-top">
            <h3>Đơn #${order.order_id}</h3>
            <div class="order-status">${getStatusLabel(order.order_status)}</div>
          </div>
          <div class="order-meta">
            <span>Ngày đặt: ${formatDate(order.order_date || order.created_at || '')}</span>
            <span>Khách hàng: ${order.customer_name || order.email || '---'}</span>
            <span>Địa chỉ: ${order.address || '---'}</span>
            <span>Tổng tiền: ${formatMoney(order.total_amount || order.tong_tien || 0)}</span>
          </div>
          <div class="order-actions">
            <button data-order-id="${order.order_id}" class="btn btn-ghost btn-order-detail">Xem chi tiết</button>
            <button data-order-id="${order.order_id}" class="btn btn-ghost btn-order-delete" style="color: #d32f2f;">Xoá</button>
          </div>
          <div id="order-detail-${order.order_id}" class="order-detail-container"></div>
        </article>
      `;
    }).join('');

    // Xem chi tiết
    orderListEl.querySelectorAll('.btn-order-detail').forEach(button => {
      button.addEventListener('click', async (event) => {
        const id = event.currentTarget.dataset.orderId;
        const detailContainer = document.getElementById(`order-detail-${id}`);
        if (!detailContainer) return;

        if (detailContainer.dataset.loaded === 'true') {
          detailContainer.innerHTML = '';
          detailContainer.dataset.loaded = 'false';
          return;
        }

        detailContainer.innerHTML = '<div class="order-message">Đang tải chi tiết...</div>';
        try {
          const result = await Chung.goiApi(`don_hang.php?id=${id}`);
          if (result.success && result.data) {
            detailContainer.innerHTML = renderOrderDetails(result.data.chi_tiet || []);
            detailContainer.dataset.loaded = 'true';
          } else {
            detailContainer.innerHTML = `<div class="order-message">${result.message || 'Không tải được chi tiết.'}</div>`;
          }
        } catch (err) {
          detailContainer.innerHTML = `<div class="order-message">Lỗi khi tải chi tiết đơn hàng.</div>`;
        }
      });
    });

    // Xoá đơn
    orderListEl.querySelectorAll('.btn-order-delete').forEach(button => {
      button.addEventListener('click', async (event) => {
        const id = event.currentTarget.dataset.orderId;
        if (confirm('Bạn chắc chắn muốn xoá đơn hàng này?')) {
          try {
            const result = await Chung.goiApi('don_hang.php', {
              method: 'DELETE',
              body: JSON.stringify({ order_id: id })
            });
            if (result.success) {
              alert('Đơn hàng đã được xoá');
              loadProfile();
            } else {
              alert(result.message || 'Không thể xoá đơn hàng');
            }
          } catch (err) {
            alert('Lỗi khi xoá đơn hàng');
          }
        }
      });
    });
  }

  function updateSummary(list) {
    const totalOrders = list.length;
    const totalItems = list.reduce((sum, order) => sum + (order.items_count || 0), 0);
    const totalMoney = list.reduce((sum, order) => sum + Number(order.total_amount || order.tong_tien || 0), 0);

    orderCountEl.textContent = totalOrders;
    orderItemsCountEl.textContent = totalItems || 0;
    orderTotalEl.textContent = formatMoney(totalMoney);
  }

  function applyData(user, orderData) {
    if (sidebarName) sidebarName.textContent = user.ho_ten || 'Khách hàng';
    if (sidebarEmail) sidebarEmail.textContent = user.email || '';

    if (form) {
      form.querySelector('[name="ho_ten"]').value = user.ho_ten || '';
      form.querySelector('[name="email"]').value = user.email || '';
      form.querySelector('[name="sdt"]').value = user.sdt || '';
      form.querySelector('[name="dia_chi"]').value = user.dia_chi || '';
    }

    orders = Array.isArray(orderData) ? orderData : [];
    updateSummary(orders);
    renderOrders(orders);
  }

  async function loadProfile() {
    try {
      const profileResult = await Chung.goiApi('nguoi_dung.php');
      if (!profileResult.success || !profileResult.data) {
        window.location.href = '../dang_nhap/dang_nhap.html';
        return;
      }

      // Redirect admin to dashboard
      if (profileResult.data.role === 'admin') {
        window.location.href = '../../admin/dashboard.html';
        return;
      }

      const orderResult = await Chung.goiApi('don_hang.php');
      if (!orderResult.success) {
        applyData(profileResult.data, []);
        return;
      }

      const enrichedOrders = Array.isArray(orderResult.data) ? orderResult.data.map(order => ({
        ...order,
        items_count: order.item_count || 0,
      })) : [];
      applyData(profileResult.data, enrichedOrders);
    } catch (error) {
      window.location.href = '../dang_nhap/dang_nhap.html';
    }
  }

  if (logoutBtn) {
    logoutBtn.addEventListener('click', (event) => {
      event.preventDefault();
      if (typeof Chung.logout === 'function') {
        Chung.logout();
      } else {
        window.location.href = '../dang_nhap/dang_nhap.html';
      }
    });
  }

  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const data = Object.fromEntries(new FormData(form).entries());
      try {
        const update = await Chung.goiApi('nguoi_dung.php', {
          method: 'PUT',
          body: JSON.stringify(data)
        });
        if (update.success) {
          alert('Cập nhật thành công');
          loadProfile();
        } else {
          alert(update.message || 'Cập nhật thất bại');
        }
      } catch (err) {
        alert('Lỗi cập nhật thông tin. Vui lòng thử lại.');
      }
    });
  }

  if (orderSearchInput) {
    orderSearchInput.addEventListener('input', () => {
      renderOrders(filterOrders(orderSearchInput.value));
    });
  }

  loadProfile();
});
