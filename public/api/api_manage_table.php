<?php
header('Content-Type: application/json');

require_once __DIR__ . "/../../config/database.php";

$database = new Database();

$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $case = $_POST['case'] ?? '';

    switch ($case) {
        case 'create_table':
            $nametable = $_POST['nametable'] ?? '';
            $status = $_POST['status'] ?? '';
            $stmt=$db->prepare("INSERT INTO `table` (name, status, create_at) VALUE (:name, :status, NOW())");
            $stmt->bindParam(':name', $nametable);
            $stmt->bindParam(':status', $status);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'เพิ่มโต๊ะสำเร็จ']);
            }

            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid case']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
