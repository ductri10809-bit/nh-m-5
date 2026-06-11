/**
 * lich_su_lien_he.js - Contact history for user dashboard
 */

async function loadContactHistory() {
  const historyEl = document.getElementById('lich-su-lien-he');
  if (!historyEl) return;

  try {
    const response = await fetch('../../../backend/api/lien_he_lich_su.php');
    const result = await response.json();

    if (!result.success || !result.data) {
      historyEl.innerHTML = '<div style="padding: 20px; color: #999;">Chưa có liên hệ nào</div>';
      return;
    }

    const contacts = result.data;
    
    if (contacts.length === 0) {
      historyEl.innerHTML = '<div style="padding: 20px; color: #999;">Chưa có liên hệ nào</div>';
      return;
    }

    let html = '<table style="width: 100%; border-collapse: collapse;">';
    html += '<thead style="background: #f5f5f5; border-bottom: 2px solid #ddd;">';
    html += '<tr><th style="padding: 12px; text-align: left;">Ngày gửi</th>';
    html += '<th style="padding: 12px; text-align: left;">Nội dung</th>';
    html += '<th style="padding: 12px; text-align: center;">Trạng thái</th></tr></thead>';
    html += '<tbody>';

    contacts.forEach(contact => {
      const date = new Date(contact.created_at).toLocaleDateString('vi-VN');
      const preview = contact.message.substring(0, 60) + 
                     (contact.message.length > 60 ? '...' : '');
      const statusBadge = getContactStatusBadge(contact.status);

      html += `<tr style="border-bottom: 1px solid #eee;">
        <td style="padding: 12px;">${date}</td>
        <td style="padding: 12px; max-width: 300px;" title="${contact.message}">
          ${preview}
        </td>
        <td style="padding: 12px; text-align: center;">
          ${statusBadge}
        </td>
      </tr>`;
    });

    html += '</tbody></table>';
    historyEl.innerHTML = html;
  } catch (error) {
    historyEl.innerHTML = '<div style="padding: 20px; color: #dc3545;">Lỗi tải dữ liệu</div>';
    console.error('Load contact history error:', error);
  }
}

function getContactStatusBadge(status) {
  const badges = {
    'pending': '<span style="background: #ffc107; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; color: #333;">Chưa xem</span>',
    'received': '<span style="background: #17a2b8; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; color: white;">Đã nhận</span>',
    'processing': '<span style="background: #007bff; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; color: white;">Đang xử lí</span>',
    'resolved': '<span style="background: #28a745; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; color: white;">Đã giải quyết</span>',
    'cancelled': '<span style="background: #dc3545; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; color: white;">Huỷ</span>'
  };
  return badges[status] || badges['pending'];
}

// Load khi trang sẵn sàng
document.addEventListener('DOMContentLoaded', () => {
  loadContactHistory();
});
