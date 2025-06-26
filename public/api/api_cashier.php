<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../../config/database.php";

$database = new Database();

$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $case = $_POST['case'] ?? '';

    switch ($case) {
        case 'getReceipt':

            try {
                $db->beginTransaction();

                $bill_id = (int)$_POST['bill_id'];
                $table_id = (int)$_POST['table_id'];

                $stmt = $db->prepare("UPDATE bills SET status = 0, close_at = NOW() WHERE id = :bill_id");
                $stmt->bindParam(':bill_id', $bill_id);
                $stmt->execute();

                $stmt3 = $db->prepare("UPDATE `table` SET table_state = 0 WHERE id = :table_id");
                $stmt3->bindParam(':table_id', $table_id);
                $stmt3->execute();

                $db->commit();
                echo json_encode(['status' => 'success', 'message' => 'สำเร็จ']);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ยกเลิกบิลไม่สำเร็จ',
                    'error' => $e->getMessage()
                ]);
            }

            $db = null;
            break;

        case 'cancelBill':
            try {
                $db->beginTransaction();

                $bill_id = (int)$_POST['bill_id'];
                $table_id = (int)$_POST['table_id'];

                // เปลี่ยนสถานะบิลเป็น 2 (ยกเลิก)
                $stmt = $db->prepare("UPDATE bills SET status = 2, close_at = NOW() WHERE id = :bill_id");
                $stmt->bindParam(':bill_id', $bill_id);
                $stmt->execute();

                // เปลี่ยนสถานะ order ในบิลนี้เป็น 2 (ยกเลิก)
                $stmt2 = $db->prepare("UPDATE order_item SET status = 4 WHERE bill_id = :bill_id");
                $stmt2->bindParam(':bill_id', $bill_id);
                $stmt2->execute();

                $stmt3 = $db->prepare("UPDATE `table` SET table_state = 0 WHERE id = :table_id");
                $stmt3->bindParam(':table_id', $table_id);
                $stmt3->execute();

                $db->commit();

                echo json_encode(['status' => 'success', 'message' => 'ยกเลิกบิลสำเร็จ']);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ยกเลิกบิลไม่สำเร็จ',
                    'error' => $e->getMessage()
                ]);
            }

            $db = null;
            break;

        case 'deleteOrder':
            try {
                $db->beginTransaction();

                $order_id = (int)$_POST['order_id'];

                // เปลี่ยนสถานะบิลเป็น 2 (ยกเลิก)
                $stmt = $db->prepare("UPDATE order_item SET status = 4 WHERE id = :order_id");
                $stmt->bindParam(':order_id', $order_id);
                $stmt->execute();

                $db->commit();

                echo json_encode(['status' => 'success', 'message' => 'ยกเลิกรายการสำเร็จ']);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ยกเลิกรายการไม่สำเร็จ',
                    'error' => $e->getMessage()
                ]);
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

        case 'getBills':
            $stmt = $db->prepare("SELECT bills.*,table.name FROM bills LEFT JOIN `table` on `table`.id = bills.table_id");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt2 = $db->prepare("SELECT order_item.*,menu.name FROM order_item LEFT JOIN menu on menu.id = order_item.menu_id");
            $stmt2->execute();
            $data2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $data, 'data2' => $data2]);
            $db = null;
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid case']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
