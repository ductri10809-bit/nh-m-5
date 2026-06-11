<?php
/**
 * don_hang_controller.php
 */
require_once __DIR__ . '/../model/don_hang.php';
require_once __DIR__ . '/../model/chi_tiet_don_hang.php';

class DonHangController
{
    private DonHang $model;
    private ChiTietDonHang $chiTietModel;

    public function __construct()
    {
        $this->model = new DonHang();
        $this->chiTietModel = new ChiTietDonHang();
    }

    public function taoDon(?int $userId, array $data): array
    {
        $items = $data['items'] ?? [];
        if (empty($items)) {
            return ['success' => false, 'message' => 'Giỏ hàng trống'];
        }

        $hoTen = trim($data['ho_ten'] ?? $data['customer_name'] ?? '');
        $sdt = trim($data['sdt'] ?? $data['phone'] ?? '');
        $diaChi = trim($data['dia_chi'] ?? $data['address'] ?? '');

        if ($hoTen === '' || $sdt === '' || $diaChi === '') {
            return ['success' => false, 'message' => 'Vui lòng điền đầy đủ họ tên, số điện thoại và địa chỉ giao hàng'];
        }

        $tongTien = 0;
        foreach ($items as $item) {
            $tongTien += ((int) ($item['gia'] ?? 0)) * ((int) ($item['so_luong'] ?? 1));
        }

        $orderId = $this->model->tao([
            'user_id'        => $userId,
            'customer_name'  => $hoTen,
            'customer_email' => $data['email'] ?? $data['customer_email'] ?? null,
            'tong_tien'      => $tongTien,
            'dia_chi'        => $diaChi,
            'phone'          => $sdt,
        ]);

        foreach ($items as $item) {
            $this->chiTietModel->tao([
                'order_id'   => $orderId,
                'product_id' => (int) ($item['product_id'] ?? $item['id']),
                'so_luong'   => (int) ($item['so_luong'] ?? 1),
                'gia'        => (int) ($item['gia'] ?? 0),
            ]);
        }

        return [
            'success' => true,
            'message' => 'Đặt hàng thành công',
            'data'    => ['order_id' => $orderId, 'tong_tien' => $tongTien],
        ];
    }

    public function layDonCuaUser(int $userId): array
    {
        return ['success' => true, 'data' => $this->model->layTheoUser($userId)];
    }

    public function chiTietDon(int $orderId): array
    {
        $don = $this->model->timTheoId($orderId);
        if (!$don) {
            return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
        }

        $chiTiet = $this->chiTietModel->layTheoDonHang($orderId);
        
        return [
            'success' => true,
            'data' => [
                'order' => $don,
                'chi_tiet' => $chiTiet
            ]
        ];
    }

    public function capNhatTrangThai(int $orderId, string $status): array
    {
        $don = $this->model->timTheoId($orderId);
        if (!$don) {
            return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
        }

        $ok = $this->model->capNhatTrangThai($orderId, $status);
        return $ok
            ? ['success' => true, 'message' => 'Cập nhật trạng thái thành công']
            : ['success' => false, 'message' => 'Cập nhật trạng thái thất bại'];
    }

    public function xoaDon(int $orderId): array
    {
        $don = $this->model->timTheoId($orderId);
        if (!$don) {
            return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
        }

        // Kiểm tra trạng thái - chỉ cho xoá nếu đơn chưa được duyệt hoặc đã hủy
        if ($don['order_status'] !== 'cho_xu_ly' && $don['order_status'] !== 'da_huy') {
            return ['success' => false, 'message' => 'Chỉ có thể xoá đơn ở trạng thái chờ xử lý hoặc đã hủy'];
        }

        // Xoá chi tiết đơn hàng trước
        $this->chiTietModel->xoaTheoDonHang($orderId);
        
        // Xoá đơn hàng
        $ok = $this->model->xoa($orderId);

        return $ok
            ? ['success' => true, 'message' => 'Xoá đơn hàng thành công']
            : ['success' => false, 'message' => 'Xoá đơn hàng thất bại'];
    }
}
