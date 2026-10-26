<?php
// backend/api/google_auth.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db_connection.php';
require_once __DIR__ . '/../includes/session_handler.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Support both JSON body and POST form data
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
    $input = $_POST;
}

$credential = trim($input['credential'] ?? '');
$email = trim($input['email'] ?? '');
$name = trim($input['name'] ?? '');

// If a Google JWT credential was sent by Google Identity Services
if (!empty($credential)) {
    $parts = explode('.', $credential);
    if (count($parts) === 3) {
        $payloadJson = base64_decode(strtr($parts[1], '-_', '+/'));
        $jwtPayload = json_decode($payloadJson, true);
        if ($jwtPayload && !empty($jwtPayload['email'])) {
            $email = strtolower(trim($jwtPayload['email']));
            $name = trim($jwtPayload['name'] ?? $jwtPayload['given_name'] ?? '');
        }
    }
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid Google account email.']);
    exit;
}

$email = strtolower($email);

try {
    // 1. Check if user already exists in DB
    $stmt = $conn->prepare('SELECT u_id, name, email, role, lifetime_grams_saved FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    // 2. If user does not exist, automatically register as a new customer
    if (!$user) {
        if (empty($name)) {
            $namePart = explode('@', $email)[0];
            $name = ucwords(str_replace(['.', '_', '-'], ' ', $namePart));
        }

        // Get default eco level
        $elId = null;
        try {
            $r = $conn->query('SELECT MIN(el_id) AS id FROM eco_levels');
            $row = $r->fetch_assoc();
            $elId = $row['id'] ?? null;
        } catch (Throwable $t) {
            $elId = null;
        }
        //Bcrypt Password Hashing
        $randomPass = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
        $role = 'customer';
        $phone = '';
        $grams = 0;

        $insStmt = $conn->prepare(
            'INSERT INTO users (name, email, password_hash, phone, role, lifetime_grams_saved, el_id) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $insStmt->bind_param('sssssii', $name, $email, $randomPass, $phone, $role, $grams, $elId);
        $insStmt->execute();
        $newId = $conn->insert_id;
        $insStmt->close();

        $user = [
            'u_id' => $newId,
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'lifetime_grams_saved' => 0
        ];
    }

    // 3. Establish authenticated session
    session_regenerate_id(true);
    $_SESSION['u_id']  = $user['u_id'];
    $_SESSION['name']  = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role']  = $user['role'];

    // 4. Set remember cookie for 30 days
    setcookie(session_name(), session_id(), [
        'expires'  => time() + 60 * 60 * 24 * 30,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    $redirectUrl = (strtolower($user['role']) === 'admin')
        ? 'modules/admin/dashboard.php'
        : 'modules/customer/user_dashboard.php';

    echo json_encode([
        'success' => true,
        'message' => 'Signed in with Google successfully.',
        'user' => [
            'id' => $user['u_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'grams' => $user['lifetime_grams_saved'] ?? 0
        ],
        'redirect' => $redirectUrl
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error during Google sign-in: ' . $e->getMessage()
    ]);
}
