/**
 * tai_component.js - Tai HTML component vao trang
 */
async function taiComponent(selector, duongDan) {
  const container = document.querySelector(selector);
  if (!container) return;

  try {
    const response = await fetch(duongDan);
    if (!response.ok) throw new Error('Khong tai duoc component');
    container.innerHTML = await response.text();
    document.dispatchEvent(new CustomEvent('componentLoaded', { detail: { selector } }));
  } catch (error) {
    console.error('Loi tai component:', duongDan, error);
  }
}
