<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../../config/database.php";

$database = new Database();

$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $case = $_POST['case'] ?? '';

    switch ($case) {
        case 'create_employee':
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $firstname = $_POST['firstname'] ?? '';
            $lastname = $_POST['lastname'] ?? '';
            $role = $_POST['role'] ?? '';
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $db->prepare("INSERT INTO employee (username, email, `password`, firstname, lastname, `role`, create_at) VALUES (:username, :email, :password, :firstname, :lastname, :role, NOW())");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':role', $role);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'เพิ่มพนักงานสำเร็จ']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'เพิ่มพนักงานไม่สำเร็จ']);
            }
            $db = null;
            break;

        case 'edit_employee':
            $username = $_POST['editusername'] ?? '';
            $email = $_POST['editemail'] ?? '';
            $password = $_POST['editpassword'] ?? '';
            $firstname = $_POST['editfirstname'] ?? '';
            $lastname = $_POST['editlastname'] ?? '';
            $role = $_POST['editrole'] ?? '';

            if ($password !== '') {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE employee SET  email = :email, `password` = :password, firstname = :firstname, lastname = :lastname, `role` = :role WHERE username = :username ");
                $stmt->bindParam(':password', $hashedPassword);
            } else {
                $stmt = $db->prepare("UPDATE employee SET  email = :email, firstname = :firstname, lastname = :lastname, `role` = :role WHERE username = :username ");
            }
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':role', $role);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'แก้ไขพนักงานสำเร็จ']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'แก้ไขพนักงานไม่สำเร็จ']);
            }
            $db = null;
            break;

        case 'delete_employee':
            $id = $_POST['id'] ?? '';
            $stmt = $db->prepare("UPDATE employee SET is_active = 0 WHERE id = :id");
            $stmt->bindParam(':id', $id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'ลบพนักงานสำเร็จ']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ลบพนักงานไม่สำเร็จ']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid case']);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $case = $_GET['case'] ?? '';

    switch ($case) {
        case 'getEmployee':
            $stmt = $db->prepare("SELECT employee.*,role.name AS rolename FROM employee LEFT JOIN role on employee.role = role.id WHERE employee.is_active = 1");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $data]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid case']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
