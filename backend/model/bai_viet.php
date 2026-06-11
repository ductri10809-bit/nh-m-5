<?php
/**
 * bai_viet.php - Model quản lý bài viết (tin tức) lưu trong JSON
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';

class BaiViet
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__ . '/../../frontend/tai_nguyen/du_lieu/tin_tuc/danh_sach.json';
    }

    public function layTatCa(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $json = file_get_contents($this->filePath);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    public function timTheoId($id): ?array
    {
        $posts = $this->layTatCa();
        foreach ($posts as $post) {
            if ((string)($post['id'] ?? '') === (string)$id) {
                return $post;
            }
        }
        return null;
    }

    public function luuTatCa(array $posts): bool
    {
        if (!is_writable(dirname($this->filePath))) {
            return false;
        }
        return file_put_contents($this->filePath, json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }

    public function them(array $post): bool
    {
        $posts = $this->layTatCa();
        $post['id'] = time();
        $posts[] = $post;
        return $this->luuTatCa($posts);
    }

    public function capNhat($id, array $post): bool
    {
        $posts = $this->layTatCa();
        foreach ($posts as &$item) {
            if ((string)($item['id'] ?? '') === (string)$id) {
                $item = array_merge($item, $post);
                return $this->luuTatCa($posts);
            }
        }
        return false;
    }

    public function xoa($id): bool
    {
        $posts = $this->layTatCa();
        $filtered = array_filter($posts, function ($item) use ($id) {
            return (string)($item['id'] ?? '') !== (string)$id;
        });
        return $this->luuTatCa(array_values($filtered));
    }
}
