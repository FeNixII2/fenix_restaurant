<div class="row g-3">
    <div class="col-lg-4">
        <h4 class="text-center border rounded shadow-sm p-3 fw-bold bg-dark text-white">
            <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle p-4" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-bell-concierge"></i>
            </span>
            รับออเดอร์
        </h4>
        <div id="pendingOrders"></div>
    </div>
    <div class="col-lg-4">
        <h4 class="text-center border rounded shadow-sm p-3 fw-bold bg-dark text-white">
            <span class="d-inline-flex align-items-center justify-content-center bg-warning text-white rounded-circle p-4" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-fire-burner"></i>
            </span>
            กำลังทำ
        </h4>
        <div id="cookingOrders"></div>
    </div>
    <div class="col-lg-4">
        <h4 class="text-center border rounded shadow-sm p-3 fw-bold bg-dark text-white">
            <span class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle p-4" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-clipboard-check"></i>
            </span>
            เสร็จแล้ว
        </h4>
        <div id="finishedOrders"></div>
    </div>
</div>

<script>
    $(document).ready(function() {
        getOrder();
        setInterval(getOrder, 10000);
    })

    function getOrder() {
        $.ajax({
            url: '/api/api_kitchen.php',
            method: 'GET',
            data: {
                case: 'getOrder'
            },
            dataType: 'json',
            success: function(response) {
                const orders = response.data;

                const statusGrouped = {
                    0: {}, // pending
                    1: {}, // cooking
                    2: {} // finished
                };

                orders.forEach(order => {
                    const status = parseInt(order.status);
                    const tableId = order.table_id;
                    if (!statusGrouped[status][tableId]) {
                        statusGrouped[status][tableId] = [];
                    }
                    statusGrouped[status][tableId].push(order);
                });

                [0, 1, 2].forEach(status => {
                    Object.keys(statusGrouped[status]).forEach(tableId => {
                        statusGrouped[status][tableId].sort((a, b) => {
                            const timeA = Date.parse(a.bill_create.replace(' ', 'T'));
                            const timeB = Date.parse(b.bill_create.replace(' ', 'T'));
                            return (timeB || 0) - (timeA || 0);
                        });
                    });
                });

                renderGroup(statusGrouped[0], 'pendingOrders', 0);
                renderGroup(statusGrouped[1], 'cookingOrders', 1);
                renderGroup(statusGrouped[2], 'finishedOrders', 2);
            }
        });
    }

    // renderGroup รวมชื่อเมนูกับจำนวนแล้ว
    function renderGroup(group, containerId, status) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';

        Object.keys(group).forEach(tableId => {
            const firstOrder = group[tableId][0];
            const tableName = firstOrder.table_name || `กลับบ้าน`;
            const billCode = firstOrder.bill_code || '';
            const time = firstOrder.bill_create || '';
            const passed = timeAgoThai(time);

            const card = document.createElement('div');
            card.className = 'col-12';
            card.innerHTML = `
                <div class="card mb-3" data-table="${tableId}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-bold">#${billCode}</div>
                                <div class="text-muted small">${passed}</div>
                            </div>
                            <div class="fw-bold fs-4">${tableName}</div>
                        </div>
                        <ul class="list-group"></ul>
                        ${status === 0 ? `<button class="btn btn-primary mt-2 w-100" onclick="acceptWholeTable(this)">รับทั้งโต๊ะ</button>` : ''}
                        ${status === 1 ? `<button class="btn btn-warning text-white mt-2 w-100" onclick="finishWholeTable(this)">เสร็จทั้งหมด</button>` : ''}
                    </div>
                </div>
            `;

            const ul = card.querySelector('ul');

            // รวมเมนูซ้ำกันตาม menu_id และรวม quantity
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
                li.className = `list-group-item d-flex justify-content-between align-items-center ${status === 2 ? 'bg-success text-white' : ''}`;
                li.dataset.orderId = order.id;
                li.dataset.menuId = order.menu_id;
                li.dataset.quantity = order.quantity; // เก็บ quantity ใน dataset
                li.dataset.billId = order.bill_id;
                li.dataset.tableId = order.table_id;

                let buttonHTML = '';
                if (status === 0) {
                    buttonHTML = `<button class="btn btn-sm btn-primary" onclick="acceptSingleOrder(this)">รับ</button>`;
                } else if (status === 1) {
                    buttonHTML = `<button class="btn btn-sm btn-warning text-white" onclick="finishOrder(this)">เสร็จแล้ว</button>`;
                }

                li.innerHTML = `
                    ${order.name} x${order.quantity}
                    <div>${buttonHTML}</div>
                `;

                ul.appendChild(li);
            });

            container.appendChild(card);
        });
    }

    function timeAgoThai(datetimeStr) {
        if (!datetimeStr) return '';
        const createdDate = new Date(datetimeStr);
        createdDate.setHours(createdDate.getHours() + 7); // +7 ชั่วโมง (UTC+7)
        const now = new Date();
        const diffMs = now - createdDate;
        const diffSec = Math.floor(diffMs / 1000);

        if (diffSec < 60) {
            return `ผ่านมา ${diffSec} วินาที`;
        } else if (diffSec < 3600) {
            const minutes = Math.floor(diffSec / 60);
            const seconds = diffSec % 60;
            return `ผ่านมา ${minutes} นาที ${seconds} วินาที`;
        } else if (diffSec < 86400) {
            const hours = Math.floor(diffSec / 3600);
            const minutes = Math.floor((diffSec % 3600) / 60);
            const seconds = diffSec % 60;
            return `ผ่านมา ${hours} ชั่วโมง ${minutes} นาที ${seconds} วินาที`;
        } else {
            const days = Math.floor(diffSec / 86400);
            return `ผ่านมา ${days} วัน`;
        }
    }

    // ส่งจำนวน qty ไปหลังบ้านด้วย
    function acceptSingleOrder(btn, showAlert = true) {
        const li = btn.closest('li');
        const card = btn.closest('.card');

        const billId = li.dataset.billId || li.getAttribute('data-bill-id') || null;
        const menuId = parseInt(li.dataset.menuId);
        const quantityText = li.textContent.match(/x(\d+)/);
        const quantity = quantityText ? parseInt(quantityText[1]) : 1;
        const tableId = parseInt(li.dataset.tableId);
        if (!billId) {
            console.error('ไม่พบ bill_id ใน li');
            return;
        }

        $.ajax({
            url: '/api/api_kitchen.php',
            method: 'POST',
            data: {
                case: 'update_status_order_to_1',
                bill_id: billId,
                menu_id: menuId,
                quantity: quantity
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    if (showAlert) Swal.fire('success', response.message || 'สำเร็จ', 'success');
                } else {
                    if (showAlert) Swal.fire('error', response.message, 'error')
                }
            }
        });

        // หา การ์ด "กำลังทำ" ของโต๊ะนี้
        let cookingCard = document.querySelector(`#cookingOrders .card[data-table='${tableId}']`);

        if (!cookingCard) {
            cookingCard = card.cloneNode(true);
            cookingCard.querySelector('ul').innerHTML = '';
            cookingCard.querySelector('button.btn-primary')?.remove();

            // เพิ่มปุ่ม "เสร็จทั้งหมด"
            const doneAllBtn = document.createElement('button');
            doneAllBtn.className = 'btn btn-warning text-white mt-2 w-100';
            doneAllBtn.textContent = 'เสร็จทั้งหมด';
            doneAllBtn.onclick = () => finishWholeTable(doneAllBtn);
            cookingCard.querySelector('.card-body').appendChild(doneAllBtn);

            document.getElementById('cookingOrders').appendChild(cookingCard);
        }

        // ลบปุ่ม "รับ"
        li.querySelector('button')?.remove();

        // ตรวจสอบว่ามีรายการเมนูนี้ใน cookingCard หรือยัง
        const existingLi = Array.from(cookingCard.querySelectorAll('ul li')).find(item => {
            return parseInt(item.dataset.menuId) === menuId;
        });

        if (existingLi) {
            // ถ้ามีแล้ว ให้รวม quantity
            let existingQuantityText = existingLi.textContent.match(/x(\d+)/);
            let existingQuantity = existingQuantityText ? parseInt(existingQuantityText[1]) : 1;
            let newQuantity = existingQuantity + quantity;

            // อัปเดตจำนวนบน UI
            existingLi.innerHTML = `${li.textContent.split('x')[0].trim()} x${newQuantity} <div><button class="btn btn-sm btn-warning text-white" onclick="finishOrder(this)">เสร็จแล้ว</button></div>`;

            // ถ้า li เดิมมีข้อมูล data ต้องคัดลอกกลับให้ด้วย
            existingLi.dataset.orderId = existingLi.dataset.orderId || li.dataset.orderId; // ถ้าต้องการ
            existingLi.dataset.quantity = newQuantity;

            // ไม่ต้องเพิ่ม li ใหม่
        } else {
            // ยังไม่มี ให้เพิ่ม li ใหม่
            // เพิ่มปุ่ม "เสร็จแล้ว"
            const doneBtn = document.createElement('button');
            doneBtn.className = 'btn btn-sm btn-warning text-white';
            doneBtn.textContent = 'เสร็จแล้ว';
            doneBtn.onclick = () => finishOrder(doneBtn);

            // ลบปุ่ม "รับ" ออกไปแล้วใน li นี้อยู่แล้ว
            const div = document.createElement('div');
            div.appendChild(doneBtn);
            li.appendChild(div);

            cookingCard.querySelector('ul').appendChild(li);
        }

        // ถ้าการ์ดเดิมใน "รับออเดอร์" ว่าง → ลบทิ้ง
        if (card.querySelectorAll('ul li').length === 0) {
            card.remove();
        }
    }


    function acceptWholeTable(btn) {
        const card = btn.closest('.card');
        const liList = card.querySelectorAll('ul li');

        liList.forEach(li => {
            const acceptBtn = li.querySelector('button.btn-primary');
            if (acceptBtn) {
                acceptSingleOrder(acceptBtn, false); // suppress alert
            }
        });

        Swal.fire('success', 'รับออเดอร์ทั้งโต๊ะแล้ว', 'success');
        btn.disabled = true;
    }

    function finishOrder(btn, showAlert = true) {
        const li = btn.closest('li');
        const card = btn.closest('.card');
        const tableId = card.getAttribute('data-table');
        const orderId = li.dataset.orderId;
        const menuId = parseInt(li.dataset.menuId);
        const billId = li.dataset.billId || li.getAttribute('data-bill-id') || null;

        const quantityText = li.textContent.match(/x(\d+)/);
        const quantity = quantityText ? parseInt(quantityText[1]) : 1;


        $.ajax({
            url: '/api/api_kitchen.php',
            method: 'POST',
            data: {
                case: 'update_status_order_to_2',
                bill_id: billId,
                menu_id: menuId,
                quantity: quantity
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {

                    if (showAlert) Swal.fire('success', response.message || 'สำเร็จ', 'success');
                } else {
                    if (showAlert) Swal.fire('error', response.message || 'ผิดพลาด', 'error');
                }
            }
        });

        // หา การ์ด "เสร็จแล้ว" ของโต๊ะนี้
        let finishedCard = document.querySelector(`#finishedOrders .card[data-table='${tableId}']`);

        if (!finishedCard) {
            finishedCard = card.cloneNode(true);
            finishedCard.querySelector('ul').innerHTML = '';
            finishedCard.querySelector('button.btn-warning')?.remove();
            document.getElementById('finishedOrders').appendChild(finishedCard);
        }

        // ลบปุ่ม "เสร็จแล้ว"
        li.querySelector('button')?.remove();

        // ตรวจสอบว่ามีรายการเมนูนี้ใน finishedCard หรือยัง
        const existingLi = Array.from(finishedCard.querySelectorAll('ul li')).find(item => {
            return parseInt(item.dataset.menuId) === menuId;
        });

        if (existingLi) {
            // ถ้ามีแล้ว ให้รวม quantity
            let existingQuantityText = existingLi.textContent.match(/x(\d+)/);
            let existingQuantity = existingQuantityText ? parseInt(existingQuantityText[1]) : 1;
            let newQuantity = existingQuantity + quantity;

            // อัปเดตจำนวนบน UI
            existingLi.innerHTML = `${li.textContent.split('x')[0].trim()} x${newQuantity}`;

            // อัปเดต data attribute quantity
            existingLi.dataset.quantity = newQuantity;

            // เพิ่มคลาสแสดงว่าเสร็จแล้ว
            existingLi.classList.add('bg-success', 'text-white');

            // ไม่ต้องเพิ่ม li ใหม่
        } else {
            // ยังไม่มี ให้เพิ่ม li ใหม่
            li.classList.add('bg-success', 'text-white');
            finishedCard.querySelector('ul').appendChild(li);
        }

        // ลบการ์ด "กำลังทำ" เมื่อไม่มีเมนูเหลือ
        const ul = card.querySelector('ul');
        if (ul.children.length === 0) {
            card.remove();
        }
    }


    function finishWholeTable(btn) {
        const card = btn.closest('.card');
        const allDoneButtons = Array.from(card.querySelectorAll('button.btn-warning'))
            .filter(b => b.textContent.trim() === 'เสร็จแล้ว');

        // กดปุ่มแบบไม่แสดง Swal และไม่หน่วง
        allDoneButtons.forEach(b => finishOrder(b, false));


        Swal.fire('success', 'อัปเดตสถานะทั้งหมดสำเร็จ', 'success');


        btn.remove();
    }
</script>