<?php
// backend/api/login_auth.php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db_connection.php';
require_once __DIR__ . '/../includes/session_handler.php';

// Development වලදී true. Project එක submit කරද්දී false කරන්න.
$DEBUG = true;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$loginInput = trim($_POST['email'] ?? $_POST['username'] ?? '');
$password   = $_POST['password'] ?? '';
$remember   = !empty($_POST['remember']);

if ($loginInput === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Please fill in your email/username and password.']);
    exit;
}

try {
    // DB එකේ user ව email හෝ name (username) එකෙන් හොයනවා
    $stmt = $conn->prepare(
        'SELECT u_id, name, email, password_hash, role, lifetime_grams_saved, el_id
         FROM users WHERE email = ? OR name = ? LIMIT 1'
    );
    $stmt->bind_param('ss', $loginInput, $loginInput);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // User නැත්නම් හෝ password වැරදි නම්
    if (!$user || !password_verify($password, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Incorrect email/username or password.']);
        exit;
    }

    // Login සාර්ථකයි: session එක හදනවා
    session_regenerate_id(true);
    $_SESSION['u_id']  = $user['u_id'];
    $_SESSION['name']  = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role']  = $user['role'];

    // Remember me: session cookie එක දවස් 30ක් තියාගන්නවා
    if ($remember) {
        setcookie(session_name(), session_id(), [
            'expires'  => time() + 60 * 60 * 24 * 30,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    echo json_encode([
        'success' => true,
        'user' => [
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
            'grams' => $user['lifetime_grams_saved'],
        ],
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $DEBUG ? 'DB error: ' . $e->getMessage() : 'Login failed. Please try again.',
    ]);
}