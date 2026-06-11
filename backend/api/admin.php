<?php
/**
 * API: admin.php
 */
require_once __DIR__ . '/../cau_hinh/session.php';
require_once __DIR__ . '/../helpers/phan_hoi_json.php';
require_once __DIR__ . '/../middleware/quan_tri.php';
require_once __DIR__ . '/../model/san_pham.php';
require_once __DIR__ . '/../model/danh_muc.php';
require_once __DIR__ . '/../model/nguoi_dung.php';
require_once __DIR__ . '/../model/don_hang.php';
require_once __DIR__ . '/../model/bai_viet.php';

yeuCauAdmin();

$action = $_GET['action'] ?? 'summary';
$method = $_SERVER['REQUEST_METHOD'];
$payload = json_decode(file_get_contents('php://input'), true) ?: [];

$sanPham = new SanPham();
$danhMuc = new DanhMuc();
$nguoiDung = new NguoiDung();
$donHang = new DonHang();
$baiViet = new BaiViet();

switch ($method) {
    case 'OPTIONS':
        traVeJson(true);
        break;
    case 'GET':
        switch ($action) {
            case 'summary':
                traVeJson(true, [
                    'products' => count($sanPham->layTatCa()),
                    'categories' => count($danhMuc->layTatCa()),
                    'users' => count($nguoiDung->layTatCa()),
                    'orders' => count($donHang->layTatCa()),
                    'posts' => count($baiViet->layTatCa()),
                ]);
                break;
            case 'products':
                traVeJson(true, $sanPham->layTatCa());
                break;
            case 'categories':
                traVeJson(true, $danhMuc->layTatCa());
                break;
            case 'users':
                traVeJson(true, $nguoiDung->layTatCa());
                break;
            case 'orders':
                traVeJson(true, $donHang->layTatCa());
                break;
            case 'posts':
                traVeJson(true, $baiViet->layTatCa());
                break;
            default:
                traVeJson(false, null, 'Action không hợp lệ', 400);
        }
        break;
    case 'POST':
        switch ($action) {
            case 'product':
                $id = $sanPham->tao($payload);
                traVeJson(true, ['id' => $id], 'Sản phẩm đã được thêm');
                break;
            case 'category':
                $id = $danhMuc->tao($payload);
                traVeJson(true, ['id' => $id], 'Danh mục đã được thêm');
                break;
            case 'user':
                $ok = $nguoiDung->capNhat((int)($payload['id'] ?? 0), $payload);
                traVeJson($ok, null, $ok ? 'Người dùng đã được cập nhật' : 'Cập nhật người dùng thất bại');
                break;
            case 'order_status':
                if (empty($payload['id']) || empty($payload['status'])) {
                    traVeJson(false, null, 'Thiếu id hoặc trạng thái', 400);
                }
                $ok = $donHang->capNhatTrangThai((int)$payload['id'], $payload['status']);
                traVeJson($ok, null, $ok ? 'Đã cập nhật trạng thái đơn hàng' : 'Cập nhật thất bại');
                break;
            case 'post':
                $ok = $baiViet->them($payload);
                traVeJson($ok, null, $ok ? 'Bài viết đã được thêm' : 'Tạo bài viết thất bại');
                break;
            default:
                traVeJson(false, null, 'Action không hợp lệ', 400);
        }
        break;
    case 'PUT':
        switch ($action) {
            case 'product':
                $ok = $sanPham->capNhat((int)($payload['id'] ?? 0), $payload);
                traVeJson($ok, null, $ok ? 'Sản phẩm đã được cập nhật' : 'Cập nhật sản phẩm thất bại');
                break;
            case 'category':
                $ok = $danhMuc->capNhat((int)($payload['id'] ?? 0), $payload);
                traVeJson($ok, null, $ok ? 'Danh mục đã được cập nhật' : 'Cập nhật danh mục thất bại');
                break;
            case 'user':
                $ok = $nguoiDung->capNhat((int)($payload['id'] ?? 0), $payload);
                traVeJson($ok, null, $ok ? 'Người dùng đã được cập nhật' : 'Cập nhật người dùng thất bại');
                break;
            case 'post':
                $ok = $baiViet->capNhat($payload['id'] ?? null, $payload);
                traVeJson($ok, null, $ok ? 'Bài viết đã được cập nhật' : 'Cập nhật bài viết thất bại');
                break;
            default:
                traVeJson(false, null, 'Action không hợp lệ', 400);
        }
        break;
    case 'DELETE':
        switch ($action) {
            case 'product':
                $ok = $sanPham->xoa((int)($_GET['id'] ?? 0));
                traVeJson($ok, null, $ok ? 'Sản phẩm đã được xóa' : 'Xóa sản phẩm thất bại');
                break;
            case 'category':
                $ok = $danhMuc->xoa((int)($_GET['id'] ?? 0));
                traVeJson($ok, null, $ok ? 'Danh mục đã được xóa' : 'Xóa danh mục thất bại');
                break;
            case 'user':
                $ok = $nguoiDung->xoa((int)($_GET['id'] ?? 0));
                traVeJson($ok, null, $ok ? 'Người dùng đã được xóa' : 'Xóa người dùng thất bại');
                break;
            case 'post':
                $ok = $baiViet->xoa($_GET['id'] ?? null);
                traVeJson($ok, null, $ok ? 'Bài viết đã được xóa' : 'Xóa bài viết thất bại');
                break;
            default:
                traVeJson(false, null, 'Action không hợp lệ', 400);
        }
        break;
    default:
        traVeJson(false, null, 'Method not allowed', 405);
}
