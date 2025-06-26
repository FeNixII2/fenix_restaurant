<div class="row g-3" id="billContainer"></div>

<!-- Offcanvas รายละเอียดบิล -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="billDetailCanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="billDetailTitle">รายละเอียดบิล</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div id="billDetailContent"></div>
        <button class="btn btn-success w-100 mt-3" id="checkoutBtn">💵 คิดเงิน</button>
    </div>
</div>

<!-- Modal ใบเสร็จ -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptModalLabel">ใบเสร็จ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="receiptContent">
                กำลังโหลด...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<script>
    let mergedBillsGlobal = [];

    $(document).ready(function() {
        getBill();
    });

    function getBill() {
        $.ajax({
            url: '/api/api_cashier.php',
            method: 'GET',
            data: {
                case: 'getBills'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const bills = response.data; // ข้อมูลบิล
                    const orders = response.data2; // ข้อมูลออร์เดอร์

                    // จัดกลุ่ม orders ตาม bill_id
                    const ordersGrouped = {};
                    orders.forEach(order => {
                        if (!ordersGrouped[order.bill_id]) ordersGrouped[order.bill_id] = [];
                        ordersGrouped[order.bill_id].push(order);
                    });

                    // รวม orders เข้า bills
                    mergedBillsGlobal = bills.map(bill => ({
                        ...bill,
                        orders: ordersGrouped[bill.id] || []
                    }));

                    renderBillCards(mergedBillsGlobal);
                } else {
                    alert('โหลดข้อมูลบิลไม่สำเร็จ');
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการโหลดข้อมูลบิล');
            }
        });
    }

    function renderBillCards(mergedBills) {
        const container = document.getElementById('billContainer');
        container.innerHTML = '';

        mergedBills.forEach(bill => {


            if (bill.status === 1) {


                const allPending = bill.orders.every(order => order.status === 0);
                const timeText = formatDateTimeThai(bill.create_at);
                const totalAmount = bill.orders.reduce((sum, o) => sum + parseFloat(o.price) * o.quantity, 0);
                const tableId = bill.table_id;

                const card = document.createElement('div');
                card.className = 'col-md-6 col-lg-4';
                card.innerHTML = `
                <div class="card shadow-sm rounded-4">
                    <div class="card-body">
                        <h5>${bill.name} • ${bill.bill_code}</h5>
                        <p class="text-muted small">เปิดเมื่อ ${timeText}</p>
                        <p class="mb-2">ยอดรวม: <strong>${totalAmount.toFixed(2)} ฿</strong></p>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="viewBillDetailById(${bill.id},${tableId})">🔍 ดูรายละเอียด</button>
                            ${allPending ? `<button class="btn btn-outline-danger" onclick="cancelBill(${bill.id},${tableId})">❌ ยกเลิกบิล</button>` : ''}
                        </div>
                    </div>
                </div>
            `;
                container.appendChild(card);
            }
        });
    }

    function viewBillDetailById(billId, tableId) {
        const bill = mergedBillsGlobal.find(b => b.id === billId);
        if (!bill) {
            alert('ไม่พบข้อมูลบิล');
            return;
        }
        viewBillDetail(bill, tableId);
    }

    function viewBillDetail(bill, table_id) {
        const content = document.getElementById('billDetailContent');
        const title = document.getElementById('billDetailTitle');
        const checkoutBtn = document.getElementById('checkoutBtn');

        title.textContent = `บิล ${bill.bill_code} (${bill.name})`;

        // ✅ กรองรายการที่ไม่ถูกยกเลิก
        const validOrders = bill.orders.filter(order => order.status !== 4);

        // ✅ คำนวณเฉพาะรายการที่ไม่ยกเลิก
        let totalAmount = 0;
        validOrders.forEach(order => {
            totalAmount += parseFloat(order.price) * order.quantity;
        });

        const vatRate = 7;
        const vat = totalAmount * vatRate / 107;
        const netTotal = totalAmount - vat;

        // ✅ สร้าง HTML เฉพาะรายการที่ไม่ถูกยกเลิก
        const items = validOrders.map(order => {
            const isEditable = order.status === 0;
            const itemTotal = parseFloat(order.price) * order.quantity;

            return `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    ${order.name} x${order.quantity}
                    <div class="text-muted small">${itemTotal.toFixed(2)} ฿</div>
                </div>
                ${isEditable
                    ? `<button class="btn btn-sm btn-outline-danger" onclick="deleteOrder(${order.id}, ${bill.id})">🗑️</button>`
                    : `<span class="badge bg-secondary">ทำแล้ว</span>`}
            </li>
        `;
        }).join('');

        content.innerHTML = `
        <ul class="list-group mb-3">${items}</ul>
        <div class="text-end">ยอดสุทธิ: ${netTotal.toFixed(2)} ฿</div>
        <div class="text-end">VAT 7%: ${vat.toFixed(2)} ฿</div>
        <h5 class="text-end">รวมทั้งสิ้น: <strong>${totalAmount.toFixed(2)} ฿</strong></h5>
    `;

        console.log('totalAmount', totalAmount);

        if (totalAmount === 0) {
            $('#checkoutBtn').addClass('disabled').prop('disabled', true);
        } else {
            $('#checkoutBtn').removeClass('disabled').prop('disabled', false);
        }

        checkoutBtn.onclick = function() {
            checkoutBill(bill.id, table_id);
        };

        const offcanvas = new bootstrap.Offcanvas(document.getElementById('billDetailCanvas'));
        offcanvas.show();
    }

    function formatDateTimeThai(datetimeStr) {
        if (!datetimeStr) return '';

        const date = new Date(datetimeStr);
        date.setHours(date.getHours() + 7); // บวกเวลาไทย (UTC+7)

        const day = date.getDate();
        const monthNames = [
            'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
            'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
        ];
        const month = monthNames[date.getMonth()];
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');

        return `${day} ${month} ${hours}:${minutes} น.`;
    }

    function checkoutBill(billId, table_id) {
        console.log(billId, table_id);


        $.ajax({
            url: '/api/api_cashier.php',
            method: 'POST',
            data: {
                case: 'getReceipt',
                bill_id: billId,
                table_id: table_id
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('success', response.message, 'success')
                    $('#billDetailCanvas').offcanvas('hide')
                    getBill();
                    // const receiptHtml = generateReceiptHtml(response.data);
                    // $('#receiptContent').html(receiptHtml);

                    // const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
                    // receiptModal.show();
                } else {
                    alert('โหลดใบเสร็จไม่สำเร็จ');
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการโหลดใบเสร็จ');
            }
        });
    }

    function generateReceiptHtml(data) {
        let ordersList = data.orders.map(order => `
            <li class="d-flex justify-content-between">
                <span>${order.name} x${order.quantity}</span>
                <span>${parseFloat(order.price).toFixed(2)} ฿</span>
            </li>
        `).join('');

        return `
            <h5>บิล: ${data.bill.bill_code}</h5>
            <p>โต๊ะ: ${data.bill.name}</p>
            <p>วันที่: ${formatDateTimeThai(data.bill.create_at)}</p>

            <ul class="list-unstyled border-top border-bottom py-2">
                ${ordersList}
            </ul>

            <div class="d-flex justify-content-between">
                <strong>ยอดรวม</strong>
                <span>${data.total.toFixed(2)} ฿</span>
            </div>
            <div class="d-flex justify-content-between">
                <strong>VAT 7%</strong>
                <span>${data.vat.toFixed(2)} ฿</span>
            </div>
            <div class="d-flex justify-content-between fw-bold fs-5 mt-2">
                <strong>รวมทั้งสิ้น</strong>
                <span>${data.grandTotal.toFixed(2)} ฿</span>
            </div>
        `;
    }

    // ตัวอย่างฟังก์ชันลบ order (ต้อง implement API ให้เหมาะสม)
    function deleteOrder(orderId, billId) {
        if (!confirm('คุณแน่ใจว่าต้องการลบออร์เดอร์นี้?')) return;

        $.ajax({
            url: '/api/api_cashier.php',
            method: 'POST',
            data: {
                case: 'deleteOrder',
                order_id: orderId
            },
            success: function(response) {
                if (response.status === 'success') {
                    alert('ลบออร์เดอร์สำเร็จ');
                    getBill(); // โหลดข้อมูลใหม่
                    // ปิด offcanvas ถ้าเปิดอยู่
                    const offcanvasEl = document.getElementById('billDetailCanvas');
                    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    if (offcanvas) offcanvas.hide();
                } else {
                    alert('ลบออร์เดอร์ไม่สำเร็จ');
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการลบออร์เดอร์');
            }
        });
    }

    // ตัวอย่างฟังก์ชันยกเลิกบิล (ต้อง implement API ให้เหมาะสม)
    function cancelBill(billId, table_id) {
        if (!confirm('คุณแน่ใจว่าต้องการยกเลิกบิลนี้?')) return;

        $.ajax({
            url: '/api/api_cashier.php',
            method: 'POST',
            data: {
                case: 'cancelBill',
                bill_id: billId,
                table_id: table_id
            },
            success: function(response) {
                if (response.status === 'success') {
                    alert('ยกเลิกบิลสำเร็จ');
                    getBill();
                } else {
                    alert('ยกเลิกบิลไม่สำเร็จ');
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการยกเลิกบิล');
            }
        });
    }
</script>