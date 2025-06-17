    <div id="bdSidebar"
        class="d-flex flex-column 
                    flex-shrink-0 
                    p-3 bg-dark
                    text-white offcanvas-md offcanvas-start
                   "
        style="width:20rem">

        <div class="d-flex justify-content-between align-items-center">
            <a href="#"
                class="navbar-brand fs-3 fw-bold">FenixRestaurant
            </a>
            <button type="button" class="btn-close btn-close-white d-md-none" id="btn-close-sidebar"></button>
        </div>
        <hr>
        <ul class="mynav nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-1">
                <a href="/dashboard">
                    <i class="fa-solid fa-house"></i>
                    หน้าหลัก
                </a>
            </li>

            <li class="nav-item mb-1">
                <a href="/order">
                    <i class="fa-solid fa-bell-concierge"></i>
                    สั่งอาหาร
                    <span class="notification-badge">5</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/kitchen">
                    <i class="fa-solid fa-kitchen-set"></i>
                    งานครัว
                </a>
            </li>
            <li class="sidebar-item  nav-item mb-1">
                <a href="#"
                    class="sidebar-link collapsed d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse"
                    data-bs-target="#settings"
                    aria-expanded="false"
                    aria-controls="settings"
                    id="settingsToggle">
                    <div class=""><i class="fas fa-cog pe-2"></i>
                        <span class="topic">ตั้งค่า </span>
                    </div>
                    <i class="fa-solid fa-caret-down" id="settingsCaret"></i>

                </a>
                <ul id="settings"
                    class="sidebar-dropdown list-unstyled collapse"
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item ">

                        <a href="/manages/manage_menu" class="sidebar-link  ps-4">
                            <i class="fa-solid fa-bowl-rice pe-2"></i>
                            <span class="topic">เมนู</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="/manages/manage_table" class="sidebar-link  ps-4">
                            <i class="fa-solid fa-stroopwafel pe-2"></i>
                            <span class="topic">โต๊ะ</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="/manages/employee" class="sidebar-link  ps-4">
                            <i class="fa-solid fa-users pe-2"></i>
                            <span class="topic">พนักงาน</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <hr>
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-book  me-2"></i>
            <span>
                <h6 class="mt-1 mb-0">
                    Work is life!!
                </h6>
            </span>
        </div>
    </div>

    <script>
        document.getElementById('btn-close-sidebar').addEventListener('click', function() {
            const offcanvasElement = document.getElementById('bdSidebar');
            const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);

            if (offcanvasInstance) {
                offcanvasInstance.hide();
            }
        });

        const toggleLink = document.getElementById('settingsToggle');
        const caretIcon = document.getElementById('settingsCaret');

        toggleLink.addEventListener('click', function() {
            const isCollapsed = toggleLink.classList.contains('collapsed');
            // สลับ icon หลัง animation

            if (isCollapsed) {
                caretIcon.classList.remove('fa-caret-right');
                caretIcon.classList.add('fa-caret-down');

            } else {
                caretIcon.classList.remove('fa-caret-down');
                caretIcon.classList.add('fa-caret-right');
            }

        });
    </script>