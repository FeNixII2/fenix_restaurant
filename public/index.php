<?php
session_start();
require_once __DIR__ . '/../app/templates/header.php';

$isLoggedIn = isset($_SESSION['user']);
if (!$isLoggedIn) {
    require_once __DIR__ . '/../app/pages/login.php';
    exit;
}
?>

<div class="container-fluid p-0 d-flex ">

    <?php
    require_once __DIR__ . '/../app/templates/sidebar.php'; ?>

    <div class="bg-light flex-fill " style="min-height: 100vh;">
        <div class="p-2 d-md-none d-flex text-white bg-dark fs-3 fw-bold">
            <a href="#" class="text-white ms-2"
                data-bs-toggle="offcanvas"
                data-bs-target="#bdSidebar">
                <i class="fa-solid fa-bars "></i>
            </a>
            <span class="ms-3">FenixRestaurant</span>
        </div>
        <div class="container mt-3">
            <?php
            // ดึง path จาก URL
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $uri = trim($uri, '/');

            // ถ้า path ว่าง ให้เป็น home
            $route = $uri === '' ? 'dashboard' : $uri;

            $routes = [
                'dashboard' => __DIR__ . '/../app/pages/dashboard.php',
                'order' => __DIR__ . '/../app/pages/order.php',
                'kitchen' => __DIR__ . '/../app/pages/kitchen.php',
                'manages/manage_menu' => __DIR__ . '/../app/pages/manages/manage_menu.php',
                'manages/manage_table' => __DIR__ . '/../app/pages/manages/manage_table.php',
                'manages/manage_employee' => __DIR__ . '/../app/pages/manages/manage_employee.php',
                'cashier' => __DIR__ . '/../app/pages/cashier.php',
                'logout' => __DIR__ . '/../app/pages/logout.php'
            ];

            if (isset($routes[$route]) && file_exists($routes[$route])) {
                require $routes[$route];
            } else {
                echo '<h3>ไม่พบหน้า</h3>';
            }
            ?>
        </div>

    </div>
</div>




<script>

</script>




<?php
require_once __DIR__ . '/../app/templates/footer.php';
