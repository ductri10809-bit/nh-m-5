/**
 * Minimal header script (Céline-like responsive behavior)
 * - idempotent init
 * - hamburger open/close with overlay and body scroll lock
 * - accessible aria attributes
 */
(() => {
  if (window.__cf_header_v2) return; // guard
  window.__cf_header_v2 = true;

  function safeGet(id) { return document.getElementById(id); }

  function init() {
    const dropdownBtn = safeGet('nav-dropdown-btn');
    const currentPageSpan = dropdownBtn ? dropdownBtn.querySelector('.nav-current-page') : null;
    const nav = safeGet('site-nav');
    const cartBadge = safeGet('cart-count');
    const wishBadge = safeGet('wishlist-count');
    const authEl = safeGet('auth-actions');
    const searchIconBtn = safeGet('search-icon-btn');
    const searchInput = safeGet('tim-kiem');

    // update badges if available
    try {
      if (cartBadge && typeof GioHang !== 'undefined') cartBadge.textContent = GioHang.dem();
      if (wishBadge && typeof YeuThich !== 'undefined') wishBadge.textContent = YeuThich.dem();
    } catch (e) { /* ignore */ }

    // Handle search icon click on mobile
    if (searchIconBtn && searchInput) {
      searchIconBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        searchInput.focus();
      });
      
      // Search on Enter key
      searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          const keyword = searchInput.value.trim();
          if (keyword) {
            // Redirect to search results
            window.location.href = `../../trang/san_pham/san_pham.html?search=${encodeURIComponent(keyword)}`;
          }
        }
      });
    }

    // detect current page and update dropdown button text
    function updateCurrentPage() {
      if (!currentPageSpan) return;
      const pathname = window.location.pathname.toLowerCase();
      const pages = {
        'trang_chu': 'Trang chủ',
        'san_pham': 'Sản phẩm',
        'xu_huong': 'Xu hướng',
        'sale': 'SALE',
        'tin_tuc': 'Tin tức',
        'lien_he': 'Liên hệ',
        'yeu_thich': 'Yêu thích',
        'gio_hang': 'Giỏ hàng',
        'dang_nhap': 'Đăng nhập',
        'dang_ky': 'Đăng ký',
        'ho_so': 'Thông tin cá nhân',
      };
      
      for (const [key, label] of Object.entries(pages)) {
        if (pathname.includes(key)) {
          currentPageSpan.textContent = label;
          return;
        }
      }
    }

    updateCurrentPage();

    // update auth area (show user name + logout) if user is logged in
    async function updateAuthActions() {
      if (!authEl) return;
      try {
        // Try fetching user data from API
        const response = await fetch(`${API_BASE}/nguoi_dung.php`, { 
          credentials: 'include' 
        });
        
        if (response.ok) {
          const data = await response.json();
          if (data.success && data.data) {
            // User is logged in - show name + logout
            authEl.innerHTML = '';
            const nameSpan = document.createElement('span');
            nameSpan.className = 'user-name';
            nameSpan.textContent = data.data.ho_ten || data.data.email || 'Tài khoản';

            const logoutLink = document.createElement('a');
            logoutLink.href = '#';
            logoutLink.id = 'logout-btn';
            logoutLink.textContent = 'Đăng xuất';
            logoutLink.addEventListener('click', (ev) => {
              ev.preventDefault();
              // Call logout API
              fetch(`${API_BASE}/dang_xuat.php`, { 
                method: 'POST',
                credentials: 'include' 
              }).then(() => {
                window.location.href = '../../trang/trang_chu/trang_chu.html';
              });
            });

            authEl.appendChild(nameSpan);
            authEl.appendChild(logoutLink);
            return;
          }
        }
      } catch (err) {
        console.error('Auth check failed:', err);
      }
      
      // Not logged in - show login link
      authEl.innerHTML = '<a href="../../trang/dang_nhap/dang_nhap.html">Đăng nhập</a>';
    }

    updateAuthActions();

    // Listen for cart/wishlist updates and update badges in real-time
    function updateCartBadge() {
      if (cartBadge && typeof GioHang !== 'undefined') {
        cartBadge.textContent = GioHang.dem();
      }
    }
    function updateWishBadge() {
      if (wishBadge && typeof YeuThich !== 'undefined') {
        wishBadge.textContent = YeuThich.dem();
      }
    }
    
    document.addEventListener('cartUpdated', updateCartBadge);
    document.addEventListener('wishlistUpdated', updateWishBadge);

    if (!dropdownBtn || !nav) return;

    function openMenu() {
      nav.classList.add('active');
      dropdownBtn.classList.add('active');
      nav.setAttribute('aria-hidden', 'false');
      dropdownBtn.setAttribute('aria-expanded', 'true');
    }

    function closeMenu() {
      nav.classList.remove('active');
      dropdownBtn.classList.remove('active');
      nav.setAttribute('aria-hidden', 'true');
      dropdownBtn.setAttribute('aria-expanded', 'false');
    }

    function toggleMenu() { if (nav.classList.contains('active')) closeMenu(); else openMenu(); }

    dropdownBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleMenu(); });

    document.addEventListener('click', (e) => {
      if (!nav.contains(e.target) && !dropdownBtn.contains(e.target) && nav.classList.contains('active')) {
        closeMenu();
      }
    });

    nav.addEventListener('click', (e) => {
      if (e.target.tagName === 'A') closeMenu();
    });

    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMenu(); });

    // Prevent auto-close on resize if still on mobile
    let t = null;
    window.addEventListener('resize', () => { 
      clearTimeout(t); 
      t = setTimeout(() => { 
        // Only close menu if we're on desktop (> 768px)
        if (window.innerWidth > 768) closeMenu(); 
      }, 150); 
    });
  }

  // If header is injected dynamically, listen for event
  document.addEventListener('componentLoaded', (e) => { if (e.detail && e.detail.selector === '#header') init(); });
  // Otherwise init immediately
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
