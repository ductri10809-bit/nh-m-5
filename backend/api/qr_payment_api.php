<?php
/**
 * qr_payment_api.php - API endpoints cho QR payment
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../model/qr_payment.php';
require_once __DIR__ . '/../model/don_hang.php';
require_once __DIR__ . '/../helpers/qr_helper.php';
require_once __DIR__ . '/../helpers/email_helper.php';
require_once __DIR__ . '/../cau_hinh/hang_so.php';

$action = $_GET['action'] ?? '';

$qrPaymentModel = new QRPayment();
$donHangModel = new DonHang();

try {
    switch ($action) {
        case 'create':
            // Tạo QR code mới
            thaoTacTao();
            break;

        case 'get':
            // Lấy thông tin QR code
            thaoTacLay();
            break;

        case 'approve':
            // Duyệt thanh toán (admin)
            thaoTacDuyet();
            break;

        case 'reject':
            // Từ chối thanh toán
            thaoTacTuChoi();
            break;

        case 'list-pending':
            // Lấy danh sách chờ duyệt
            thaoTacDanhSachCho();
            break;

        case 'list-approved':
            // Lấy danh sách đã duyệt (THÊM)
            thaoTacDanhSachDaDuyet();
            break;

        case 'auto-create':
            // Tự động tạo QR record khi khách đặt hàng với payment method là qr_code/bank_transfer
            thaoTacTaoTuDong();
            break;

        default:
            traVeJson(false, null, 'Action không hợp lệ', 400);
    }
} catch (Exception $e) {
    traVeJson(false, null, $e->getMessage(), 500);
}

/**
 * Tạo QR code mới cho đơn hàng
 */
function thaoTacTao()
{
    global $qrPaymentModel, $donHangModel;

    $orderId = (int)($_POST['order_id'] ?? 0);
    $customerEmail = $_POST['customer_email'] ?? '';

    if (!$orderId || !$customerEmail) {
        traVeJson(false, null, 'Thiếu thông tin order_id hoặc customer_email', 400);
    }

    // Kiểm tra đơn hàng tồn tại
    $order = $donHangModel->timTheoId($orderId);
    if (!$order) {
        traVeJson(false, null, 'Không tìm thấy đơn hàng', 404);
    }

    // Tạo dữ liệu QR
    $dataQR = QRHelper::taoDataQR([
        'admin_email' => ADMIN_EMAIL,
        'customer_email' => $customerEmail,
        'order_id' => $orderId,
        'amount' => $order['total_amount'],
    ]);

    // Generate QR code (base64)
    $qrImage = QRHelper::taoQRCode($dataQR, true);

    // Lưu QR payment
    $qrPaymentId = $qrPaymentModel->tao([
        'order_id' => $orderId,
        'qr_code_data' => json_encode($dataQR),
        'admin_email' => ADMIN_EMAIL,
        'customer_email' => $customerEmail,
        'amount' => $order['total_amount'],
        'transaction_status' => 'pending',
    ]);

    // Lưu đường dẫn ảnh QR
    $qrImagePath = QRHelper::taoQRCode($dataQR, false);
    $qrPaymentModel->capNhatQRImage($qrPaymentId, $qrImagePath);

    traVeJson(true, [
        'qr_payment_id' => $qrPaymentId,
        'qr_image' => $qrImage,
        'qr_image_url' => $qrImagePath,
    ], 'Tạo QR code thành công');
}

/**
 * Lấy thông tin QR code
 */
function thaoTacLay()
{
    global $qrPaymentModel;

    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        traVeJson(false, null, 'Thiếu ID', 400);
    }

    $qrPayment = $qrPaymentModel->layTheoId($id);
    if (!$qrPayment) {
        traVeJson(false, null, 'Không tìm thấy QR payment', 404);
    }

    traVeJson(true, $qrPayment, 'Lấy thông tin thành công');
}

/**
 * Duyệt thanh toán (admin approve)
 */
function thaoTacDuyet()
{
    global $qrPaymentModel, $donHangModel;

    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        traVeJson(false, null, 'Thiếu ID', 400);
    }

    $qrPayment = $qrPaymentModel->layTheoId($id);
    if (!$qrPayment) {
        traVeJson(false, null, 'Không tìm thấy QR payment', 404);
    }

    if ($qrPayment['transaction_status'] !== 'pending') {
        traVeJson(false, null, 'Thanh toán không ở trạng thái chờ duyệt', 400);
    }

    // Cập nhật trạng thái
    $qrPaymentModel->xacNhanThanhToan($id);

    // Cập nhật trạng thái đơn hàng
    $donHangModel->capNhatTrangThai($qrPayment['order_id'], 'da_thanh_toan');

    // Lấy thông tin đơn hàng
    $order = $donHangModel->timTheoId($qrPayment['order_id']);

    // Gửi email thông báo cho khách hàng
    try {
        $emailHelper = new EmailHelper();
        $emailHelper->guiThongBaoKhachThanhToanThanhCong($qrPayment, $order);
        $qrPaymentModel->danhDauDaThongBao($id);
    } catch (Exception $e) {
        error_log('Lỗi gửi email: ' . $e->getMessage());
    }

    traVeJson(true, null, 'Duyệt thanh toán thành công. Đã gửi email xác nhận cho khách.');
}

/**
 * Từ chối thanh toán
 */
function thaoTacTuChoi()
{
    global $qrPaymentModel;

    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        traVeJson(false, null, 'Thiếu ID', 400);
    }

    $qrPayment = $qrPaymentModel->layTheoId($id);
    if (!$qrPayment) {
        traVeJson(false, null, 'Không tìm thấy QR payment', 404);
    }

    // Cập nhật trạng thái
    $qrPaymentModel->capNhatTrangThai($id, 'rejected');

    traVeJson(true, null, 'Đã từ chối thanh toán');
}

/**
 * Lấy danh sách QR payment chờ duyệt
 */
function thaoTacDanhSachCho()
{
    global $qrPaymentModel;

    $page = (int)($_GET['page'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 20);
    $offset = ($page - 1) * $limit;

    $danhSach = $qrPaymentModel->layDanhSachCho($limit, $offset);
    $soLuongCho = $qrPaymentModel->demSoCho();

    traVeJson(true, [
        'list' => $danhSach,
        'total' => $soLuongCho,
        'page' => $page,
        'limit' => $limit,
    ], 'Lấy danh sách chờ duyệt thành công');
}

/**
 * Lấy danh sách QR payment đã duyệt (THÊM MỚI)
 */
function thaoTacDanhSachDaDuyet()
{
    global $qrPaymentModel;

    $page = (int)($_GET['page'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 20);
    $offset = ($page - 1) * $limit;

    $danhSach = $qrPaymentModel->layDanhSachDaDuyet($limit, $offset);
    
    traVeJson(true, [
        'list' => $danhSach,
        'page' => $page,
        'limit' => $limit,
    ], 'Lấy danh sách đã duyệt thành công');
}

/**
 * Tự động tạo QR payment record khi khách đặt hàng
 */
function thaoTacTaoTuDong()
{
    global $qrPaymentModel, $donHangModel;

    // Accept both GET and POST
    $orderId = (int)($_REQUEST['order_id'] ?? 0);
    $customerEmail = $_REQUEST['customer_email'] ?? '';

    if (!$orderId || !$customerEmail) {
        traVeJson(false, null, 'Thiếu thông tin order_id hoặc customer_email', 400);
        return;
    }

    // Kiểm tra đơn hàng tồn tại
    $order = $donHangModel->timTheoId($orderId);
    if (!$order) {
        traVeJson(false, null, 'Không tìm thấy đơn hàng', 404);
        return;
    }

    // Kiểm tra xem đã tạo QR payment cho order này chưa
    $existingQR = $qrPaymentModel->layTheoOrder($orderId);
    if ($existingQR) {
        traVeJson(true, [
            'qr_payment_id' => $existingQR['id'],
            'message' => 'QR payment đã tồn tại cho đơn hàng này'
        ], 'QR payment đã tồn tại');
        return;
    }

    // Tạo dữ liệu QR
    $dataQR = QRHelper::taoDataQR([
        'admin_email' => ADMIN_EMAIL,
        'customer_email' => $customerEmail,
        'order_id' => $orderId,
        'amount' => $order['total_amount'],
    ]);

    // Generate QR code (base64)
    $qrImage = QRHelper::taoQRCode($dataQR, true);

    // Lưu QR payment
    $qrPaymentId = $qrPaymentModel->tao([
        'order_id' => $orderId,
        'qr_code_data' => json_encode($dataQR),
        'admin_email' => ADMIN_EMAIL,
        'customer_email' => $customerEmail,
        'amount' => $order['total_amount'],
        'transaction_status' => 'pending',
    ]);

    // Lưu đường dẫn ảnh QR
    $qrImagePath = QRHelper::taoQRCode($dataQR, false);
    $qrPaymentModel->capNhatQRImage($qrPaymentId, $qrImagePath);

    traVeJson(true, [
        'qr_payment_id' => $qrPaymentId,
        'qr_image' => $qrImage,
        'qr_image_url' => $qrImagePath,
    ], 'Tạo QR code thành công');
}

/**
 * Trả về JSON response
 */
function traVeJson(bool $success, mixed $data = null, string $message = '', int $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s'),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
