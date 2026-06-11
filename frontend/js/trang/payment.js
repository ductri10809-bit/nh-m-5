/**
 * Payment methods handler
 */
document.addEventListener('DOMContentLoaded', () => {
  const paymentOptions = document.querySelectorAll('.payment-option input[name="payment_method"]');
  const bankOptions = document.querySelector('.bank-options');
  const bankRadios = document.querySelectorAll('input[name="bank_name"]');

  paymentOptions.forEach(option => {
    option.addEventListener('change', () => {
      const isBank = option.value === 'bank_transfer';
      if (bankOptions) {
        bankOptions.hidden = !isBank;
      }

      // Uncheck bank radios if not bank transfer
      if (!isBank) {
        bankRadios.forEach(radio => radio.checked = false);
      } else if (bankRadios.length > 0 && !Array.from(bankRadios).some(r => r.checked)) {
        bankRadios[0].checked = true;
      }
    });
  });

  // Bank card selection
  const bankItems = document.querySelectorAll('.bank-item');
  bankItems.forEach(item => {
    item.addEventListener('click', () => {
      const radio = item.querySelector('input[type="radio"]');
      radio.checked = true;
    });
  });

  // Auto-check first bank if bank transfer is selected on load
  const selectedPaymentMethod = Array.from(paymentOptions).find(o => o.checked)?.value;
  if (selectedPaymentMethod === 'bank_transfer' && bankRadios.length > 0 && !Array.from(bankRadios).some(r => r.checked)) {
    bankRadios[0].checked = true;
  }
});
