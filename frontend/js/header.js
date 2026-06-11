/* header.js - compatibility loader
   If a page includes `js/header.js` (legacy path), this script will dynamically
   load the real implementation at `js/thanh_phan/header.js` relative to itself.
*/
(function() {
  try {
    const current = document.currentScript;
    // Fallback base path: assume current folder is /frontend/js/
    let base = (current && current.src) ? current.src.replace(/header\.js(\?.*)?$/, '') : window.location.origin + '/luxurious-fashion-store/frontend/js/';

    // Normalize: ensure trailing slash
    if (!base.endsWith('/')) base += '/';

    const target = base + 'thanh_phan/header.js';

    // If the target script is already present, do nothing
    const already = Array.from(document.scripts).some(s => s.src === target || s.getAttribute('data-header-proxy') === 'true');
    if (already) return;

    const s = document.createElement('script');
    s.src = target;
    s.defer = true;
    // mark so we can detect it later
    s.setAttribute('data-header-proxy', 'true');
    s.addEventListener('error', function() {
      // If loading fails, try one level up (common when pages are in subfolders)
      const alt = base.replace(/\/js\/?$/, '/') + 'js/thanh_phan/header.js';
      if (alt !== target) {
        const s2 = document.createElement('script');
        s2.src = alt;
        s2.defer = true;
        s2.setAttribute('data-header-proxy', 'true');
        document.head.appendChild(s2);
      }
    });
    document.head.appendChild(s);
  } catch (err) {
    console.error('header loader error', err);
  }
})();
