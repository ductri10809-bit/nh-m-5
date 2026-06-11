<?php
/**
 * thanh_toan_controller.php
 */
require_once __DIR__ . '/../model/thanh_toan.php';
require_once __DIR__ . '/../model/don_hang.php';

class ThanhToanController
{
    private ThanhToan $model;
    private DonHang $donHangModel;

    public function __construct()
    {
        $this->model = new ThanhToan();
        $this->donHangModel = new DonHang();
    }

    public function xuLy(int $orderId, array $data): array
    {
        $don = $this->donHangModel->timTheoId($orderId);
        if (!$don) return ['success' => false, 'message' => 'Khong tim thay don hang'];

        $paymentId = $this->model->tao([
            'order_id'     => $orderId,
            'phuong_thuc'  => $data['phuong_thuc'] ?? 'cod',
            'so_tien'      => $don['tong_tien'],
            'trang_thai'   => 'cho_xu_ly',
        ]);

        return ['success' => true, 'message' => 'Thanh toan thanh cong', 'data' => ['payment_id' => $paymentId]];
    }
}
