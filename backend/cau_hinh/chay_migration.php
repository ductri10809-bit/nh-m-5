<?php
/**
 * Chay migration luxury_upgrade.sql
 */
require_once __DIR__ . '/ket_noi_csdl.php';

header('Content-Type: text/plain; charset=utf-8');

$sqlFile = dirname(__DIR__, 2) . '/database/luxury_upgrade.sql';
if (!is_file($sqlFile)) {
    http_response_code(500);
    echo "Khong tim thay file SQL.\n";
    exit;
}

$pdo = ketNoiCSDL();
$sql = file_get_contents($sqlFile);
$sql = preg_replace('/--.*$/m', '', $sql);

$statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));

function columnExists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
    );
    $stmt->execute([$table, $column]);
    return (bool) $stmt->fetchColumn();
}

$ok = 0;
$skip = 0;
$err = 0;

foreach ($statements as $statement) {
    if ($statement === '') {
        continue;
    }
    if (stripos($statement, 'ADD COLUMN IF NOT EXISTS') !== false) {
        if (preg_match('/ALTER TABLE\s+(\w+)\s+ADD COLUMN IF NOT EXISTS\s+(\w+)/i', $statement, $m)) {
            if (columnExists($pdo, $m[1], $m[2])) {
                $skip++;
                continue;
            }
            $statement = preg_replace('/IF NOT EXISTS\s+/i', '', $statement);
        }
    }
    try {
        $pdo->exec($statement);
        $ok++;
    } catch (Throwable $e) {
        $err++;
        echo "ERR: " . substr($statement, 0, 100) . "...\n => " . $e->getMessage() . "\n\n";
    }
}

echo "Migration: $ok ok, $skip skip, $err err.\n";
echo "San pham: " . $pdo->query('SELECT COUNT(*) FROM product')->fetchColumn() . "\n";
echo "Bien the: " . $pdo->query('SELECT COUNT(*) FROM product_variant')->fetchColumn() . "\n";
