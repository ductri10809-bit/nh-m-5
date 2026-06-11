/**
 * yeu_thich.js - Quan ly danh sach yeu thich (global)
 */
const YeuThich = {
  KEY: 'lux_wishlist',

  lay() {
    return JSON.parse(localStorage.getItem(this.KEY) || '[]');
  },

  luu(items) {
    localStorage.setItem(this.KEY, JSON.stringify(items));
    document.dispatchEvent(new CustomEvent('wishlistUpdated'));
  },

  them(sanPham) {
    const items = this.lay();
    if (!items.find(i => i.id === sanPham.id)) {
      items.push(sanPham);
      this.luu(items);
    }
  },

  xoa(id) {
    this.luu(this.lay().filter(i => i.id !== id));
  },

  co(id) {
    return this.lay().some(i => i.id === id);
  },

  dem() {
    return this.lay().length;
  }
};
