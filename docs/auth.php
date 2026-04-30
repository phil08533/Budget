<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

session_start();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? '';

try {
    if ($action === 'login' && $method === 'POST') {
        $body = readJsonBody();
        $email = trim((string) ($body['email'] ?? ''));
        $password = trim((string) ($body['password'] ?? ''));

        if (!$email || !$password) {
            jsonResponse(['error' => 'Email and password required'], 422);
        }

        $pdo = db();
        $stmt = $pdo->prepare('SELECT user_id, username, password_hash FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            jsonResponse(['error' => 'Invalid email or password'], 401);
        }

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        jsonResponse(['success' => true, 'message' => 'Logged in successfully']);
    }
    elseif ($action === 'register' && $method === 'POST') {
        $body = readJsonBody();
        $username = trim((string) ($body['username'] ?? ''));
        $email = trim((string) ($body['email'] ?? ''));
        $password = trim((string) ($body['password'] ?? ''));

        if (!$username || !$email || !$password) {
            jsonResponse(['error' => 'All fields required'], 422);
        }

        if (strlen($password) < 6) {
            jsonResponse(['error' => 'Password must be at least 6 characters'], 422);
        }

        $pdo = db();

        // Check if email exists
        $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            jsonResponse(['error' => 'Email already registered'], 422);
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
        $stmt->execute([$username, $email, $passwordHash]);

        $userId = $pdo->lastInsertId();
        $_SESSION['user_id'] = (int) $userId;
        $_SESSION['username'] = $username;

        jsonResponse(['success' => true, 'message' => 'Account created successfully']);
    }
    elseif ($action === 'demo' && $method === 'POST') {
        // Demo user login
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'demo';
        jsonResponse(['success' => true, 'message' => 'Demo user logged in']);
    }
    elseif ($action === 'logout' && $method === 'POST') {
        session_destroy();
        jsonResponse(['success' => true, 'message' => 'Logged out']);
    }
    elseif ($action === 'check' && $method === 'GET') {
        if (isset($_SESSION['user_id'])) {
            jsonResponse([
                'logged_in' => true,
                'user_id' => $_SESSION['user_id'],
                'username' => $_SESSION['username']
            ]);
        } else {
            jsonResponse(['logged_in' => false]);
        }
    }
    else {
        jsonResponse(['error' => 'Unknown action'], 404);
    }
} catch (Exception $e) {
    http_response_code(500);
    jsonResponse(['error' => $e->getMessage()]);
}
?>
