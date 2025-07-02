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
                $selectedMethod = $_POST['selectedMethod'];
                $user_id = $_SESSION['user']['id'];

                $stmt = $db->prepare("UPDATE bills SET status = 0, close_at = NOW(), payment = :selectedMethod, checkbill_by = :checkbill_by   WHERE id = :bill_id");
                $stmt->bindParam(':bill_id', $bill_id);
                $stmt->bindParam(':selectedMethod', $selectedMethod);
                $stmt->bindParam(':checkbill_by', $user_id);
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

                $stmtStock = $db->prepare("SELECT menu_id, quantity, price FROM order_item WHERE bill_id = :bill_id");
                $stmtStock->bindParam(':bill_id', $bill_id);
                $stmtStock->execute();
                $orders = $stmtStock->fetchAll(PDO::FETCH_ASSOC);

                $stmt = $db->prepare("UPDATE bills SET status = 2, close_at = NOW() WHERE id = :bill_id");
                $stmt->bindParam(':bill_id', $bill_id);
                $stmt->execute();

                $stmt2 = $db->prepare("UPDATE order_item SET status = 4 WHERE bill_id = :bill_id");
                $stmt2->bindParam(':bill_id', $bill_id);
                $stmt2->execute();

                foreach ($orders as $order) {
                    $menu_id = $order['menu_id'];
                    $quantity = $order['quantity'];
                    $price = $order['price'];

                    $stmtUpdateStock = $db->prepare("UPDATE menu SET stock = stock + :quantity WHERE id = :menu_id");
                    $stmtUpdateStock->bindParam(':quantity', $quantity);
                    $stmtUpdateStock->bindParam(':menu_id', $menu_id);
                    $stmtUpdateStock->execute();

                    $stmttotal = $db->prepare("UPDATE bills SET total_amount = total_amount - :price WHERE id = :bill_id");
                    $stmttotal->bindParam(':bill_id', $bill_id);
                    $stmttotal->bindParam(':price', $price);
                    $stmttotal->execute();
                }

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

                $menu_id = (int)$_POST['menu_id'];
                $billId = (int)$_POST['billId'];
                $qty = (int)$_POST['qty'];
                $total = (int)$_POST['total'];

                $stmt = $db->prepare("UPDATE order_item SET status = 4 WHERE menu_id = :menu_id AND bill_id = :bill_id AND status = 0 ");
                $stmt->bindParam(':menu_id', $menu_id);
                $stmt->bindParam(':bill_id', $billId);
                $stmt->execute();

                $stmt = $db->prepare("UPDATE menu SET stock = stock + :quantity WHERE id = :menu_id");
                $stmt->bindParam(':quantity', $qty);
                $stmt->bindParam(':menu_id', $menu_id);
                $stmt->execute();

                $stmt = $db->prepare("UPDATE bills SET total_amount = total_amount - :price WHERE id = :billId");
                $stmt->bindParam(':price', $total);
                $stmt->bindParam(':billId', $billId);
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


        case 'getHistortBill':
            $stmt = $db->prepare("SELECT 
                                    bills.*, 
                                    `table`.name,
                                    
                                    e1.firstname AS order_fname,
                                    e1.lastname AS order_lname,
                                    
                                    e2.firstname AS cashier_fname,
                                    e2.lastname AS cashier_lname,

                                    payment.name AS name_payment

                                    FROM bills

                                    LEFT JOIN `table` ON `table`.id = bills.table_id
                                    LEFT JOIN employee AS e1 ON e1.id = bills.create_by    -- พนักงานเปิดโต๊ะ
                                    LEFT JOIN employee AS e2 ON e2.id = bills.checkbill_by     -- พนักงานคิดเงิน
                                    LEFT JOIN payment ON payment.id = bills.payment");
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
