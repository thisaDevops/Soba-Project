<?php
require_once __DIR__ . '/../backend/includes/session_handler.php';


$dest = (($_SESSION['role'] ?? '') === 'admin')
    ? 'modules/admin/dashboard.php'
    : 'home.php';

header("Location: $dest");
exit;
?>