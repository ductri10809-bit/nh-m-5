/**
 * lien_he.js
 */
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('form-lien-he');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(form).entries());

    try {
      const result = await Chung.goiApi('lien_he.php', {
        method: 'POST',
        body: JSON.stringify(data)
      });

      if (result.success) {
        const successMsg = document.getElementById('successMessage');
        if (successMsg) {
          document.getElementById('trackingId').textContent = 
            result.data?.tracking_id || '#' + result.data?.id;
          successMsg.style.display = 'block';
        }
        form.reset();
        console.log('Contact form submitted:', result.data);
      } else {
        alert('Gửi thất bại: ' + (result.message || 'Lỗi không xác định'));
      }
    } catch (error) {
      alert('Lỗi kết nối: ' + error.message);
      console.error('Contact form error:', error);
    }
  });
});
