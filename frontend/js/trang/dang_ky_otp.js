/**
 * dang_ky_otp.js - Hợp nhất: Khách hàng (OTP) + Admin (trực tiếp)
 */

let otpTimer;
let otpExpires = 0;
let currentEmail = '';

document.addEventListener('DOMContentLoaded', () => {
    // ==================== TAB SWITCHING ====================
    const tabButtons = document.querySelectorAll('.tab-btn');
    const customerFlow = document.getElementById('customer-flow');
    const adminFlow = document.getElementById('admin-flow');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tab;
            
            // Update tab buttons
            tabButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Update flows
            if (tab === 'customer') {
                customerFlow.style.display = 'block';
                adminFlow.style.display = 'none';
            } else {
                customerFlow.style.display = 'none';
                adminFlow.style.display = 'block';
            }
        });
    });

    // ==================== CUSTOMER FLOW ====================
    const formSendOtp = document.getElementById('form-send-otp');
    const formVerifyOtp = document.getElementById('form-verify-otp');

    // BƯỚC 1: Gửi OTP
    if (formSendOtp) {
        formSendOtp.addEventListener('submit', async (e) => {
            e.preventDefault();
            const hoTen = document.querySelector('#form-send-otp input[name="ho_ten"]').value.trim();
            const email = document.querySelector('#form-send-otp input[name="email"]').value.trim();

            if (!hoTen || !email) {
                alert('Vui lòng nhập đầy đủ thông tin');
                return;
            }

            const btn = formSendOtp.querySelector('.btn-submit');
            btn.disabled = true;
            btn.textContent = 'Đang gửi...';

            try {
                const result = await Chung.goiApi('send_otp.php', {
                    method: 'POST',
                    body: JSON.stringify({ email, ho_ten: hoTen })
                });

                if (result.success) {
                    currentEmail = email;
                    alert(result.message);
                    moveToStep2(email);
                    startOtpTimer();
                } else {
                    alert(result.message || 'Gửi OTP thất bại');
                }
            } catch (error) {
                alert('Lỗi: ' + (error.message || 'Không thể gửi OTP'));
            } finally {
                btn.disabled = false;
                btn.textContent = 'Gửi mã OTP';
            }
        });
    }

    // BƯỚC 2: Xác thực OTP + tạo mật khẩu
    if (formVerifyOtp) {
        formVerifyOtp.addEventListener('submit', async (e) => {
            e.preventDefault();
            const otp = document.querySelector('#form-verify-otp input[name="otp"]').value.trim();
            const password = document.querySelector('#form-verify-otp input[name="password"]').value;
            const confirmPassword = document.querySelector('#form-verify-otp input[name="confirm_password"]').value;
            const sdt = document.querySelector('#form-verify-otp input[name="sdt"]').value.trim();

            if (otp.length !== 6) {
                alert('Mã OTP phải có 6 chữ số');
                return;
            }

            if (password !== confirmPassword) {
                alert('Mật khẩu xác nhận không khớp');
                return;
            }

            if (password.length < 6) {
                alert('Mật khẩu phải có ít nhất 6 ký tự');
                return;
            }

            const btn = formVerifyOtp.querySelector('.btn-submit');
            btn.disabled = true;
            btn.textContent = 'Đang tạo tài khoản...';

            try {
                const result = await Chung.goiApi('verify_otp_then_register.php', {
                    method: 'POST',
                    body: JSON.stringify({ otp, password, confirm_password: confirmPassword, sdt })
                });

                if (result.success) {
                    alert(result.message);
                    setTimeout(() => {
                        window.location.href = '../dang_nhap/dang_nhap.html';
                    }, 500);
                } else {
                    alert(result.message || 'Xác thực thất bại');
                }
            } catch (error) {
                alert('Lỗi: ' + (error.message || 'Không thể tạo tài khoản'));
            } finally {
                btn.disabled = false;
                btn.textContent = 'Tạo tài khoản';
            }
        });
    }

    // ==================== ADMIN FLOW ====================
    const formAdmin = document.getElementById('form-dang-ky-admin');
    if (formAdmin) {
        formAdmin.addEventListener('submit', async (e) => {
            e.preventDefault();
            const hoTen = document.querySelector('#form-dang-ky-admin input[name="ho_ten"]').value.trim();
            const email = document.querySelector('#form-dang-ky-admin input[name="email"]').value.trim();
            const password = document.querySelector('#form-dang-ky-admin input[name="password"]').value;
            const confirmPassword = document.querySelector('#form-dang-ky-admin input[name="confirm_password"]').value;
            const adminCode = document.querySelector('#form-dang-ky-admin input[name="admin_code"]').value.trim();

            if (!hoTen || !email || !password || !confirmPassword || !adminCode) {
                alert('Vui lòng nhập đầy đủ thông tin bắt buộc');
                return;
            }

            if (password !== confirmPassword) {
                alert('Mật khẩu xác nhận không khớp');
                return;
            }

            if (password.length < 6) {
                alert('Mật khẩu phải có ít nhất 6 ký tự');
                return;
            }

            const btn = formAdmin.querySelector('.btn-submit');
            btn.disabled = true;
            btn.textContent = 'Đang tạo tài khoản...';

            try {
                const result = await Chung.goiApi('dang_ky_admin.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        ho_ten: hoTen,
                        email: email,
                        password: password,
                        confirm_password: confirmPassword,
                        admin_code: adminCode
                    })
                });

                if (result.success) {
                    alert('Đăng ký admin thành công! Vui lòng đăng nhập.');
                    setTimeout(() => {
                        window.location.href = '../dang_nhap/dang_nhap.html';
                    }, 500);
                } else {
                    alert(result.message || 'Đăng ký admin thất bại');
                }
            } catch (error) {
                alert('Lỗi: ' + (error.message || 'Không thể tạo tài khoản admin'));
            } finally {
                btn.disabled = false;
                btn.textContent = 'Đăng ký Admin';
            }
        });
    }
});

function moveToStep2(email) {
    document.getElementById('step-1').classList.remove('active');
    document.getElementById('step-2').classList.add('active');
    document.getElementById('email-display').querySelector('strong').textContent = email;
    document.querySelector('#form-verify-otp input[name="otp"]').focus();
}

function startOtpTimer() {
    otpExpires = Date.now() + 600000; // 10 phút
    updateTimer();
    if (otpTimer) clearInterval(otpTimer);
    otpTimer = setInterval(updateTimer, 1000);
}

function updateTimer() {
    const remaining = Math.max(0, Math.floor((otpExpires - Date.now()) / 1000));
    const mins = Math.floor(remaining / 60);
    const secs = remaining % 60;
    document.getElementById('timer').textContent = `Hết hạn trong ${mins}:${secs < 10 ? '0' : ''}${secs}`;

    if (remaining <= 0) {
        clearInterval(otpTimer);
        document.getElementById('timer').textContent = 'Mã OTP đã hết hạn. Vui lòng gửi lại.';
        document.getElementById('form-verify-otp').style.opacity = '0.5';
        document.querySelector('#form-verify-otp .btn-submit').disabled = true;
    }
}

function backToStep1(e) {
    e.preventDefault();
    if (otpTimer) clearInterval(otpTimer);
    document.getElementById('step-2').classList.remove('active');
    document.getElementById('step-1').classList.add('active');
    document.querySelector('#form-send-otp input[name="email"]').focus();
}

async function resendOtp(e) {
    e.preventDefault();
    const hoTen = document.querySelector('#form-send-otp input[name="ho_ten"]').value.trim();
    
    if (!currentEmail) {
        alert('Vui lòng nhập email trước');
        return;
    }

    if (!hoTen) {
        alert('Vui lòng nhập họ tên');
        return;
    }

    const link = document.querySelector('.resend-otp a');
    link.textContent = 'Đang gửi...';

    try {
        const result = await Chung.goiApi('send_otp.php', {
            method: 'POST',
            body: JSON.stringify({ email: currentEmail, ho_ten: hoTen })
        });

        if (result.success) {
            alert('Mã OTP mới đã gửi về email');
            startOtpTimer();
            document.querySelector('#form-verify-otp input[name="otp"]').value = '';
            document.querySelector('#form-verify-otp input[name="otp"]').focus();
            document.getElementById('form-verify-otp').style.opacity = '1';
            document.querySelector('#form-verify-otp .btn-submit').disabled = false;
        } else {
            alert(result.message || 'Gửi OTP thất bại');
        }
    } catch (error) {
        alert('Lỗi: ' + (error.message || 'Không thể gửi OTP'));
    } finally {
        link.textContent = 'Gửi lại';
    }
}
