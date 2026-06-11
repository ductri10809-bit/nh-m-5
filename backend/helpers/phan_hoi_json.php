<?php
/**
 * phan_hoi_json.php - Tra ve JSON response chuan
 */
function traVeJson(bool $success, $data = null, string $message = '', int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function layDuLieuJson(): array
{
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

// Global error/exception handlers: ensure APIs always return JSON on failure
if (php_sapi_name() !== 'cli') {
    set_error_handler(function ($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) {
            return false; // respect @ operator
        }
        throw new ErrorException($message, 0, $severity, $file, $line);
    });

    set_exception_handler(function ($e) {
        $msg = $e instanceof Throwable ? $e->getMessage() : (string)$e;
        if (headers_sent()) {
            error_log('Uncaught exception: ' . $msg);
            exit(1);
        }
        // Hide internal file/line in production if desired; for now include message.
        traVeJson(false, null, 'Internal server error: ' . $msg, 500);
    });

    register_shutdown_function(function () {
        $err = error_get_last();
        if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            if (!headers_sent()) {
                traVeJson(false, null, 'Fatal error: ' . ($err['message'] ?? 'Unknown'), 500);
            } else {
                error_log('Shutdown fatal: ' . ($err['message'] ?? 'Unknown'));
            }
        }
    });
}
