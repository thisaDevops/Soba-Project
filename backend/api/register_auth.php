<?php
// backend/api/register_auth.php
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

$name     = trim($_POST['name'] ?? '');
$email    = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

// ---------- Validation ----------
if ($name === '' || $email === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}
if (mb_strlen($name) > 100) {
    echo json_encode(['success' => false, 'message' => 'Name is too long.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
    exit;
}

try {
    // ---------- Email already exists? ----------
    $stmt = $conn->prepare('SELECT u_id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($exists) {
        echo json_encode(['success' => false, 'message' => 'This email is already registered. Please login.']);
        exit;
    }

    // ---------- Starting eco level (eco_levels table එකේ පළමු level එක) ----------
    $elId = null;
    try {
        $r = $conn->query('SELECT MIN(el_id) AS id FROM eco_levels');
        $row = $r->fetch_assoc();
        $elId = $row['id'] ?? null;
    } catch (Throwable $e) {
        $elId = null;
    }

    // ---------- Insert ----------
    $hash  = password_hash($password, PASSWORD_DEFAULT);
    $role  = 'customer';   // role එක client එකෙන් කවදාවත් ගන්නේ නෑ. Register වෙන්නේ customer ලා විතරයි.

    $cols  = ['name', 'email', 'password_hash', 'phone', 'role', 'lifetime_grams_saved'];
    $vals  = [$name, $email, $hash, '', $role, 0];
    $types = 'sssssi';

    if ($elId !== null) {
        $cols[]  = 'el_id';
        $vals[]  = (int)$elId;
        $types  .= 'i';
    }

    $sql = 'INSERT INTO users (' . implode(',', $cols) . ') VALUES ('
         . implode(',', array_fill(0, count($cols), '?')) . ')';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$vals);
    $stmt->execute();
    $newId = $conn->insert_id;
    $stmt->close();

    // Register වුණාට පස්සේ auto login කරන්නේ නෑ. User login page එකෙන් login වෙන්න ඕනේ.
    echo json_encode([
        'success' => true,
        'message' => 'Account created. Please login.',
        'user'    => ['name' => $name, 'email' => $email],
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $DEBUG ? 'DB error: ' . $e->getMessage() : 'Registration failed. Please try again.',
    ]);
}