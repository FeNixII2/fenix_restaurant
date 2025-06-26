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
            $order_id = (int)$_POST['order_id'];
            $stmt = $db->prepare("UPDATE order_item SET status = 1 WHERE id = :order_id");
            $stmt->bindParam(':order_id', $order_id);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'สำเร็จ']);
            }


            $db = null;
            break;

        case 'update_status_order_to_2':
            $order_id = (int)$_POST['order_id'];
            $stmt = $db->prepare("UPDATE order_item SET status = 2 WHERE id = :order_id");
            $stmt->bindParam(':order_id', $order_id);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'สำเร็จ']);
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
            $stmt = $db->prepare("SELECT order_item.*,bills.bill_code,bills.create_at as bill_create,bills.table_id,menu.`name`,`table`.name as table_name FROM order_item LEFT JOIN menu on menu.id = order_item.menu_id LEFT JOIN bills on bills.id = order_item.bill_id LEFT JOIN `table` on `table`.id = bills.table_id WHERE order_item.status IN (0,1,2)");
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
