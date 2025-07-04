<div class="row g-3 mt-3" id="billContainer"></div>

<!-- Offcanvas รายละเอียดบิล -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="billDetailCanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title text-warning fw-bold" id="billDetailTitle">รายละเอียดบิล</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div id="billDetailContent"></div>
        <button class="btn btn-warning text-white w-100 mt-3" id="checkoutBtn"><i class="fa-solid fa-calculator"></i> คิดเงิน</button>
    </div>
</div>


<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="modal-header">
                <h5 class="modal-title">เลือกวิธีชำระเงิน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3 mb-3" id="paymentOptions">
                    <div class="col-6 col-md-3">
                        <div class="card payment-card selected text-center p-3" data-method="1"> <!-- เงินสด -->
                            <i class="fa-solid fa-money-bill-wave fa-2x mb-2"></i>
                            <div>เงินสด</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card payment-card text-center p-3" data-method="2"> <!-- โอนเงิน -->
                            <i class="fa-solid fa-building-columns fa-2x mb-2"></i>
                            <div>โอนเงิน</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card payment-card text-center p-3" data-method="3"> <!-- QR -->
                            <i class="fa-solid fa-qrcode fa-2x mb-2"></i>
                            <div>QR Code</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card payment-card text-center p-3" data-method="4"> <!-- บัตร -->
                            <i class="fa-solid fa-credit-card fa-2x mb-2"></i>
                            <div>บัตรเครดิต</div>
                        </div>
                    </div>
                </div>

                <div id="paymentDetails" class="border rounded p-3" style="min-height:120px;">
                    <!-- รายละเอียดแสดงที่นี่ -->
                </div>
            </div>

            <div class="modal-footer">
                <button id="confirmPaymentBtn" class="btn btn-primary">ยืนยัน</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>





<h2 class="mb-4 mt-3 fw-bold text-warning">รายการบิล</h2>

<table id="billTable" class="display table ">
    <thead>
        <tr>
            <th>ลำดับ</th>
            <th>เลขบิล</th>
            <th>โต๊ะ</th>
            <th>เริ่มเวลา</th>
            <th>ปิดบิล</th>
            <th>สถานะ</th>
            <th>ยอดรวม</th>
            <th>ชำระ</th>
            <th>ดู</th>
        </tr>
    </thead>
    <tbody id="billBody">
        <!-- JavaScript will populate rows -->
    </tbody>
</table>

<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ใบเสร็จ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="receiptBody">
                <!-- ใส่ข้อมูลบิล และ รายการอาหารที่นี่ -->
            </div>
        </div>
    </div>
</div>


<script>
    let mergedBillsGlobal = [];
    let foodItems, bills;
    $(document).ready(function() {
        getBill();
        getHistoryBill();
    });

    function getHistoryBill() {
        $.ajax({
            url: '/api/api_cashier.php',
            method: 'GET',
            data: {
                case: 'getHistortBill'
            },
            dataType: 'json',
            success: function(response) {
                bills = response.data;
                foodItems = response.data2;
                writeTable(response.data);

            }
        })
    }

    function writeTable(data) {


        if ($.fn.DataTable.isDataTable('#billTable')) {
            $('#billTable').DataTable().destroy();
        }
        const billbody = $('#billBody');
        billbody.empty();


        data.forEach((bill, index) => {
            const statusBadge = bill.status === 0 ?
                '<span class="badge bg-success">ชำระแล้ว</span>' :
                bill.status === 2 ?
                '<span class="badge bg-danger">ยกเลิก</span>' :
                '<span class="badge bg-warning text-white">รอดำเนินการ</span>';

            billbody.append(`
          <tr class="align-middle">
            <td >${index + 1}</td>
            <td >${bill.bill_code}</td>
            <td >${bill.name ? bill.name : 'กลับบ้าน'}</td>
            <td >${bill.create_at}</td>
            <td >${bill.close_at || '-'}</td>
            <td >${statusBadge}</td>
            <td>${bill.total_amount} ฿</td>
            <td>${bill.name_payment ? bill.name_payment:'-'}</td>
             <td><button class="btn btn-sm btn-warning text-white view-bill-btn" data-billdata="${JSON.stringify(bill).replace(/"/g, '&quot;')}"><i class="fa-solid fa-magnifying-glass"></i></button></td>
          </tr>
        `);
        });

        $('#billTable').DataTable({
            responsive: true,
            scrollX: false,
            autoWidth: false,
            dom: `
                <'row mb-2 '
                <'col-md-6 d-flex align-items-center'B>
                <'col-md-6 text-end'f>
                >
                <'row'
                <'col-12'tr>
                >
                <'row mt-2'
                <'col-md-5'i>
                <'col-md-7 text-end'p>
                >`,
            buttons: [{
                    extend: 'copy',
                    className: 'btn btn-dark'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-dark'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-dark'
                },
                {
                    extend: 'print',
                    className: 'btn btn-dark'
                }
            ],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            language: {
                lengthMenu: "แสดง _MENU_ รายการ",
                search: "ค้นหา:",
                info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                paginate: {
                    next: "ถัดไป",
                    previous: "ก่อนหน้า"
                },
                zeroRecords: "ไม่พบข้อมูล",
                infoEmpty: "ไม่มีข้อมูล",
            }
        });
    }

    $(document).on('click', '.view-bill-btn', function() {
        const billStr = $(this).attr('data-billdata').replace(/&quot;/g, '"');
        const bill = JSON.parse(billStr);
        const items = foodItems.filter(item => item.bill_id === bill.id);

        const statusBadge = bill.status === 0 ?
            '<span class="badge bg-success">ชำระแล้ว</span>' :
            bill.status === 2 ?
            '<span class="badge bg-danger">ยกเลิก</span>' :
            '<span class="badge bg-warning text-dark">รอดำเนินการ</span>';

        // ✅ รวมชื่อซ้ำ
        const groupedItems = {};
        items.filter(item => item.status != 4).forEach(item => {
            if (!groupedItems[item.name]) {
                groupedItems[item.name] = {
                    quantity: 0,
                    price: item.price
                };
            }
            groupedItems[item.name].quantity += item.quantity;
        });

        let idx = 0;
        let itemRows = Object.entries(groupedItems).map(([name, data]) => {
            idx++;
            return `
            <tr>
                <td>${idx}</td>
                <td>${name}</td>
                <td>${data.quantity}</td>
                <td>${data.price}</td>
                <td>${(data.quantity * data.price).toFixed(2)}</td>
            </tr>
        `;
        }).join('');

        $('#receiptBody').html(`
        <p><strong>รหัสบิล:</strong> ${bill.bill_code}</p>
        <p><strong>พนักงานเสิร์ฟ:</strong> ${bill.order_fname} ${bill.order_lname}</p>
        <p><strong>พนักงานคิดเงิน:</strong> ${bill.cashier_fname ? bill.cashier_fname : "" }${bill.cashier_lname ? bill.cashier_lname : ""} </p>
        <p><strong>เวลาเปิดบิล:</strong> ${bill.create_at}</p>
        <p><strong>เวลาเช็คบิล:</strong> ${bill.close_at || '-'}</p>
        <p><strong>สถานะ:</strong> ${statusBadge} </p>
        <hr>
        <table class="table table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>ชื่ออาหาร</th>
                <th>จำนวน</th>
                <th>ราคา/หน่วย</th>
                <th>รวม</th>
              </tr>
            </thead>
            <tbody>
              ${itemRows}
            </tbody>
        </table>
        <h5 class="text-end">รวมทั้งหมด: ${bill.total_amount} ฿</h5>
    `);

        const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
        modal.show();
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
                const timeText = formatDateTimeThai(bill.create_at);
                const tableId = bill.table_id;

                const validOrders = bill.orders.filter(o => o.served !== 2); // ตัด order ที่ถูกลบออก (served == 2)

                const totalAmount = validOrders.reduce((sum, o) => sum + parseFloat(o.price) * o.quantity, 0);

                // ✅ ตรวจสอบว่า ยกเลิกบิลได้หรือไม่
                const canCancelBill = validOrders.every(order => order.status === 0 && order.served === 0);

                const card = document.createElement('div');
                card.className = 'col-md-6 col-lg-4';
                card.innerHTML = `
                <div class="card shadow-sm rounded">
                    <div class="card-body">
                        <h5 class="fw-bold">${bill.name ? bill.name : 'กลับบ้าน'} • ${bill.bill_code}</h5>
                        <p class="text-muted small">เปิดเมื่อ ${timeText}</p>
                        <p class="mb-2">ยอดรวม: <strong>${totalAmount.toFixed(2)} ฿</strong></p>

                        <div class="d-grid gap-2">
                            <button class="btn btn-warning text-white" onclick="viewBillDetailById(${bill.id},${tableId})"><i class="fa-solid fa-magnifying-glass"></i> ดูรายละเอียด</button>
                            ${canCancelBill ? `<button class="btn btn-outline-danger" onclick="cancelBill(${bill.id},${tableId})">ยกเลิกบิล</button>` : ''}
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

        title.textContent = `บิล ${bill.bill_code} (${bill.name ? bill.name : 'กลับบ้าน'})`;

        // ✅ กรองเฉพาะ order ที่ไม่ใช่ status 4 และ served != 2
        const validOrders = bill.orders.filter(order => order.status !== 4 && order.served !== 2);

        const pendingOrders = validOrders.filter(order => order.served === 0);
        const doneOrders = validOrders.filter(order => order.served === 1);

        function groupOrders(orders) {
            const grouped = {};
            orders.forEach(order => {
                const key = `${order.name}_${order.price}`;
                if (!grouped[key]) {
                    grouped[key] = {
                        ...order
                    };
                } else {
                    grouped[key].quantity += order.quantity;
                }
            });
            return Object.values(grouped);
        }

        const groupedPending = groupOrders(pendingOrders);
        const groupedDone = groupOrders(doneOrders);

        const totalAmount = [...groupedPending, ...groupedDone].reduce((sum, item) => sum + parseFloat(item.price) * item.quantity, 0);
        const vatRate = 7;
        const vat = totalAmount * vatRate / 107;
        const netTotal = totalAmount - vat;

        const pendingHtml = groupedPending.map(order => {
            const itemTotal = parseFloat(order.price) * order.quantity;
            let statusBadge = '';
            let showDeleteButton = true;

            if (order.served === 1) {
                statusBadge = '<span class="badge bg-secondary">เสิร์ฟแล้ว</span>';
                showDeleteButton = false;
            } else if (order.status !== 0 && order.status !== 4) {
                statusBadge = '<span class="badge bg-secondary">ทำแล้ว</span>';
                showDeleteButton = false;
            }

            return `
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                ${order.name} x${order.quantity}
                <div class="text-muted small">${itemTotal.toFixed(2)} ฿</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                ${statusBadge}
                ${showDeleteButton ? `<button class="btn btn-sm btn-outline-danger" onclick="deleteOrder(${order.menu_id}, ${bill.id}, ${order.quantity}, ${itemTotal})"><i class="fa-solid fa-trash"></i></button>` : ''}
            </div>
        </li>
    `;
        }).join('');

        const doneHtml = groupedDone.map(order => {
            const itemTotal = parseFloat(order.price) * order.quantity;
            return `
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                ${order.name} x${order.quantity}
                <div class="text-muted small">${itemTotal.toFixed(2)} ฿</div>
            </div>
            <span class="badge bg-secondary">เสิร์ฟแล้ว</span>
        </li>
    `;
        }).join('');

        content.innerHTML = `
    ${pendingHtml ? `<h6 class="mt-2 fw-bold">รายการรอดำเนินการ</h6><ul class="list-group mb-3">${pendingHtml}</ul>` : ''}
    ${doneHtml ? `<h6 class="mt-2 fw-bold">รายการที่เสิร์ฟแล้ว</h6><ul class="list-group mb-3">${doneHtml}</ul>` : ''}
    <div class="text-end">ยอดสุทธิ: ${netTotal.toFixed(2)} ฿</div>
    <div class="text-end">VAT 7%: ${vat.toFixed(2)} ฿</div>
    <h5 class="text-end">รวมทั้งสิ้น: <strong>${totalAmount.toFixed(2)} ฿</strong></h5>
`;

        if (totalAmount === 0) {
            $('#checkoutBtn').addClass('disabled').prop('disabled', true);
        } else {
            $('#checkoutBtn').removeClass('disabled').prop('disabled', false);
        }

        checkoutBtn.onclick = function() {
            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            paymentModal.show();

            document.getElementById('confirmPaymentBtn').onclick = function() {
                if (selectedMethod === 'credit') {
                    const cardNumber = document.getElementById('cardNumber').value.trim();
                    if (cardNumber.length < 13) {
                        Swal.fire({
                            title: "กรุณาใส่ข้อมูล",
                            text: "กรุณากรอกเลขบัตรเครดิตให้ถูกต้อง",
                            icon: "warning"
                        });
                        return;
                    }
                }
                checkoutBill(bill.id, table_id, selectedMethod);
                paymentModal.hide();
            };
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

    function checkoutBill(billId, table_id, selectedMethod) {


        $.ajax({
            url: '/api/api_cashier.php',
            method: 'POST',
            data: {
                case: 'getReceipt',
                bill_id: billId,
                table_id: table_id,
                selectedMethod: selectedMethod
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('success', response.message, 'success')
                    $('#billDetailCanvas').offcanvas('hide')
                    getBill();
                    getHistoryBill();
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

    function deleteOrder(menu_id, billId, qty, total) {
        if (!confirm('คุณแน่ใจว่าต้องการลบออร์เดอร์นี้?')) return;

        $.ajax({
            url: '/api/api_cashier.php',
            method: 'POST',
            data: {
                case: 'deleteOrder',
                menu_id: menu_id,
                billId: billId,
                qty: qty,
                total: total
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('สำเร็จ', response.message, 'success')
                    getBill();
                    getHistoryBill();
                    const offcanvasEl = document.getElementById('billDetailCanvas');
                    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    if (offcanvas) offcanvas.hide();
                } else {
                    Swal.fire('ผิดพลาด', response.message, 'error')
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการลบออร์เดอร์');
            }
        });
    }

    // ตัวอย่างฟังก์ชันยกเลิกบิล (ต้อง implement API ให้เหมาะสม)
    function cancelBill(billId, table_id) {
        Swal.fire({
            title: 'ยืนยันการยกเลิกบิล?',
            text: "คุณแน่ใจว่าต้องการยกเลิกบิลนี้?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'ใช่, ยกเลิกบิล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
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
                            Swal.fire('สำเร็จ', 'ยกเลิกบิลเรียบร้อย', 'success')
                            getBill();
                            getHistoryBill();
                        }
                    },
                    error: function() {
                        alert('เกิดข้อผิดพลาดในการยกเลิกบิล');
                    }
                });
            }
        });


    }

    const paymentCards = document.querySelectorAll('.payment-card');
    const paymentDetails = document.getElementById('paymentDetails');
    let selectedMethod = 1; // เริ่มต้นคือเงินสด (1)

    // สร้าง map เพื่อแมปตัวเลขกับรายละเอียด
    const paymentMethodMap = {
        1: 'เงินสด',
        2: 'โอนเงิน',
        3: 'QR Code',
        4: 'บัตรเครดิต'
    };

    function updatePaymentDetails(method) {
        let html = '';
        switch (parseInt(method)) {
            case 1: // เงินสด
                html = `<p>ชำระด้วยเงินสด กรุณารับเงินจากลูกค้า</p>`;
                break;
            case 2: // โอนเงิน
                html = `
                <p><strong>ชื่อธนาคาร:</strong> ธนาคารกรุงไทย</p>
                <p><strong>ชื่อบัญชี:</strong> บริษัท เฟนิกซ์ จำกัด</p>
                <p><strong>QR Code ธนาคาร:</strong></p>
                <img src="/assets/images/qrcode.png" alt="QR Code โอนเงิน" class="img-fluid" />
            `;
                break;
            case 3: // QR
                html = `
                <p>สแกน QR Code เพื่อชำระเงิน</p>
                <img src="/assets/images/qrcode.png" alt="QR Code" class="img-fluid" />
            `;
                break;
            case 4: // บัตร
                html = `
                <label for="cardNumber" class="form-label">กรอกเลขบัตรเครดิต</label>
                <input type="text" id="cardNumber" class="form-control" placeholder="xxxx-xxxx-xxxx-xxxx" maxlength="19" />
            `;
                break;
        }
        paymentDetails.innerHTML = html;
    }

    paymentCards.forEach(card => {
        card.addEventListener('click', () => {
            paymentCards.forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            selectedMethod = card.dataset.method;
            updatePaymentDetails(selectedMethod);
        });
    });

    // เริ่มต้น
    updatePaymentDetails(selectedMethod);
</script>