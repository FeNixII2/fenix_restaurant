<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ล้าง session
$_SESSION = [];
session_unset();
session_destroy();

// ตั้ง header ให้บอกว่าเป็น JSON
header('Content-Type: application/json');
echo json_encode(['status' => 'success']);
exit;
