<?php
/**
 * API: don_hang.php
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../middleware/xac_thuc.php';
require_once __DIR__ . '/../controller/don_hang_controller.php';
require_once __DIR__ . '/../model/nguoi_dung.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    traVeJson(true);
}

yeuCauDangNhap();
$controller = new DonHangController();
$userId = layUserId();

// Get user info to check role
$userModel = new NguoiDung();
$user = $userModel->timTheoId($userId);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (!empty($_GET['id'])) {
            $result = $controller->chiTietDon((int) $_GET['id']);
        } else {
            $result = $controller->layDonCuaUser($userId);
        }
        break;
    case 'POST':
        $result = $controller->taoDon($userId, layDuLieuJson());
        break;
    case 'PUT':
        $data = layDuLieuJson();
        $orderId = $data['order_id'] ?? null;
        if (!$orderId) {
            $result = ['success' => false, 'message' => 'Thiếu order_id'];
            break;
        }
        
        // Only admin can update order status
        if ($user['role'] !== 'admin') {
            traVeJson(false, null, 'Chỉ quản trị viên mới có thể cập nhật trạng thái đơn hàng', 403);
        }
        
        if (!empty($data['order_status'])) {
            $result = $controller->capNhatTrangThai((int) $orderId, $data['order_status']);
        } else {
            $result = ['success' => false, 'message' => 'Không có dữ liệu cập nhật'];
        }
        break;
    case 'DELETE':
        $data = layDuLieuJson();
        $orderId = $data['order_id'] ?? null;
        if (!$orderId) {
            $result = ['success' => false, 'message' => 'Thiếu order_id'];
            break;
        }
        $result = $controller->xoaDon((int) $orderId);
        break;
    default:
        traVeJson(false, null, 'Method not allowed', 405);
}

traVeJson($result['success'], $result['data'] ?? null, $result['message'] ?? '', $result['success'] ? 200 : 400);


