/**
 * chung.js
 */
// Compute API base dynamically so frontend works whether served from
// http://localhost/ (with project in htdocs) or via a nested path.
function normalizePath(url) {
  return url.replace(/([^:]\/)\/+/g, '$1');
}
const currentPath = window.location.pathname;
const projectRoot = currentPath.includes('/frontend/')
  ? currentPath.split('/frontend/')[0]
  : currentPath.replace(/\/frontend\/.*$/, '');
const API_BASE = normalizePath(`${window.location.origin}${projectRoot}/backend/api`);
console.log('Using API_BASE =', API_BASE);

const Chung = {
  formatGia(gia) {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND',
      maximumFractionDigits: 0,
    }).format(gia || 0);
  },

  layQueryParam(key) {
    return new URLSearchParams(window.location.search).get(key);
  },

  async goiApi(endpoint, options = {}) {
    const url = `${API_BASE}/${endpoint}`;
    const response = await fetch(url, {
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      ...options,
    });

    const text = await response.text();
    try {
      const data = text ? JSON.parse(text) : {};
      if (!response.ok) {
        const msg = data && data.message ? data.message : (text || response.statusText || 'Server error');
        throw new Error(`${response.status} ${msg}`);
      }
      return data;
    } catch (err) {
      const serverText = (text || '').trim();
      const snippet = serverText ? (serverText.length > 400 ? serverText.slice(0, 400) + '...' : serverText) : '';
      console.error('API raw response:', serverText);
      throw new Error(`API response error: ${err.message}${snippet ? ' — server response: ' + snippet : ''}`);
    }
  },

  toast(message) {
    let el = document.querySelector('.toast');
    if (!el) {
      el = document.createElement('div');
      el.className = 'toast';
      document.body.appendChild(el);
    }
    el.textContent = message;
    el.classList.add('show');
    clearTimeout(el._timer);
    el._timer = setTimeout(() => el.classList.remove('show'), 2200);
  },

  ganGiaChoThe(grid, products) {
    if (!grid || !products) return;
    grid.querySelectorAll('.product-card').forEach(card => {
      const id = parseInt(card.dataset.id, 10);
      const sp = products.find(p => p.id === id);
      if (sp) card.dataset.gia = sp.gia;
    });
  },
};

document.addEventListener('DOMContentLoaded', () => {
  if (typeof taiComponent === 'function') {
    taiComponent('#header', '../../thanh_phan/header/header.html');
    taiComponent('#footer', '../../thanh_phan/footer/footer.html');
    
    // Initialize header JS after component loaded
    document.addEventListener('componentLoaded', (e) => {
      if (e.detail.selector === '#header' && typeof window.__cf_header_v2 === 'undefined') {
        // Reload header.js to reinitialize after component is loaded
        const script = document.createElement('script');
        script.src = '../../js/thanh_phan/header.js';
        document.head.appendChild(script);
      }
    });
  }
});

// --- User session helper: fetch current user once and expose helper functions ---
Chung.currentUser = undefined; // undefined = not yet checked, null = guest, object = user

Chung.fetchCurrentUser = async function() {
  // Fast path: use sessionStorage cached user for immediate UI update
  const cached = sessionStorage.getItem('currentUser');
  if (cached && typeof Chung.currentUser === 'undefined') {
    try {
      Chung.currentUser = JSON.parse(cached);
      // Refresh in background
      (async () => {
        try {
          const res = await fetch(`${API_BASE}/nguoi_dung.php`, { credentials: 'include' });
          if (!res.ok) { Chung.currentUser = null; sessionStorage.removeItem('currentUser'); return; }
          const data = await res.json();
          Chung.currentUser = data.success ? data.data : null;
          if (Chung.currentUser) sessionStorage.setItem('currentUser', JSON.stringify(Chung.currentUser));
        } catch (e) {
          Chung.currentUser = Chung.currentUser || null;
        }
      })();
      return Chung.currentUser;
    } catch (e) {
      // fall through to real fetch
    }
  }

  try {
    const res = await fetch(`${API_BASE}/nguoi_dung.php`, { credentials: 'include' });
    if (!res.ok) {
      Chung.currentUser = null;
      sessionStorage.removeItem('currentUser');
      return null;
    }
    const data = await res.json();
    Chung.currentUser = data.success ? data.data : null;
    if (Chung.currentUser) sessionStorage.setItem('currentUser', JSON.stringify(Chung.currentUser));
    return Chung.currentUser;
  } catch (err) {
    Chung.currentUser = null;
    sessionStorage.removeItem('currentUser');
    return null;
  }
};

Chung.ensureUserChecked = async function() {
  if (typeof Chung.currentUser !== 'undefined') return Chung.currentUser;
  return await Chung.fetchCurrentUser();
};

Chung.showLoginPrompt = function(message, returnUrl = null) {
  return new Promise((resolve) => {
    // If modal exists reuse
    let modal = document.getElementById('login-prompt-modal');
    if (!modal) {
      modal = document.createElement('div');
      modal.id = 'login-prompt-modal';
      modal.style = 'position:fixed;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);z-index:2500;';
      const loginHref = `${window.location.origin}/luxurious-fashion-store/frontend/trang/dang_nhap/dang_nhap.html${returnUrl ? `?returnUrl=${encodeURIComponent(returnUrl)}` : ''}`;
      modal.innerHTML = `
        <div style="background:#fff;padding:1.25rem;border-radius:10px;max-width:520px;width:94%;text-align:left;">
          <h3 style="margin-top:0;margin-bottom:0.5rem">Bạn cần đăng nhập</h3>
          <p style="margin-bottom:1rem">${message || 'Vui lòng đăng nhập hoặc đăng ký để tiếp tục. Bạn có thể tiếp tục đặt hàng mà không cần tài khoản, nhưng một số chức năng cần đăng nhập.'}</p>
          <div style="display:flex;gap:0.5rem;justify-content:flex-end;flex-wrap:wrap;">
            <button id="login-prompt-cancel" style="padding:0.5rem 0.75rem;border-radius:6px;border:1px solid #ccc;background:#fff;">Tiếp tục dưới dạng khách</button>
            <a id="login-prompt-login" href="${loginHref}" style="padding:0.5rem 0.9rem;border-radius:6px;background:#111;color:#fff;text-decoration:none;">Đăng nhập / Đăng ký</a>
          </div>
        </div>
      `;
      document.body.appendChild(modal);
      document.getElementById('login-prompt-cancel').addEventListener('click', function() {
        modal.remove();
        resolve(true); // continue as guest
      });
    }
    // also handle direct login link click -> let browser navigate
  });
};

Chung.logout = async function() {
  try {
    await this.goiApi('dang_xuat.php', { method: 'POST' });
  } catch (err) {
    console.error('Logout failed', err);
  }
  this.currentUser = null;
  sessionStorage.removeItem('currentUser');
  window.location.href = `${window.location.origin}/luxurious-fashion-store/frontend/trang/trang_chu/trang_chu.html`;
};
