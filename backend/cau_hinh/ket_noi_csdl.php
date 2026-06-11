<?php
/**
 * ket_noi_csdl.php - Ket noi PDO toi shop_db
 */
require_once __DIR__ . '/hang_so.php';

function ketNoiCSDL(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            // Log the detailed error for developer troubleshooting, but do not expose details to users
            try {
                $logDir = __DIR__ . '/../logs';
                if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
                $logFile = $logDir . '/db_errors.log';
                $msg = date('[Y-m-d H:i:s] ') . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n" . $e->getTraceAsString() . "\n\n";
                @file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);
            } catch (Exception $ignore) {
                // ignore logging failures
            }
            // Return a JSON response so API callers receive a consistent error object
            if (function_exists('traVeJson')) {
                require_once __DIR__ . '/../helpers/phan_hoi_json.php';
                traVeJson(false, null, 'Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.', 500);
            }
            // If traVeJson is not available for some reason, throw a generic exception
            throw new Exception('Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.');
        }
    }

    return $pdo;
}
