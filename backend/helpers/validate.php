<?php
/**
 * validate.php - Ham validate du lieu
 */
function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validateRequired(array $data, array $fields): ?string
{
    foreach ($fields as $field) {
        if (empty(trim($data[$field] ?? ''))) {
            return "Truong {$field} la bat buoc";
        }
    }
    return null;
}

function validateMinLength(string $value, int $min): bool
{
    return strlen($value) >= $min;
}
