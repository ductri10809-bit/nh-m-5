/**
 * modal.js
 */
function moModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.classList.add('active');
}

function dongModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.classList.remove('active');
}

document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.remove('active');
  }
  if (e.target.classList.contains('modal-close')) {
    e.target.closest('.modal-overlay')?.classList.remove('active');
  }
});
