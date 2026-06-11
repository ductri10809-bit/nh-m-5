<?php
/**
 * verify_otp_then_register.php
 * Xác thực OTP, rồi tạo tài khoản
 * POST { otp, password, confirm_password, sdt }
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../model/nguoi_dung.php';
require_once __DIR__ . '/../helpers/bao_mat.php';
require_once __DIR__ . '/../helpers/validate.php';

session_start();

try {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    // Kiểm tra OTP session
    if (empty($_SESSION['otp_temp'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Session OTP không hợp lệ. Vui lòng gửi OTP lại.']);
        exit;
    }

    $otpData = $_SESSION['otp_temp'];

    // Kiểm tra hết hạn
    if (time() > $otpData['expires_at']) {
        unset($_SESSION['otp_temp']);
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Mã OTP đã hết hạn. Vui lòng gửi OTP mới.']);
        exit;
    }

    // Kiểm tra OTP
    if (empty($data['otp']) || $data['otp'] !== $otpData['code']) {
        $_SESSION['otp_temp']['attempts'] = ($otpData['attempts'] ?? 0) + 1;
        
        if ($_SESSION['otp_temp']['attempts'] >= 5) {
            unset($_SESSION['otp_temp']);
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Vượt quá số lần thử. Vui lòng gửi OTP mới.']);
            exit;
        }

        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Mã OTP không chính xác']);
        exit;
    }

    // Kiểm tra password
    if (empty($data['password']) || empty($data['confirm_password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mật khẩu']);
        exit;
    }

    if ($data['password'] !== $data['confirm_password']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Mật khẩu xác nhận không khớp']);
        exit;
    }

    if (strlen($data['password']) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự']);
        exit;
    }

    // TẠO TÀI KHOẢN dùng Model (sẽ tự generate username)
    $model = new NguoiDung();
    
    // Kiểm tra email đã tồn tại
    if ($model->timTheoEmail($otpData['email'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email đã được đăng ký']);
        exit;
    }

    $userId = $model->tao([
        'ho_ten' => $otpData['ho_ten'],
        'email' => $otpData['email'],
        'password' => hashMatKhau($data['password']),
        'sdt' => $data['sdt'] ?? '',
        'role' => 'customer',
        'is_verified' => 1  // Email đã xác thực qua OTP
    ]);

    if (!$userId) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Tạo tài khoản thất bại']);
        exit;
    }

    // Xóa OTP session
    unset($_SESSION['otp_temp']);

    echo json_encode([
        'success' => true,
        'message' => 'Tài khoản được tạo thành công! Vui lòng đăng nhập.',
        'data' => ['user_id' => $userId]
    ]);

} catch (Exception $e) {
    error_log('verify_otp_then_register error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' =>'error'. $e->getMessage()]);
}
