<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../../config/database.php";

$database = new Database();

$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $case = $_POST['case'] ?? '';

    switch ($case) {
        case 'create_table':
            $nametable = $_POST['nametable'] ?? '';
            $status = $_POST['status'] ?? 1;

            $stmtCheckActive = $db->prepare("SELECT id FROM `table` WHERE name = :name AND is_active = 1 LIMIT 1");
            $stmtCheckActive->bindParam(':name', $nametable);
            $stmtCheckActive->execute();

            if ($stmtCheckActive->fetch(PDO::FETCH_ASSOC)) {
                echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถเพิ่มโต๊ะได้ ชื่อนี้ถูกใช้ไปแล้ว']);
                break;
            }

            $stmtCheck = $db->prepare("SELECT id FROM `table` WHERE name = :name AND is_active = 0 LIMIT 1");
            $stmtCheck->bindParam(':name', $nametable);
            $stmtCheck->execute();

            if ($row = $stmtCheck->fetch(PDO::FETCH_ASSOC)) {
                $stmtUpdate = $db->prepare("UPDATE `table` SET is_active = 1, status = 1 WHERE id = :id");
                $stmtUpdate->bindParam(':id', $row['id']);
                if ($stmtUpdate->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'เปิดใช้งานโต๊ะเดิมสำเร็จ']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถอัปเดตโต๊ะได้']);
                }
            } else {
                $stmt = $db->prepare("INSERT INTO `table` (name, status, is_active, create_at) VALUES (:name, :status, 1, NOW())");
                $stmt->bindParam(':name', $nametable);
                $stmt->bindParam(':status', $status);
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'เพิ่มโต๊ะใหม่สำเร็จ']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถเพิ่มโต๊ะได้']);
                }
            }
            break;

        case 'edit_table':
            $nametable = $_POST['nametable'] ?? '';
            $status = $_POST['status'] ?? '';
            $id = $_POST['id'];
            $stmt = $db->prepare("UPDATE `table` SET name=:name, status=:status WHERE id = :id");
            $stmt->bindParam(':name', $nametable);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'แก้ไขโต๊ะสำเร็จ']);
            }

            break;

        case 'toggle_status':
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? null;
            if ($id !== null && $status !== null) {
                $stmt = $db->prepare("UPDATE `table` SET status = :status WHERE id = :id");
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'อัปเดตสถานะสำเร็จ']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'อัปเดตไม่สำเร็จ']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
            }
            break;

        case 'delete_table':
            $id = $_POST['id'] ?? null;
            if ($id !== null) {
                $stmt = $db->prepare("UPDATE `table` SET is_active = 0 WHERE id = :id");
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'ลบโต๊ะสำเร็จ']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'ลบโต๊ะไม่สำเร็จ']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid case']);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $case = $_GET['case'] ?? '';

    switch ($case) {

        case 'get_table':
            $stmt = $db->prepare("SELECT * FROM `table` WHERE is_active = '1' ORDER BY status desc , name asc");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $data]);
            $db = null;
            break;


        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid case']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
