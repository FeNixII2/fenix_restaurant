<!-- <div class="row">
    <div class="col-12 col-sm-12 col-m-12 col-lg-4 col-xl-4">
        <div class="">รับออเดอร์</div>
    </div>
    <div class="col-12 col-sm-12 col-m-12 col-lg-4 col-xl-4">
        <div class="">กำลังทำ</div>
    </div>
    <div class="col-12 col-sm-12 col-m-12 col-lg-4 col-xl-4">
        <div class="">เสร็จ</div>
    </div>
</div> -->

<!-- <div class="container-fluid mt-4">
    <div class="row g-3">

        <div class="col-lg-4">
            <h4 class="text-center">🛎️ รับออเดอร์</h4>
            <div id="pendingOrders">
            </div>
        </div>

        <div class="col-lg-4">
            <h4 class="text-center">👨‍🍳 กำลังทำ</h4>
            <div id="cookingOrders"></div>
        </div>

        <div class="col-lg-4">
            <h4 class="text-center">✅ เสร็จแล้ว</h4>
            <div id="finishedOrders"></div>
        </div>

    </div>
</div> -->

<div class="row g-3">
    <div class="col-lg-4">
        <h4 class="text-center">🛎️ รับออเดอร์</h4>
        <div id="pendingOrders"></div>
    </div>
    <div class="col-lg-4">
        <h4 class="text-center">👨‍🍳 กำลังทำ</h4>
        <div id="cookingOrders"></div>
    </div>
    <div class="col-lg-4">
        <h4 class="text-center">✅ เสร็จแล้ว</h4>
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
                console.log(response.data);

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
                            // แปลงเป็น timestamp (ms)
                            const timeA = Date.parse(a.bill_create.replace(' ', 'T'));
                            const timeB = Date.parse(b.bill_create.replace(' ', 'T'));
                            // ถ้าแปลงไม่ได้ ให้ fallback เป็น 0
                            return (timeB || 0) - (timeA || 0);
                        });
                    });
                });
                console.log(statusGrouped);

                // เรียก render
                renderGroup(statusGrouped[0], 'pendingOrders', 0); // รับออเดอร์
                renderGroup(statusGrouped[1], 'cookingOrders', 1); // กำลังทำ
                renderGroup(statusGrouped[2], 'finishedOrders', 2); // เสร็จแล้ว

            }
        })

    }

    // 2. วาดแต่ละกลุ่ม
    function renderGroup(group, containerId, status) {
        const container = document.getElementById(containerId);
        container.innerHTML = ''; // ล้างเก่าก่อน




        Object.keys(group).forEach(tableId => {
            const firstOrder = group[tableId][0]; // ดึงออเดอร์แรกของโต๊ะนั้น
            const tableName = firstOrder.table_name || `โต๊ะ ${tableId}`;
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
                    ${status === 0 ? `<button class="btn btn-warning mt-2 w-100" onclick="acceptWholeTable(this)">รับทั้งโต๊ะ</button>` : ''}
                    ${status === 1 ? `<button class="btn btn-success mt-2 w-100" onclick="finishWholeTable(this)">เสร็จทั้งหมด</button>` : ''}
                </div>
            </div>
        `;

            const ul = card.querySelector('ul');

            group[tableId].forEach(order => {
                const li = document.createElement('li');
                li.className = `list-group-item d-flex justify-content-between align-items-center ${status === 2 ? 'bg-success text-white' : ''}`;
                li.dataset.orderId = order.id;
                li.dataset.menuId = order.menu_id;

                let buttonHTML = '';
                if (status === 0) {
                    buttonHTML = `<button class="btn btn-sm btn-primary" onclick="acceptSingleOrder(this)">รับ</button>`;
                } else if (status === 1) {
                    buttonHTML = `<button class="btn btn-sm btn-success" onclick="finishOrder(this)">เสร็จแล้ว</button>`;
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
        createdDate.setHours(createdDate.getHours() + 7); // บวกเวลา +7 ชั่วโมง (UTC+7)

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


    function acceptSingleOrder(btn) {
        const li = btn.closest('li');
        const card = btn.closest('.card');
        const tableId = card.getAttribute('data-table');
        const orderId = li.dataset.orderId;
        $.ajax({
            url: '/api/api_kitchen.php',
            method: 'POST',
            data: {
                case: 'update_status_order_to_1',
                order_id: orderId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('success', response.massage, 'success')
                }
            }
        });

        // ตรวจว่ามีการ์ดโต๊ะนี้ในฝั่ง "กำลังทำ" หรือยัง
        let cookingCard = document.querySelector(`#cookingOrders .card[data-table='${tableId}']`);

        if (!cookingCard) {
            cookingCard = card.cloneNode(true);
            cookingCard.querySelector('ul').innerHTML = '';
            cookingCard.querySelector('button.btn-warning')?.remove();

            // เพิ่มปุ่ม "เสร็จทั้งหมด"
            const doneAllBtn = document.createElement('button');
            doneAllBtn.className = 'btn btn-success mt-2 w-100';
            doneAllBtn.textContent = 'เสร็จทั้งหมด';
            doneAllBtn.onclick = () => finishWholeTable(doneAllBtn);
            cookingCard.querySelector('.card-body').appendChild(doneAllBtn);

            document.getElementById('cookingOrders').appendChild(cookingCard);
        }

        // ลบปุ่ม "รับ"
        li.querySelector('button')?.remove();

        // เพิ่มปุ่ม "เสร็จแล้ว" ในเมนูนั้น
        const doneBtn = document.createElement('button');
        doneBtn.className = 'btn btn-sm btn-success';
        doneBtn.textContent = 'เสร็จแล้ว';
        doneBtn.onclick = () => finishOrder(doneBtn);

        const div = document.createElement('div');
        div.appendChild(doneBtn);
        li.appendChild(div);

        // ย้ายเมนูไปการ์ดกำลังทำ
        cookingCard.querySelector('ul').appendChild(li);

        // ถ้าการ์ดเดิมใน "รับออเดอร์" ว่าง → ลบทิ้ง
        if (card.querySelectorAll('ul li').length === 0) {
            card.remove();
        }
    }

    function acceptWholeTable(btn) {
        const card = btn.closest('.card');
        const btns = card.querySelectorAll('button.btn-primary');
        btns.forEach(b => b.click());
        btn.disabled = true;
    }

    function finishOrder(btn) {
        const li = btn.closest('li');
        const card = btn.closest('.card');
        const tableId = card.getAttribute('data-table');
        const orderId = li.dataset.orderId
        $.ajax({
            url: '/api/api_kitchen.php',
            method: 'POST',
            data: {
                case: 'update_status_order_to_2',
                order_id: orderId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('success', response.massage, 'success')
                }
            }
        });

        // ย้าย li ไปการ์ด "เสร็จแล้ว"
        let finishedCard = document.querySelector(`#finishedOrders .card[data-table='${tableId}']`);
        if (!finishedCard) {
            finishedCard = card.cloneNode(true);
            finishedCard.querySelector('ul').innerHTML = '';
            finishedCard.querySelector('button.btn-success')?.remove(); // ลบ "เสร็จทั้งหมด"
            document.getElementById('finishedOrders').appendChild(finishedCard);
        }

        li.querySelector('button')?.remove();
        li.classList.add('bg-success', 'text-white');
        finishedCard.querySelector('ul').appendChild(li);

        // ✅ ตรวจสอบใหม่หลังย้าย li ไปแล้ว
        const ul = card.querySelector('ul');
        if (ul.children.length === 0) {
            card.remove(); // ลบการ์ดกำลังทำเมื่อไม่มีเมนูเหลือ
        }
    }

    function finishWholeTable(btn) {
        const card = btn.closest('.card');
        const allDoneButtons = card.querySelectorAll('button.btn-success');

        allDoneButtons.forEach(btn => {
            if (btn.textContent === 'เสร็จแล้ว') btn.click();
        });

        btn.remove(); // ลบปุ่ม "เสร็จทั้งหมด"
    }
</script>