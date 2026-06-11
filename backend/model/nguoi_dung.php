<?php
/**
 * nguoi_dung.php - Model bang users
 */
require_once __DIR__ . '/../cau_hinh/ket_noi_csdl.php';

class NguoiDung
{
    private PDO $db;

    public function __construct()
    {
        $this->db = ketNoiCSDL();
    }

    public function timTheoEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ? $this->dinhDang($row) : null;
    }

    public function timTheoId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE user_id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $this->dinhDang($row) : null;
    }

    public function tao(array $data): int
    {
        // Ensure the 'role' column exists to support older databases/imports
        $this->ensureRoleColumnExists();
        // Build column list dynamically in case the `username` column exists on some schemas
        $columns = ['fullname', 'email', 'password', 'phone', 'role', 'is_verified'];
        $params = [
            $data['ho_ten'],
            $data['email'],
            $data['password'],
            $data['sdt'] ?? null,
            $data['role'] ?? 'customer',
            $data['is_verified'] ?? 0,
        ];

        if ($this->hasColumn('username')) {
            // Derive a sane username from email or provided value and ensure uniqueness
            $base = '';
            if (!empty($data['username'])) {
                $base = preg_replace('/[^a-z0-9._-]/i', '', $data['username']);
            } else {
                $parts = explode('@', $data['email']);
                $base = isset($parts[0]) ? preg_replace('/[^a-z0-9._-]/i', '', $parts[0]) : 'user';
            }
            $base = substr(strtolower($base), 0, 32) ?: 'user';
            $username = $base;
            $i = 1;
            while ($this->usernameExists($username)) {
                $username = $base . $i;
                $i++;
            }
            array_unshift($columns, 'username');
            array_unshift($params, $username);
        }

        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $colsSql = implode(', ', $columns);
        $sql = "INSERT INTO users ({$colsSql}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $this->db->lastInsertId();
    }

    private function hasColumn(string $col): bool
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?"
            );
            $stmt->execute(['users', $col]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    private function usernameExists(string $username): bool
    {
        try {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
            $stmt->execute([$username]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Create the `role` column on the users table if it does not exist yet.
     * This is defensive: some databases imported from older dumps may not have the column.
     */
    private function ensureRoleColumnExists(): void
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?"
            );
            $stmt->execute(['users', 'role']);
            $count = (int) $stmt->fetchColumn();
            if ($count === 0) {
                // Add the column with a sensible default
                $this->db->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'customer'");
            }
        } catch (PDOException $e) {
            // If anything goes wrong here, don't block user creation; rethrow so the outer code can handle it
            throw $e;
        }
    }

    private function ensureAddressColumnExists(): void
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?"
            );
            $stmt->execute(['users', 'dia_chi']);
            $count = (int) $stmt->fetchColumn();
            if ($count === 0) {
                // Add the address column
                $this->db->exec("ALTER TABLE users ADD COLUMN dia_chi TEXT NULL");
            }
        } catch (PDOException $e) {
            // If anything goes wrong here, don't block user operations
            throw $e;
        }
    }

    public function capNhat(int $id, array $data): bool
    {
        $this->ensureAddressColumnExists();
        
        $fields = ['fullname = ?', 'email = ?', 'phone = ?'];
        $params = [$data['ho_ten'], $data['email'], $data['sdt'] ?? null];

        if (!empty($data['role'])) {
            $fields[] = 'role = ?';
            $params[] = $data['role'];
        }

        if (isset($data['dia_chi'])) {
            $fields[] = 'dia_chi = ?';
            $params[] = $data['dia_chi'] ?? null;
        }

        $params[] = $id;
        $stmt = $this->db->prepare(
            'UPDATE users SET ' . implode(', ', $fields) . ' WHERE user_id = ?'
        );
        return $stmt->execute($params);
    }

    public function capNhatMatKhau(int $id, string $hash): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE user_id = ?');
        return $stmt->execute([$hash, $id]);
    }

    public function saveOtp(int $userId, string $otp): bool
    {
        $expiresAt = date('Y-m-d H:i:s', time() + 600);
        $stmt = $this->db->prepare('UPDATE users SET otp_code = ?, otp_expires_at = ? WHERE user_id = ?');
        return $stmt->execute([$otp, $expiresAt, $userId]);
    }

    public function verifyOtp(string $otp): bool
    {
        $stmt = $this->db->prepare('
            SELECT user_id FROM users 
            WHERE otp_code = ? 
            AND otp_expires_at > NOW() 
            LIMIT 1
        ');
        $stmt->execute([$otp]);
        $user = $stmt->fetch();

        if (!$user) {
            return false;
        }

        $updateStmt = $this->db->prepare('
            UPDATE users 
            SET is_verified = 1, otp_code = NULL, otp_expires_at = NULL 
            WHERE user_id = ?
        ');
        return $updateStmt->execute([$user['user_id']]);
    }

    public function xoa(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE user_id = ?');
        return $stmt->execute([$id]);
    }

    public function layTatCa(): array
    {
        $stmt = $this->db->query('SELECT * FROM users ORDER BY user_id DESC');
        return array_map([$this, 'dinhDang'], $stmt->fetchAll());
    }

    private function dinhDang(array $row): array
    {
        return [
            'id'      => (int) $row['user_id'],
            'ho_ten'  => $row['fullname'] ?? '',
            'email'   => $row['email'] ?? '',
            'sdt'     => $row['phone'] ?? '',
            'dia_chi' => $row['dia_chi'] ?? '',
            'role'    => $row['role'] ?? 'customer',
            'password'=> $row['password'] ?? '',
        ];
    }
}
