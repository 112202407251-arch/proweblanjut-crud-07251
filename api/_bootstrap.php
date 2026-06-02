<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once dirname(__DIR__) . '/config/database.php';

function send_json(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function get_input_data(): array
{
    $contentType = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));
    $rawBody = file_get_contents('php://input');
    $rawBody = $rawBody === false ? '' : trim($rawBody);

    if (strpos($contentType, 'application/json') !== false && $rawBody !== '') {
        $decoded = json_decode($rawBody, true);
        if (is_array($decoded)) {
            return $decoded;
        }
        return [];
    }

    $parsed = [];
    if ($rawBody !== '') {
        parse_str($rawBody, $parsed);
    }

    if (!empty($_POST)) {
        $parsed = array_merge($parsed, $_POST);
    }

    return is_array($parsed) ? $parsed : [];
}

function method_not_allowed(array $allowedMethods): void
{
    header('Allow: ' . implode(', ', $allowedMethods));
    send_json(405, [
        'success' => false,
        'message' => 'Method tidak diizinkan.',
        'allowed_methods' => $allowedMethods,
    ]);
}

