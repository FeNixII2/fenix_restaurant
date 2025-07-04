<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <button class="nav-link active w-50 fw-bold" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">เลือกโต๊ะ</button>
        <button class="nav-link w-50 fw-bold" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">สั่งอาหาร</button>

    </div>
</nav>


<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active " id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">

        <div class="text-center" id="tableLoader">
            <div class="spinner-border" role="status"></div>
        </div>
        <div class="contrainer mt-3">
            <div class="row" id="alltable">
            </div>
            <hr>
            <div class="text-warning fw-bold fs-3 mb-3">รายการรอเสิร์ฟ</div>
            <div class="row" id="pendingOrders"></div>
        </div>

    </div>
    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
        <div class="row mt-3">
            <div class="col-12 col-sm-6 col-m-6 col-lg-6 col-xl-6">
                <div class="d-flex justify-content-between gap-5 mb-3">
                    <select id="category" name="category" class="form-select">
                        <option value="">ทั้งหมด</option>
                    </select>
                    <input id="search" class="form-control" placeholder="ค้นหา..." onkeyup="searchMenu()"></input>
                </div>
                <div id="menuWrapper" style="max-height: 80vh; overflow-x: auto;">
                    <div class="row" id="menu"></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-m-6 col-lg-6 col-xl-6">
                <div class="text-center fw-bold fs-4 text-warning mt-3" id="order_type"></div>
                <div id="orderList"></div>
                <div id="previousOrder"></div>
            </div>
        </div>

    </div>
</div>
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">สั่งอาหาร</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
            </div>
            <div class="modal-body">
                <img src="" class="menu-img rounded mb-3" id="modal_image" alt="">
                <div class="d-flex justify-content-between">
                    <div class="fs-4 fw-bold" id="modal_name"></div>
                    <div class="fs-4 fw-bold" id="modal_price"></div>

                </div>
                <div id="modal_stock"></div>
                <div class="mb-3" id="modal_details"></div>
                <div class="d-flex justify-content-center gap-3">
                    <button id="btn-minus" class="btn btn-warning text-white"><i class="fa-solid fa-minus"></i></button>
                    <input type="number" class="form-control w-25 text-right" value="1" id="qty-input">
                    <button id="btn-plus" class="btn btn-warning text-white"><i class="fa-solid fa-plus"></i></button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-warning text-white" id="btn-add-to-order">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<script>
    let menuList = [];
    let filteredMenu = [];
    let orderList = [];
    let selectedMenu = null;
    let editIndex = null;
    $(document).ready(function() {
        getTable();
        getCategory();
        getMenu();
        getServe();
    })

    function getServe() {
        $.ajax({
            url: '/api/api_order.php',
            method: 'GET',
            data: {
                case: 'getOrder_serve'
            },
            dataType: 'json',
            success: function(response) {
                const orders = response.data;

                const groupedByTable = {};

                orders.forEach(order => {
                    const tableId = order.table_id;
                    const serveType = parseInt(order.serve_type);
                    const status = parseInt(order.status);

                    if ((serveType === 1 && status === 2) || serveType === 0) {
                        if (!groupedByTable[tableId]) {
                            groupedByTable[tableId] = [];
                        }
                        groupedByTable[tableId].push(order);
                    }
                });

                renderGroup(groupedByTable, 'pendingOrders');
            }
        });
    }

    function renderGroup(group, containerId) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';

        Object.keys(group).forEach(tableId => {
            const firstOrder = group[tableId][0];
            const tableName = firstOrder.table_name || `กลับบ้าน`;
            const billCode = firstOrder.bill_code || '';
            const time = firstOrder.bill_create || '';
            let buttonText = (firstOrder.table_id == 999) ? 'เรียบร้อยทั้งหมด' : 'เสิร์ฟทั้งโต๊ะ';

            // <button class="btn btn-outline-warning mt-2 w-100" onclick="serveWholeTable(this)">${buttonText}</button>

            const card = document.createElement('div');
            card.className = 'col-12 col-sm-12 col-lg-3 col-xl-3';
            card.innerHTML = `
            <div class="card mb-3" data-table="${tableId}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="fw-bold">#${billCode}</div>
                        </div>
                        <div class="fw-bold fs-4">${tableName}</div>
                    </div>
                    <ul class="list-group"></ul>
                </div>
            </div>
        `;

            const ul = card.querySelector('ul');

            const groupedOrders = {};
            group[tableId].forEach(order => {
                const key = order.menu_id;
                if (!groupedOrders[key]) {
                    groupedOrders[key] = {
                        ...order
                    };
                } else {
                    groupedOrders[key].quantity += order.quantity;
                }
            });

            Object.values(groupedOrders).forEach(order => {
                const li = document.createElement('li');
                li.className = `list-group-item d-flex justify-content-between align-items-center`;
                li.dataset.orderId = order.id;
                li.dataset.menuId = order.menu_id;
                li.dataset.quantity = order.quantity;
                li.dataset.billId = order.bill_id;
                li.dataset.tableId = order.table_id;


                let buttonText = (order.table_id == 999) ? 'เรียบร้อย' : 'เสิร์ฟ';
                let buttonHTML = `<button class="btn btn-sm  btn-warning text-white" onclick="serveOrder(this)">${buttonText}</button>`;

                li.innerHTML = `
                <div>
                    ${order.name} x${order.quantity}
                </div>
                <div>${buttonHTML}</div>
            `;

                ul.appendChild(li);
            });

            container.appendChild(card);
        });
    }

    // ฟังก์ชันกดเสิร์ฟรายการเดี่ยว
    function serveOrder(btn) {
        const li = btn.closest('li');
        const menu_id = li.dataset.menuId;
        const billId = li.dataset.billId;

        $.ajax({
            url: '/api/api_order.php',
            method: 'POST',
            data: {
                case: 'serveOrder',
                menu_id: menu_id,
                bill_id: billId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('สำเร็จ', 'เสิร์ฟรายการแล้ว', 'success');
                } else {
                    Swal.fire('ผิดพลาด', 'เสิร์ฟรายการไม่สำเร็จ', 'error');
                }
                getServe(); // reload ใหม่
            }
        });
    }




    function getCategory() {
        $.ajax({
            url: '/api/api_manage_menu.php',
            method: 'GET',
            data: {
                case: 'getCategory'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    let categorySelect = $('#category');
                    categorySelect.empty(); // เคลียร์ของเดิม
                    categorySelect.append(`<option value="">ทั้งหมด</option>`); // ตัวเลือกแรก

                    response.data.forEach(cat => {
                        categorySelect.append(`
                        <option value="${cat.id}">${cat.name}</option>
                    `);
                    });
                }
            }
        })
    }

    $('#category').on('change', function(e) {
        const selectedCategory = $(this).val(); // ได้ค่า category ที่เลือก

        if (selectedCategory === "") {
            // ถ้าเลือก "ทั้งหมด" ให้แสดงทั้งหมด
            filteredMenu = menuList;
        } else {
            // กรองตาม category_id
            filteredMenu = menuList.filter(item => item.category_id == selectedCategory);
        }

        writeMenu(filteredMenu);
    })

    let searchTimeout;

    function searchMenu() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const keyword = document.getElementById("search").value.trim().toLowerCase();
            const results = filteredMenu.filter(item =>
                item.name.toLowerCase().includes(keyword) ||
                item.price.toString().includes(keyword) ||
                item.category_name.toLowerCase().includes(keyword) ||
                item.details.toLowerCase().includes(keyword)
            );
            writeMenu(results);
        }, 600); // รอ 300ms หลังจากหยุดพิมพ์
    }

    function getMenu() {
        $.ajax({
            url: '/api/api_order.php',
            method: 'GET',
            data: {
                case: 'getMenu'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    menuList = response.data;
                    filteredMenu = response.data;
                    writeMenu(response.data);
                }
            }
        })
    }

    function getTable() {

        $.ajax({
            url: '/api/api_order.php',
            method: 'GET',
            data: {
                case: 'get_table'
            },
            dataType: 'json',
            beforeSend: function() {
                $('#tableLoader').removeClass('d-none');
            },
            success: function(response) {
                if (response.status === 'success') {
                    let listtable = $('#alltable');
                    listtable.empty();
                    if (response.data.length === 0) {
                        listtable.append(`
                                    <div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-3">
                                            <div class="card shadow-sm h-100 cursor-pointer table-card bg-dark" onclick="takeAway('999', 'กลับบ้าน', 0)">
                                                <div class="card-body h-100">
                                                    <div class="d-none d-lg-flex align-items-center justify-content-center h-100">
                                                        <div class="me-3">
                                                            <span class="d-inline-flex bg-white align-items-center justify-content-center  text-white  rounded-circle" style="width: 40px; height: 40px;padding: 2.5rem">
                                                                <i class="fa-solid fa-house table-icon text-dark"></i>
                                                            </span>

                                                        </div>
                                                        <div>
                                                            <h5 class="mb-0 fw-bold text-white">กลับบ้าน</h5>
                                                        </div>
                                                    </div>

                                                    <div class="d-lg-none">
                                                        <h5 class="fw-bold text-white">กลับบ้าน</h5>
                                                        <span class="d-inline-flex align-items-center justify-content-center  bg-white  text-white  rounded-circle" style="width: 40px; height: 40px;padding: 2.5rem">
                                                            <i class="fa-solid fa-house table-icon text-dark"></i>
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    `);
                    } else {
                        listtable.append(`
                        <div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-3">
                                <div class="card shadow-sm h-100 cursor-pointer table-card bg-dark" onclick="takeAway('999', 'กลับบ้าน', 0)">
                                    <div class="card-body h-100">
                                        <div class="d-none d-lg-flex align-items-center justify-content-center h-100">
                                            <div class="me-3">
                                                <span class="d-inline-flex bg-white align-items-center justify-content-center  text-white  rounded-circle" style="width: 40px; height: 40px;padding: 2.5rem">
                                                    <i class="fa-solid fa-house table-icon text-dark"></i>
                                                </span>

                                            </div>
                                            <div>
                                                <h5 class="mb-0 fw-bold text-white">กลับบ้าน</h5>
                                            </div>
                                        </div>

                                        <div class="d-lg-none">
                                            <h5 class="fw-bold text-white">กลับบ้าน</h5>
                                            <span class="d-inline-flex align-items-center justify-content-center  bg-white  text-white  rounded-circle" style="width: 40px; height: 40px;padding: 2.5rem">
                                                <i class="fa-solid fa-house table-icon text-dark"></i>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        `)
                        response.data.forEach(function(table) {
                            const stateText = table.table_state === 1 ? 'มีลูกค้า' : 'ว่าง';
                            const stateColor = table.table_state === 1 ? 'bg-warning' : 'bg-white';
                            const iconColor = table.table_state === 1 ? 'text-dark' : 'text-dark';
                            const textColor = table.table_state === 1 ? 'text-warning' : 'text-white';


                            listtable.append(`
                                    <div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-3">
                                        <div class="card shadow-sm h-100 cursor-pointer table-card bg-dark" onclick="selectTable(${table.id}, '${table.name}', ${table.table_state})">

                                            <span class="badge ${stateColor} status-badge  text-dark"> ${stateText}</span>

                                            <div class="card-body h-100">
                                                <div class="d-none d-lg-flex align-items-center justify-content-center h-100">
                                                    <div class="me-3">
                                                        <span class="d-inline-flex align-items-center justify-content-center ${stateColor} text-white  rounded-circle" style="width: 40px; height: 40px;padding: 2.5rem">
                                                            <i class="fa-solid fa-chair table-icon ${iconColor} "></i>
                                                        </span>
                                                        
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-0 fw-bold ${textColor}">${table.name}</h5>
                                                    </div>
                                                </div>

                                                <div class="d-lg-none">
                                                    <h5 class="fw-bold ${textColor}">${table.name}</h5>
                                                     <span class="d-inline-flex align-items-center justify-content-center ${stateColor} text-white  rounded-circle" style="width: 40px; height: 40px;padding: 2.5rem">
                                                            <i class="fa-solid fa-chair table-icon ${iconColor} "></i>
                                                        </span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                `);
                        });
                    }
                }
            },
            complete: function() {
                $('#tableLoader').addClass('d-none');
            },
        })
    }

    function writeMenu(data) {
        let menu = $('#menu');
        menu.empty();
        if (data.length === 0) {
            menu.append(`<div  class="text-center">ไม่มีเมนู</div>`);
        } else {
            data.forEach(function(data) {
                if (data.stock === 0) {
                    menu.append(`
                                <div class="col-6 col-sm-6 col-m-4 col-lg-4 mb-3">
                                    <div class="h-100 menu-card position-relative bg-light opacity-50" style="cursor: not-allowed;">
                                        <div class="sold-out-banner">หมด</div>
                                        <img src="${data.path ? data.path : '../assets/images/picture.png'}" alt="" class="rounded menu-img" />
                                        <div class="p-1">
                                            <div class="fs-4 fw-bold text-truncate">${data.name}</div>
                                            <div>${data.details ? data.details : '-'}</div>
                                            <div class="fw-bold">฿${data.price}</div>
                                        </div>
                                    </div>
                                </div>
                            `);
                } else {
                    menu.append(`
                        <div class="col-6 col-sm-6 col-m-4 col-lg-4 mb-3">
                            <div class="h-100 menu-card" id="cardMenu" data-menu='${JSON.stringify(data)}' style="cursor: pointer;">
                                <img src="${data.path ? data.path : '../assets/images/picture.png'}" alt="" class="rounded menu-img" />
                                <div class="p-1">
                                <div class="fs-4 fw-bold text-truncate">
                                    ${data.name}
                                </div>
                                <div>${data.details ? data.details : '-'}</div>
                                <div class=" fw-bold ">฿${data.price}</div>
                                </div>
                            </div>
                        </div>
                        `);
                }

            });
        }
    }


    $(document).on('click', '#cardMenu', function(e) {
        const Modal = new bootstrap.Modal(document.getElementById('orderModal'));
        Modal.show();
        const menuData = $(this).data('menu'); // ดึงข้อมูลจาก data-menu
        selectedMenu = menuData;
        $('#modal_image').attr('src', menuData.path)
        $('#modal_name').text(menuData.name)
        $('#modal_details').text(menuData.details)
        $('#modal_price').text('฿' + menuData.price)
        $('#modal_stock').text('คงเหลือ ' + menuData.stock)


        const $qtyInput = $('#qty-input');
        const $btnMinus = $('#btn-minus');
        const $btnPlus = $('#btn-plus');

        $('#qty-input').val(1);

        $btnPlus.off('click').on('click', function() {
            let qty = parseInt($qtyInput.val()) || 1;
            if (selectedMenu && qty < selectedMenu.stock) {
                $qtyInput.val(qty + 1);
            }
        });

        $btnMinus.off('click').on('click', function() {
            let qty = parseInt($qtyInput.val()) || 1;
            if (qty > 1) {
                $qtyInput.val(qty - 1);
            }
        });

        // ป้องกันกรอกนอกช่วงใน input เอง
        $qtyInput.off('input').on('input', function() {
            let val = parseInt($(this).val()) || 1;
            if (val < 1) val = 1;
            if (val > menuData.stock) val = menuData.stock;
            $(this).val(val);
        });
    });


    function selectTable(id, name, state) {
        if (state != 0) {
            $('#order_type').data('table-id', id);
            $('#order_type').text(name);
            const tabTrigger = new bootstrap.Tab(document.querySelector('#nav-profile-tab'));
            tabTrigger.show();
            getPreviousorder(id);
        } else {
            Swal.fire({
                title: `คุณต้องการเปิดบิล ${name} ?`,
                showDenyButton: true,
                confirmButtonText: "ใช่",
                denyButtonText: `ยกเลิก`
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/api_order.php',
                        method: 'POST',
                        data: {
                            case: 'openBill',
                            table_id: id,
                            employeeid: <?php echo $_SESSION['user']['id'] ?>
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                $('#order_type').data('table-id', id);
                                $('#order_type').text(name);
                                const tabTrigger = new bootstrap.Tab(document.querySelector('#nav-profile-tab'));
                                tabTrigger.show();
                                getPreviousorder(id);
                            } else {
                                Swal.fire('error', 'เกิดข้อผิดพลาด', 'error')
                            }

                        }
                    })

                }
            })
        }
    }

    function takeAway(id, name, state) {
        $.ajax({
            url: '/api/api_order.php',
            method: 'GET',
            data: {
                case: 'getBilltakeAway'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    if (response.data.length != 0) {
                        $('#order_type').data('table-id', id);
                        $('#order_type').text(name);
                        const tabTrigger = new bootstrap.Tab(document.querySelector('#nav-profile-tab'));
                        tabTrigger.show();
                        getPreviousorder(id);
                    } else {
                        Swal.fire({
                            title: `คุณต้องการเปิดบิล ${name} ?`,
                            showDenyButton: true,
                            confirmButtonText: "ใช่",
                            denyButtonText: `ยกเลิก`
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: '/api/api_order.php',
                                    method: 'POST',
                                    data: {
                                        case: 'openBill',
                                        table_id: id,
                                        employeeid: <?php echo $_SESSION['user']['id'] ?>
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response.status === 'success') {
                                            $('#order_type').data('table-id', id);
                                            $('#order_type').text(name);
                                            const tabTrigger = new bootstrap.Tab(document.querySelector('#nav-profile-tab'));
                                            tabTrigger.show();
                                            getPreviousorder(id);
                                        } else {
                                            Swal.fire('error', 'เกิดข้อผิดพลาด', 'error')
                                        }

                                    }
                                })

                            }
                        })
                    }

                } else {
                    Swal.fire('error', 'เกิดข้อผิดพลาด', 'error')
                }

            }
        })
    }

    $('#btn-add-to-order').on('click', function() {
        // const qty = parseInt($('#qty-input').val()) || 1;
        // addToOrder();

        // const modal = bootstrap.Modal.getInstance(document.getElementById('orderModal'));
        // modal.hide(); // ปิด modal หลังเพิ่ม

        const qty = parseInt($('#qty-input').val()) || 1;

        if (editIndex !== null) {
            // แก้ไข
            orderList[editIndex].qty = qty;
            editIndex = null;
        } else {
            // เพิ่มใหม่
            const existingItem = orderList.find(item => item.id === selectedMenu.id);
            if (existingItem) {
                existingItem.qty = Math.min(existingItem.qty + qty, selectedMenu.stock);
            } else {
                orderList.push({
                    ...selectedMenu,
                    qty: qty
                });
            }
        }

        const modal = bootstrap.Modal.getInstance(document.getElementById('orderModal'));
        modal.hide();
        renderOrderList();
    });

    function addToOrder() {
        const qty = parseInt($('#qty-input').val()) || 1;

        if (!selectedMenu) return;

        // ตรวจสอบว่าเมนูนี้มีอยู่ใน orderList แล้วหรือยัง
        const existingItem = orderList.find(item => item.id === selectedMenu.id);

        if (existingItem) {
            // ถ้ามีอยู่แล้ว → บวกจำนวน (แต่ไม่เกิน stock)
            const newQty = existingItem.qty + qty;
            existingItem.qty = Math.min(newQty, selectedMenu.stock); // จำกัดไม่เกิน stock
        } else {
            // ถ้ายังไม่มี → เพิ่มเข้าไปใหม่
            orderList.push({
                ...selectedMenu,
                qty: qty
            });
        }

        // ปิด modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('orderModal'));
        modal.hide();
        renderOrderList();
    }


    function renderOrderList() {

        const container = $('#orderList');

        if (orderList.length === 0) {
            container.empty();
        } else {
            container.empty();
            orderList.forEach((item, index) => {
                container.append(`
            <div class="d-flex justify-content-between border-bottom py-2">
                <div>${item.name} x ${item.qty} ฿${item.price}</div>
                <div>
                <div  class="btn btn-warning text-white btn-editorder" data-index="${index}"><i class="fa-solid fa-pencil"></i></div>
                <div  class="btn btn-danger btn-deleteorder"  data-index="${index}"><i class="fa-regular fa-trash-can"></i></div>
                </div>
                
            </div>
        `);
            });

            container.append(`
            <input type="hidden" name="total_amount" id="total_amount">
            <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="fw-bold"id="orderTotal"></div>
            <div class="btn btn-warning text-white" id="confirm-order" >สั่งอาหาร</div>
            </div>
            `);

            const total = orderList.reduce((sum, item) => sum + item.price * item.qty, 0);
            $('#orderTotal').text(`รวมทั้งหมด: ฿${total}`);
            $('#total_amount').val(total);
        }

    }

    $(document).on('click', '.btn-editorder', function() {
        editIndex = $(this).data('index');
        const item = orderList[editIndex];
        selectedMenu = item;

        const Modal = new bootstrap.Modal(document.getElementById('orderModal'));
        Modal.show();

        $('#modal_image').attr('src', item.path || '');
        $('#modal_name').text(item.name);
        $('#modal_details').text(item.details || '-');
        $('#modal_price').text('฿' + item.price);
        $('#qty-input').val(item.qty);

        // setup ปุ่ม +/- ใหม่
        const $qtyInput = $('#qty-input');
        const $btnMinus = $('#btn-minus');
        const $btnPlus = $('#btn-plus');

        $btnPlus.off('click').on('click', function() {
            let qty = parseInt($qtyInput.val()) || 1;
            if (selectedMenu && qty < selectedMenu.stock) {
                $qtyInput.val(qty + 1);
            }
        });

        $btnMinus.off('click').on('click', function() {
            let qty = parseInt($qtyInput.val()) || 1;
            if (qty > 1) {
                $qtyInput.val(qty - 1);
            }
        });

        $qtyInput.off('input').on('input', function() {
            let val = parseInt($(this).val()) || 1;
            if (val < 1) val = 1;
            if (val > selectedMenu.stock) val = selectedMenu.stock;
            $(this).val(val);
        });
    });

    $(document).on('click', '.btn-deleteorder', function() {
        const index = $(this).data('index');
        orderList.splice(index, 1); // ลบรายการที่ตำแหน่งนั้น
        renderOrderList();
    });

    $(document).on('click', '#confirm-order', function(e) {
        e.preventDefault();

        const tableId = $('#order_type').data('table-id');
        if (!tableId) {
            Swal.fire('error', 'กรุณาเลือกโต๊ะ', 'error')
            return;
        }

        Swal.fire({
            title: `คุณต้องการสั่งอาหาร ?`,
            showDenyButton: true,
            confirmButtonText: "ใช่",
            denyButtonText: `ยกเลิก`
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/api_order.php',
                    method: 'POST',
                    data: {
                        case: 'orderMenu',
                        orders: JSON.stringify(orderList),
                        table_id: tableId,
                        total_amount: $('#total_amount').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('success', 'สั่งอาหารสำเร็จ', 'success')
                            orderList = [];
                            renderOrderList();
                            getCategory();
                            getMenu();
                            getPreviousorder(tableId);
                            getServe();
                        } else {
                            Swal.fire('error', 'เกิดข้อผิดพลาด', 'error')
                        }
                    }
                });
            }
        })




    });

    $('#nav-home-tab').on('click', function() {
        getTable();
    })

    $('#nav-home-tab').on('click', function() {
        getCategory();
        getMenu();
    })

    function getPreviousorder(table_id) {
        $.ajax({
            url: '/api/api_order.php',
            method: 'GET',
            data: {
                case: 'get_previousOrder',
                table_id: table_id
            },
            dataType: 'json',
            success: function(response) {
                let previous = $('#previousOrder');
                previous.empty();

                if (response.data.length === 0) {
                    previous.html(`
                    <hr>
                    <div class="text-muted text-center fw-bold fs-4">ไม่มีรายการก่อนหน้า</div>`);
                    return;
                }

                previous.html(`
                <hr>
                <div class="text-center fw-bold fs-4 text-warning">รายการก่อนหน้า</div>`);

                // 👇 รวมชื่อสินค้าที่ซ้ำกัน
                const groupedItems = {};
                response.data.forEach(item => {
                    if (!groupedItems[item.name]) {
                        groupedItems[item.name] = {
                            name: item.name,
                            quantity: item.quantity,
                            price: item.price
                        };
                    } else {
                        groupedItems[item.name].quantity += item.quantity;
                    }
                });

                let totalAll = 0;

                // 👇 แสดงผลแต่ละรายการที่รวมแล้ว
                Object.values(groupedItems).forEach(item => {
                    const total = item.price * item.quantity;
                    totalAll += total;
                    previous.append(`
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div>${item.name} x ${item.quantity}</div>
                        <div>฿${total}</div>
                    </div>
                `);
                });

                previous.append(`
                <div class="text-end fw-bold pt-2 border-top mt-2">รวมทั้งหมด: ฿${totalAll}</div>
            `);
            }
        });
    }


    $(document).on('click', '.btn-cancelTable', function() {
        const table_id = $(this).data('table_id');
        $.ajax({
            url: '/api/api_order.php',
            method: 'POST',
            data: {
                case: 'cancleBill',
                table_id: table_id
            },
            dataType: 'json',
            success: function(response) {
                Swal.fire('success', response.message, 'success')
            }
        })

    })
</script>