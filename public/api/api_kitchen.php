<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../../config/database.php";

$database = new Database();

$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $case = $_POST['case'] ?? '';

    switch ($case) {
        case 'update_status_order_to_1':
            $bill_id = (int)$_POST['bill_id'];
            $menuId = (int)$_POST['menu_id'];
            $quantity = (int)$_POST['quantity'];

            $stmt = $db->prepare("UPDATE order_item oi
                          JOIN bills b ON oi.bill_id = b.id
                          SET oi.status = 1
                          WHERE b.id = :bill_id AND oi.menu_id = :menu_id AND oi.status = 0");

            $stmt->bindParam(':bill_id', $bill_id, PDO::PARAM_INT);
            $stmt->bindParam(':menu_id', $menuId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'อัปเดตสถานะเรียบร้อย']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'อัปเดตสถานะไม่สำเร็จ']);
            }
            $db = null;
            break;

        case 'update_status_order_to_2':
            $bill_id = (int)$_POST['bill_id'];
            $menuId = (int)$_POST['menu_id'];
            $quantity = (int)$_POST['quantity'];

            $stmt = $db->prepare("UPDATE order_item oi
                          JOIN bills b ON oi.bill_id = b.id
                          SET oi.status = 2
                          WHERE b.id = :bill_id AND oi.menu_id = :menu_id AND oi.status = 1");

            $stmt->bindParam(':bill_id', $bill_id, PDO::PARAM_INT);
            $stmt->bindParam(':menu_id', $menuId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'อัปเดตสถานะเรียบร้อย']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'อัปเดตสถานะไม่สำเร็จ']);
            }

            $db = null;
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid case']);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $case = $_GET['case'] ?? '';

    switch ($case) {

        case 'getOrder':
            $stmt = $db->prepare("SELECT order_item.*, bills.bill_code, bills.create_at AS bill_create, bills.table_id,
       menu.`name`, menu.serve_type, `table`.name AS table_name
FROM order_item
LEFT JOIN menu ON menu.id = order_item.menu_id
LEFT JOIN bills ON bills.id = order_item.bill_id
LEFT JOIN `table` ON `table`.id = bills.table_id
WHERE 
    order_item.status IN (0, 1, 2)
    AND menu.serve_type = 1
    AND (
        order_item.status != 2
        OR bills.create_at >= NOW() - INTERVAL 1 DAY
    )");
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
