<?php
/**
 * lien_he_controller.php
 */
require_once __DIR__ . '/../model/phan_hoi.php';
require_once __DIR__ . '/../helpers/validate.php';
require_once __DIR__ . '/../helpers/bao_mat.php';
require_once __DIR__ . '/../cau_hinh/session.php';

class LienHeController
{
    private PhanHoi $model;

    public function __construct()
    {
        $this->model = new PhanHoi();
    }

    public function gui(array $data): array
    {
        $loi = validateRequired($data, ['ho_ten', 'email', 'noi_dung']);
        if ($loi) return ['success' => false, 'message' => $loi];

        if (!validateEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }

        $userId = layUserId();

        $id = $this->model->tao([
            'fullname' => sanitize($data['ho_ten']),
            'email' => sanitize($data['email']),
            'message' => sanitize($data['noi_dung']),
            'user_id' => $userId,
        ]);

        return [
            'success' => true,
            'message' => 'Gửi liên hệ thành công',
            'data' => ['id' => $id, 'tracking_id' => '#LH' . str_pad($id, 6, '0', STR_PAD_LEFT)]
        ];
    }

    public function getDanhSach(array $filters = []): array
    {
        try {
            $data = $this->model->getDanhSach($filters);
            return ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getById(int $id): array
    {
        try {
            $data = $this->model->getById($id);
            if (!$data) {
                return ['success' => false, 'message' => 'Không tìm thấy liên hệ'];
            }
            return ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateStatus(int $id, string $status): array
    {
        $validStatuses = ['pending', 'received', 'processing', 'resolved', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Trạng thái không hợp lệ'];
        }

        try {
            $updated = $this->model->updateStatus($id, $status);
            return [
                'success' => $updated,
                'message' => $updated ? 'Cập nhật thành công' : 'Cập nhật thất bại'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getLichSuUser(int $userId): array
    {
        try {
            $data = $this->model->getLichSuUser($userId);
            return ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
