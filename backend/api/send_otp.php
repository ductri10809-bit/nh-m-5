<?php
/**
 * send_otp.php
 * Gửi OTP tới email TRƯỚC khi tạo tài khoản
 * POST { email, ho_ten }
 * 
 * Dùng Brevo API để gửi email thực
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../helpers/otp_helper_brevo.php';
require_once __DIR__ . '/../helpers/validate.php';
require_once __DIR__ . '/../helpers/email_validator.php';

session_start();

try {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    if (empty($data['email'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email không được để trống']);
        exit;
    }

    if (!validateEmail($data['email'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email không hợp lệ']);
        exit;
    }

    // Kiểm tra DNS domain
    if (!EmailValidator::isValidEmailDomain($data['email'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => '❌ Email domain không tồn tại hoặc không hợp lệ. Vui lòng kiểm tra lại.'
        ]);
        exit;
    }

    $email = $data['email'];
    $hoTen = $data['ho_ten'] ?? 'Bạn';

    // Tạo OTP
    $otpHelper = new OtpHelperBrevo();
    $code = $otpHelper->generateOtp();

    // Lưu OTP vào session (chưa tạo tài khoản)
    $_SESSION['otp_temp'] = [
        'code' => $code,
        'email' => $email,
        'ho_ten' => $hoTen,
        'expires_at' => time() + 600, // 10 phút
        'attempts' => 0
    ];

    // Gửi email qua Brevo API
    $emailSent = $otpHelper->guiOtpDenEmail($email, $hoTen, $code);

    if ($emailSent) {
        echo json_encode([
            'success' => true,
            'message' => "✅ OTP đã gửi về email: {$email}\n📧 Vui lòng kiểm tra thư mục inbox hoặc spam",
            'data' => ['email' => $email]
        ]);
    } else {
        // Nếu gửi thất bại, vẫn lưu OTP để test
        echo json_encode([
            'success' => true,
            'message' => "⚠️ Lỗi gửi email, nhưng OTP đã lưu vào hệ thống.\n💡 Mã OTP: {$code} (Test)",
            'data' => ['email' => $email, 'otp_test' => $code]
        ]);
    }

} catch (Exception $e) {
    error_log('send_otp error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
}
