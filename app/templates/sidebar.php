<?php
$role = $_SESSION['user']['role'] ?? null;
$id = $_SESSION['user']['id'] ?? null;
?>

<div id="bdSidebar"
    class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white offcanvas-lg offcanvas-start"
    style="width:20rem; ">

    <div class="d-flex justify-content-between align-items-center">
        <a href="/dashboard"
            class="navbar-brand fs-3 fw-bold">
            <img src="/assets/images/letter-f.png" alt="" style="width:3rem;height:3rem">
            enixRestaurant
        </a>
        <button type="button" class="btn-close btn-close-white d-lg-none" id="btn-close-sidebar"></button>
    </div>
    <hr>
    <ul class="mynav nav nav-pills flex-column mb-auto">
        <?php if ($role == 1 or $role == 4): // admin เห็นทุกอย่าง 
        ?>
            <li class="nav-item mb-1">
                <a href="/dashboard" class="<?= ($route === 'dashboard' ? 'active' : '') ?>">
                    <i class="fa-solid fa-house"></i>
                    หน้าหลัก
                </a>
            </li>

            <li class="nav-item mb-1">
                <a href="/order" class="<?= ($route === 'order' ? 'active' : '') ?>">
                    <i class="fa-solid fa-bell-concierge"></i>
                    สั่งอาหาร
                </a>
            </li>

            <li class="nav-item mb-1">
                <a href="/kitchen" class="<?= ($route === 'kitchen' ? 'active' : '') ?>">
                    <i class="fa-solid fa-kitchen-set"></i>
                    งานครัว
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/cashier" class="<?= ($route === 'cashier' ? 'active' : '') ?>">
                    <i class="fa-solid fa-kitchen-set"></i>
                    แคชเชียร์
                </a>
            </li>

            <?php
            $settingsPages = [
                'manages/manage_menu',
                'manages/manage_table',
                'manages/manage_employee'
            ];

            $isSettingsOpen = in_array($route, $settingsPages);
            ?>


            <li class="sidebar-item nav-item mb-1">
                <a href="#"
                    class="sidebar-link collapsed d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse"
                    data-bs-target="#settings"
                    aria-expanded="<?= $isSettingsOpen ? 'true' : 'false' ?>"
                    aria-controls="settings"
                    id="settingsToggle">
                    <div><i class="fas fa-cog pe-2"></i>
                        <span class="topic">ตั้งค่า </span>
                    </div>
                    <i class="fa-solid <?= $isSettingsOpen ? 'fa-caret-up' : 'fa-caret-down' ?>" id="settingsCaret"></i>
                </a>

                <ul id="settings"
                    class="sidebar-dropdown list-unstyled collapse <?= $isSettingsOpen ? 'show' : '' ?>"
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a href="/manages/manage_menu" class="sidebar-link ps-4 my-1 <?= ($route === 'manages/manage_menu' ? 'active' : '') ?>">
                            <i class="fa-solid fa-bowl-rice pe-2"></i>
                            <span class="topic">เมนู</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="/manages/manage_table" class="sidebar-link ps-4 mb-1 <?= ($route === 'manages/manage_table' ? 'active' : '') ?>">
                            <i class="fa-solid fa-stroopwafel pe-2"></i>
                            <span class="topic">โต๊ะ</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="/manages/manage_employee" class="sidebar-link ps-4 mb-1 <?= ($route === 'manages/manage_employee' ? 'active' : '') ?>">
                            <i class="fa-solid fa-users pe-2"></i>
                            <span class="topic">พนักงาน</span>
                        </a>
                    </li>
                </ul>
            </li>

        <?php elseif ($role == 2): // staff เห็นแค่ order 
        ?>
            <li class="nav-item mb-1">
                <a href="/dashboard" class="<?= ($route === 'dashboard' ? 'active' : '') ?>">
                    <i class="fa-solid fa-house"></i>
                    หน้าหลัก
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/order" class="<?= ($route === 'order' ? 'active' : '') ?>">
                    <i class="fa-solid fa-bell-concierge"></i>
                    สั่งอาหาร
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/cashier" class="<?= ($route === 'cashier' ? 'active' : '') ?>">
                    <i class="fa-solid fa-kitchen-set"></i>
                    แคชเชียร์
                </a>
            </li>
        <?php elseif ($role == 3): // staff เห็นแค่ order 
        ?>
            <li class="nav-item mb-1">
                <a href="/dashboard" class="<?= ($route === 'dashboard' ? 'active' : '') ?>">
                    <i class="fa-solid fa-house"></i>
                    หน้าหลัก
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/kitchen" class="<?= ($route === 'kitchen' ? 'active' : '') ?>">
                    <i class="fa-solid fa-kitchen-set"></i>
                    งานครัว
                </a>
            </li>
        <?php endif; ?>
    </ul>
    <hr>
    <ul class="mynav nav  nav-pills">

        <li class="nav-item mb-1 w-100">
            <a
                href="#"
                id="logoutBtn">
                <i class="fa-solid fa-right-from-bracket"></i>
                ออกจากระบบ
            </a>
        </li>
    </ul>
</div>

<script>
    $(document).ready(function() {
    })
    document.getElementById('btn-close-sidebar').addEventListener('click', function() {
        const offcanvasElement = document.getElementById('bdSidebar');
        const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);

        if (offcanvasInstance) {
            offcanvasInstance.hide();
        }
    });

    const toggleLink = document.getElementById('settingsToggle');
    const caretIcon = document.getElementById('settingsCaret');

    if (toggleLink && caretIcon) {
        toggleLink.addEventListener('click', function() {
            const isCollapsed = toggleLink.classList.contains('collapsed');
            if (isCollapsed) {
                caretIcon.classList.remove('fa-caret-right');
                caretIcon.classList.add('fa-caret-down');
            } else {
                caretIcon.classList.remove('fa-caret-down');
                caretIcon.classList.add('fa-caret-right');
            }
        });
    }

    $('#logoutBtn').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: "คุณต้องการออกจากระบบ ?",
            showDenyButton: true,
            confirmButtonText: "ใช่",
            denyButtonText: `ยกเลิก`
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('/api/api_logout.php', function(response) {
                    if (response.status === 'success') {
                        window.location.href = '/login.php';
                    } else {
                        alert('Logout ไม่สำเร็จ ลองใหม่อีกครั้ง');
                    }
                }, 'json').fail(function(xhr, status, error) {
                    console.error('Logout AJAX failed:', status, error);
                    alert('Logout ไม่สำเร็จ ลองใหม่อีกครั้ง');
                });
            }
        });

    });
</script>