<?php

declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'futureworth';
const DB_USER = 'phil';
const DB_PASS = 'phil';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    return $pdo;
}

function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

function readJsonBody(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $decoded = json_decode($raw, true);

    if (!is_array($decoded)) {
        jsonResponse(['error' => 'Invalid JSON body.'], 400);
    }

    return $decoded;
}

function toMoney(mixed $value, string $field): float
{
    if (!is_numeric($value)) {
        jsonResponse(['error' => "{$field} must be numeric."], 422);
    }

    return round((float) $value, 2);
}

function toInt(mixed $value, string $field): int
{
    if (!is_numeric($value)) {
        jsonResponse(['error' => "{$field} must be numeric."], 422);
    }

    return (int) $value;
}
