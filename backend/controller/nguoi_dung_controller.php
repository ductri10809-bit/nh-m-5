<?php
/**
 * nguoi_dung_controller.php
 */
require_once __DIR__ . '/../model/nguoi_dung.php';
require_once __DIR__ . '/../helpers/bao_mat.php';
require_once __DIR__ . '/../helpers/validate.php';
require_once __DIR__ . '/../helpers/otp_helper.php';
require_once __DIR__ . '/../cau_hinh/session.php';

class NguoiDungController
{
    private NguoiDung $model;
    private OtpHelper $otpHelper;

    public function __construct()
    {
        $this->model = new NguoiDung();
        $this->otpHelper = new OtpHelper();
    }

    public function dangKy(array $data): array
    {
        $loi = validateRequired($data, ['ho_ten', 'email', 'password']);
        if ($loi) return ['success' => false, 'message' => $loi];

        if (!validateEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email khong hop le'];
        }

        if ($this->model->timTheoEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email da ton tai'];
        }

        $id = $this->model->tao([
            'ho_ten'   => sanitize($data['ho_ten']),
            'email'    => sanitize($data['email']),
            'password' => hashMatKhau($data['password']),
            'sdt'      => sanitize($data['sdt'] ?? ''),
            'role'     => $data['role'] ?? 'customer',
        ]);

        // Generate OTP and save
        $otp = $this->otpHelper->generateOtp();
        $this->model->saveOtp($id, $otp);

        // Send OTP email
        $hoTen = sanitize($data['ho_ten']);
        $email = sanitize($data['email']);
        $emailSent = $this->otpHelper->guiOtpDenEmail($email, $hoTen, $otp);

        return [
            'success' => true,
            'message' => 'Đăng ký thành công!',
            'data' => [
                'id' => $id,
                'email' => $email,
                'requires_otp' => true
            ]
        ];
    }

    public function verifyOtp(string $otp): array
    {
        if (empty($otp) || strlen($otp) !== 6) {
            return ['success' => false, 'message' => 'Mã OTP không hợp lệ'];
        }

        $verified = $this->model->verifyOtp($otp);
        if (!$verified) {
            return ['success' => false, 'message' => 'Mã OTP không chính xác hoặc đã hết hạn'];
        }

        return ['success' => true, 'message' => 'Xác thực thành công! Bạn có thể đăng nhập ngay.'];
    }

    public function dangNhap(array $data): array
    {
        $loi = validateRequired($data, ['email', 'password']);
        if ($loi) return ['success' => false, 'message' => $loi];

        $user = $this->model->timTheoEmail($data['email']);
        if (!$user || !kiemTraMatKhau($data['password'], $user['password'])) {
            return ['success' => false, 'message' => 'Email hoac mat khau khong dung'];
        }

        datUserId((int) $user['id']);
        return [
            'success' => true,
            'message' => 'Dang nhap thanh cong',
            'data' => [
                'id' => $user['id'],
                'ho_ten' => $user['ho_ten'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ];
    }

    public function layHoSo(int $userId): array
    {
        $user = $this->model->timTheoId($userId);
        if (!$user) return ['success' => false, 'message' => 'Khong tim thay nguoi dung'];
        return ['success' => true, 'data' => $user];
    }

    public function capNhatHoSo(int $userId, array $data): array
    {
        $loi = validateRequired($data, ['ho_ten', 'email']);
        if ($loi) return ['success' => false, 'message' => $loi];

        $ok = $this->model->capNhat($userId, [
            'ho_ten'  => sanitize($data['ho_ten']),
            'email'   => sanitize($data['email']),
            'sdt'     => sanitize($data['sdt'] ?? ''),
            'dia_chi' => sanitize($data['dia_chi'] ?? ''),
        ]);

        return $ok
            ? ['success' => true, 'message' => 'Cap nhat thanh cong']
            : ['success' => false, 'message' => 'Cap nhat that bai'];
    }
}
