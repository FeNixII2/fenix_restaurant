<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
$database = new Database();

$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $case = $_POST['case'] ?? '';

    switch ($case) {
        case 'addCategory':
            $categoryName = $_POST['categoryName'] ?? '';

            if ($categoryName) {
                $stmt = $db->prepare("SELECT id, is_active FROM category WHERE name = :name LIMIT 1");
                $stmt->bindParam(':name', $categoryName);
                $stmt->execute();
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing) {
                    if ($existing['is_active'] == 0) {
                        $updateStmt = $db->prepare("UPDATE category SET is_active = 1 WHERE id = :id");
                        $updateStmt->bindParam(':id', $existing['id']);
                        if ($updateStmt->execute()) {
                            echo json_encode(['status' => 'success', 'message' => 'เปิดใช้งานประเภทเมนูอีกครั้ง']);
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถเปิดใช้งานประเภทเมนูได้']);
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'ประเภทเมนูนี้มีอยู่แล้ว']);
                    }
                } else {
                    $stmt = $db->prepare("INSERT INTO category (name, create_at, is_active) VALUES (:name, NOW(), 1)");
                    $stmt->bindParam(':name', $categoryName);
                    if ($stmt->execute()) {
                        echo json_encode(['status' => 'success', 'message' => 'เพิ่มประเภทเมนูสำเร็จ']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถเพิ่มประเภทเมนูได้']);
                    }
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'กรุณาระบุชื่อประเภทเมนู']);
            }

            $db = null;
            break;
        case 'editCategory':
            $categoryId = $_POST['id'] ?? '';
            $categoryName = $_POST['categoryName'] ?? '';
            if ($categoryId && $categoryName) {
                $stmt = $db->prepare("UPDATE category SET name = :name WHERE id = :id");
                $stmt->bindParam(':name', $categoryName);
                $stmt->bindParam(':id', $categoryId);
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'แก้ไขประเภทเมนูสำเร็จ']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to edit category']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Category ID and name are required']);
            }
            $db = null;
            break;
        case 'deleteCategory':
            $categoryId = $_POST['id'] ?? '';
            if ($categoryId) {
                $stmt = $db->prepare("UPDATE category SET is_active = '0' WHERE id = :id");
                $stmt->bindParam(':id', $categoryId);
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'ลบประเภทเมนูสำเร็จ']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to delete category']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Category ID is required']);
            }
            $db = null;
            break;

        case 'addMenu':
            $menuName = $_POST['menuName'] ?? '';
            $menuCategory = $_POST['menuCategory'] ?? '';
            $menuDetails = $_POST['menuDetails'] ?? '';
            $price = $_POST['price'] ?? '';
            $status = $_POST['status'] ?? '';

            if (isset($_FILES['foodImage']) && $_FILES['foodImage']['error'] === 0) {
                $targetDir = __DIR__ . '/../uploads/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $fileName = uniqid() . '_' . basename($_FILES['foodImage']['name']);
                $targetFile = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['foodImage']['tmp_name'], $targetFile)) {
                    $imagePath = '/uploads/' . $fileName;

                    // INSERT ลงตาราง images
                    $imageStmt = $db->prepare("INSERT INTO images (name ,path, create_at) VALUES (:name, :path, NOW())");
                    $imageStmt->bindParam(':name', $fileName);
                    $imageStmt->bindParam(':path', $imagePath);
                    $imageStmt->execute();
                    $imageId = $db->lastInsertId();  // เอา id ไปใส่ menu

                    $stmt = $db->prepare("INSERT INTO menu (name, category_id, details, price, image_id, status, create_at)
                      VALUES (:name, :category_id, :details, :price, :image_id, :status, NOW())");
                    $stmt->bindParam(':image_id', $imageId); // ใส่ imageId แทน path

                }
            } else {
                $stmt = $db->prepare("INSERT INTO menu (name, category_id, details, price, status, create_at)
                      VALUES (:name, :category_id, :details, :price, :status, NOW())");
            }


            $stmt->bindParam(':name', $menuName);
            $stmt->bindParam(':category_id', $menuCategory);
            $stmt->bindParam(':details', $menuDetails);
            $stmt->bindParam(':price', $price);

            $stmt->bindParam(':status', $status);
            $stmt->execute();
            // Check if the insert was successful
            if ($stmt->rowCount() > 0) {
                echo json_encode(['status' => 'success', 'message' => 'เพิ่มเมนูสำเร็จ']);
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'เพิ่มเมนูไม่สำเร็จ']);
                exit;
            }
            $db = null;
            break;

        case 'editMenu':
            $menuId = $_POST['id'] ?? '';
            $menuName = $_POST['menuName'] ?? '';
            $menuCategory = intval($_POST['menuCategory']) ?? '';
            $menuDetails = $_POST['menuDetails'] ?? '';
            $price = floatval($_POST['price']) ?? '';
            $status = intval($_POST['status']) ?? '';
            $imageId = isset($_POST['image_id']) ? intval($_POST['image_id']) : null;

            if (isset($_FILES['foodImage']) && $_FILES['foodImage']['error'] === 0) {

                $stmtSelect = $db->prepare("SELECT name FROM images WHERE id = :id");
                $stmtSelect->bindParam(':id', $imageId);
                $stmtSelect->execute();
                $imageData = $stmtSelect->fetchColumn();

                // ลบไฟล์รูปเก่าหากมี
                if ($imageData && file_exists(__DIR__ . '/../uploads/' . $imageData)) {
                    unlink(__DIR__ . '/../uploads/' . $imageData);
                    $deleteimgStmt = $db->prepare("DELETE FROM images WHERE id = :id");
                    $deleteimgStmt->bindParam(':id', $imageId);
                    $deleteimgStmt->execute();
                }

                // อัปโหลดรูปใหม่
                $targetDir = __DIR__ . '/../uploads/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fileName = uniqid() . '_' . basename($_FILES['foodImage']['name']);
                $targetFile = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['foodImage']['tmp_name'], $targetFile)) {
                    $imagePath = '/uploads/' . $fileName;

                    // INSERT ลงตาราง images
                    $imageStmt = $db->prepare("INSERT INTO images (name ,path, create_at) VALUES (:name, :path, NOW())");
                    $imageStmt->bindParam(':name', $fileName);
                    $imageStmt->bindParam(':path', $imagePath);
                    $imageStmt->execute();
                    $imageId = $db->lastInsertId();  // เอา id ไปใส่ menu
                }

                // เตรียม statement พร้อมอัปเดตรูป
                $stmt = $db->prepare("UPDATE menu 
                SET name = :name, category_id = :category_id, price = :price, image_id = :image, details= :details, status = :status 
                WHERE id = :id");
                $stmt->bindParam(':image', $imageId);
            } else {
                $stmt = $db->prepare("UPDATE menu 
                SET name = :name, category_id = :category_id, price = :price, details= :details, status = :status 
                WHERE id = :id");
            }
            $stmt->bindParam(':details', $menuDetails);
            $stmt->bindParam(':name', $menuName);
            $stmt->bindParam(':category_id', $menuCategory);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $menuId);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'แก้ไขเมนูสำเร็จ']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to edit menu']);
            }
            $db = null;
            break;

        case 'deleteMenu':
            $menuId = $_POST['id'] ?? '';
            if ($menuId) {
                $stmt = $db->prepare("UPDATE menu SET is_active = '0' WHERE id = :id");
                $stmt->bindParam(':id', $menuId);
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'ลบเมนูสำเร็จ']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to delete menu']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Menu ID is required']);
            }
            $db = null;
            break;

        case 'updateStock':
            $items = json_decode($_POST['stockItems'] ?? '[]', true);
            foreach ($items as $item) {
                $id = intval($item['id']);
                $stock = intval($item['stock']);

                $stmt = $db->prepare("UPDATE menu SET stock = :stock WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':stock', $stock);
                $stmt->execute();
            }

            echo json_encode(['status' => 'success', 'message' => 'แก้ไขสต๊อกสำเร็จ']);
            $db = null;
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid case']);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $case = $_GET['case'] ?? '';

    switch ($case) {

        case 'getCategory':
            $stmt = $db->prepare("SELECT * FROM category WHERE is_active = '1' ORDER BY create_at DESC");
            $stmt->execute();
            $category = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $category]);
            $db = null;
            break;

        case 'getAllMenu':
            $stmt = $db->prepare("SELECT menu.*, category.name as category_name, images.path FROM menu LEFT JOIN category ON menu.category_id = category.id LEFT JOIN images on menu.image_id = images.id WHERE menu.is_active = '1'  ORDER BY menu.is_active DESC");
            $stmt->execute();
            $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $menu]);
            $db = null;
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid case']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
