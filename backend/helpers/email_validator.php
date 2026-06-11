<?php
/**
 * email_validator.php - Kiểm tra email domain hợp lệ qua DNS
 */
class EmailValidator
{
    /**
     * Check if email domain has MX record (DNS)
     */
    public static function isValidEmailDomain(string $email): bool
    {
        // Lấy domain từ email
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return false;
        }

        $domain = strtolower($parts[1]);

        // Check basic format
        if (empty($domain) || strpos($domain, '.') === false) {
            return false;
        }

        // Kiểm tra DNS MX record
        return checkdnsrr($domain, 'MX');
    }
}
