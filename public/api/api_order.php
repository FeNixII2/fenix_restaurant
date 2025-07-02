<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../../config/database.php";

$database = new Database();

$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $case = $_POST['case'] ?? '';

    switch ($case) {
        case 'openBill':
            $table_id = (int)$_POST['table_id'];
            $employeeid = (int)$_POST['employeeid'];

            try {
                $db->beginTransaction();

                $dateCode = date("Ymd"); // เช่น 20250625
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM bills WHERE DATE(create_at) = CURDATE()");
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $nextNumber = str_pad($row['count'] + 1, 3, "0", STR_PAD_LEFT);

                $bill_code = "BILL" . $dateCode . $nextNumber;

                if ($table_id === 999) {
                    $stmt = $db->prepare("INSERT INTO bills (table_id, create_at, bill_code, create_by, bill_type) VALUES (:table_id, NOW(), :bill_code, :employeeid, 1)");
                    $stmt->bindParam(':table_id', $table_id);
                    $stmt->bindParam(':bill_code', $bill_code);
                    $stmt->bindParam(':employeeid', $employeeid);
                    $stmt->execute();
                } else {
                    $stmt = $db->prepare("INSERT INTO bills (table_id, create_at, bill_code, create_by) VALUES (:table_id, NOW(), :bill_code, :employeeid)");
                    $stmt->bindParam(':table_id', $table_id);
                    $stmt->bindParam(':bill_code', $bill_code);
                    $stmt->bindParam(':employeeid', $employeeid);
                    $stmt->execute();

                    $stmt2 = $db->prepare("UPDATE `table` SET table_state = 1 WHERE id = :table_id");
                    $stmt2->bindParam(':table_id', $table_id);
                    $stmt2->execute();
                }




                $db->commit();


                echo json_encode(['status' => 'success', 'message' => 'เปิดบิลสำเร็จ']);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode([
                    'status' => 'error',
                    'message' => 'เปิดบิลไม่สำเร็จ',
                    'error' => $e->getMessage()
                ]);
            }
            $db = null;
            break;
        case 'orderMenu':
            $orders = json_decode($_POST['orders'], true);
            $table_id = isset($_POST['table_id']) ? (int)$_POST['table_id'] : null;
            $total_amount = (int)$_POST['total_amount'];




            try {
                $db->beginTransaction();

                $stmt0 = $db->prepare("UPDATE bills SET total_amount = total_amount + :add_amount WHERE table_id = :table_id");
                $stmt0->bindParam(':table_id', $table_id);
                $stmt0->bindParam(':add_amount', $total_amount);
                $stmt0->execute();

                $stmt1 = $db->prepare("SELECT id as bills_id FROM bills  WHERE bills.table_id = :table_id AND status = 1 ");
                $stmt1->bindParam(':table_id', $table_id);
                $stmt1->execute();
                $bill = $stmt1->fetch(PDO::FETCH_ASSOC);
                $bills_id = $bill['bills_id'];

                foreach ($orders as $order) {
                    for ($i = 0; $i < (int)$order['qty']; $i++) {
                        $stmt = $db->prepare("INSERT INTO `order_item` (bill_id, menu_id, quantity, price, create_at)
                          VALUES (:bill_id, :menu_id, 1, :price, NOW())");
                        $stmt->bindValue(':menu_id', (int)$order['id'], PDO::PARAM_INT);
                        $stmt->bindValue(':price', (int)$order['price'], PDO::PARAM_INT);
                        $stmt->bindValue(':bill_id',  $bills_id, PDO::PARAM_INT);

                        if (!$stmt->execute()) {
                            throw new Exception('Insert failed for menu_id: ' . $order['id']);
                        }
                    }

                    // 2. อัปเดต stock ของเมนู
                    $stmt2 = $db->prepare("UPDATE menu SET stock = stock - :qty WHERE id = :menu_id");
                    $stmt2->bindValue(':qty', (int)$order['qty'], PDO::PARAM_INT);
                    $stmt2->bindValue(':menu_id', (int)$order['id'], PDO::PARAM_INT);

                    if (!$stmt2->execute()) {
                        throw new Exception('Stock update failed for menu_id: ' . $order['id']);
                    }
                }

                $db->commit();
                echo json_encode(['status' => 'success', 'message' => 'บันทึกออเดอร์สำเร็จ']);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
            $db = null;
            break;


        case 'cancleBill':
            $table_id = (int)$_POST['table_id'];


            try {
                $db->beginTransaction();
                $stmt = $db->prepare("UPDATE `table` SET table_state = 0 WHERE id = :table_id");
                $stmt->bindParam(':table_id', $table_id);
                $stmt->execute();


                $stmt1 = $db->prepare("SELECT id as bills_id FROM bills  WHERE bills.table_id = :table_id AND status = 1 ");
                $stmt1->bindParam(':table_id', $table_id);
                $stmt1->execute();
                $bill = $stmt1->fetch(PDO::FETCH_ASSOC);
                if (!$bill) {
                    throw new Exception('ไม่พบบิลที่เปิดอยู่สำหรับโต๊ะนี้');
                }
                $bills_id = $bill['bills_id'];


                $stmt2 = $db->prepare("UPDATE `bills` SET status = 2, close_at = NOW()  WHERE id = :bill_id");
                $stmt2->bindParam(':bill_id', $bills_id);
                $stmt2->execute();

                $db->commit();
                echo json_encode(['status' => 'success', 'message' => 'ยกเลิกบิลสำเร็จ']);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }

            break;


        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid case']);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $case = $_GET['case'] ?? '';

    switch ($case) {

        case 'getMenu':
            $stmt = $db->prepare("SELECT menu.*, category.name as category_name, images.path FROM menu LEFT JOIN category ON menu.category_id = category.id LEFT JOIN images on menu.image_id = images.id WHERE menu.is_active = '1' AND menu.status = 1 ORDER BY menu.name,menu.stock DESC ");
            $stmt->execute();
            $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $menu]);
            $db = null;
            break;

        case 'get_previousOrder':
            $table_id = (int)$_GET['table_id'];
            $stmt1 = $db->prepare("SELECT id as bills_id FROM bills  WHERE bills.table_id = :table_id AND status = 1 ");
            $stmt1->bindParam(':table_id', $table_id);
            $stmt1->execute();
            $bill = $stmt1->fetch(PDO::FETCH_ASSOC);
            $bills_id = $bill['bills_id'];
            $stmt1->execute();
            $stmt = $db->prepare("SELECT order_item.*,menu.name FROM order_item LEFT JOIN menu ON order_item.menu_id = menu.id  WHERE order_item.bill_id = :bills_id AND order_item.status != 4 ");
            $stmt->bindParam(':bills_id', $bills_id);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $data]);
            $db = null;
            break;

        case 'get_table':
            $stmt = $db->prepare("SELECT * FROM `table` WHERE is_active = '1' AND status = 1 ORDER BY status desc");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $data]);
            $db = null;
            break;

        case 'getBilltakeAway':
            $stmt = $db->prepare("SELECT * FROM `bills` WHERE table_id = 999 AND status = 1");
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
