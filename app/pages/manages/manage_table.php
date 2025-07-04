<div class="row mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="fw-bold fs-3 text-warning">โต๊ะ</div>
        <div class="gap-2">
            <button class="btn btn-warning text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#canvasaddtable" aria-controls="offcanvasRight"><i class="fa-solid fa-chair"></i> โต๊ะ</button>
        </div>
    </div>
</div>

<div class="row" id="alltable">
    <div class="text-center" id="tableLoader">
        <div class="spinner-border" role="status"></div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="canvasaddtable" aria-labelledby="canvasaddtable">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold text-warning">ข้อมูลโต๊ะ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="addTable">
        <form id="form-add">
            <div class="mb-3">
                <label for="tableName" class="form-label">ชื่อโต๊ะ</label>
                <input type="text" class="form-control mb-3" placeholder="โต๊ะ1..,โต๊ะ2..,โต๊ะVip1.." id="tableName" name="tableName" required>
            </div>
            <div class="form-check form-switch mb-3">
                <label for="status" class="form-lable">สถานะ</label>
                <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" checked>
            </div>
            <button type="submit" id="btn-addTable" class="btn btn-warning text-white">เพิ่มโต๊ะ</button>
        </form>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="canvasedittable" aria-labelledby="canvasedittable">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold text-warning">แก้ไขข้อมูลโต๊ะ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="editTable">
        <form id="form-edit">
            <div class="mb-3">
                <input type="hidden" id="editTableId">
                <label for="tableName" class="form-label">ชื่อโต๊ะ</label>
                <input type="text" class="form-control mb-3" placeholder="โต๊ะ1..,โต๊ะ2..,โต๊ะVip1.." id="edittableName" name="edittableName" required>
            </div>
            <div class="form-check form-switch mb-3">
                <label for="status" class="form-lable">สถานะ</label>
                <input class="form-check-input" type="checkbox" role="switch" id="editstatus" name="editstatus" checked>
            </div>
            <button type="submit" id="btn-editTable" class="btn btn-warning text-white">ยืนยันการแก้ไข</button>
        </form>
    </div>
</div>


<script>
    $(document).ready(function() {
        getTable();
    })

    function getTable() {
        $.ajax({
            url: '/api/api_manage_table.php',
            method: 'GET',
            data: {
                case: 'get_table'
            },
            dataType: 'json',
            beforeSend: function() {
                $('#tableLoader').removeClass('d-none');
                $('#table-listmenu').empty(); // เคลียร์ table ก่อน
            },
            success: function(response) {
                if (response.status === 'success') {
                    let listtable = $('#alltable');
                    listtable.empty();
                    if (response.data.length === 0) {
                        listtable.append(`
                                        <tr class="border-top">
                                            <td colspan="6" class="text-center text-muted">ไม่มีข้อมูล</td>
                                        </tr>
                                    `);
                    } else {
                        response.data.forEach(function(table) {
                            let stateText = table.table_state === 1 ? 'มีลูกค้า' : 'ว่าง';
                            let stateBadge = table.table_state === 1 ? 'bg-white text-warning' : 'bg-white text-dark';
                            let isDisabled = table.status === 0;

                            let overlay = isDisabled ?
                                `<div class="position-absolute top-0 start-0 w-100 h-100 bg-light rounded-4" style="opacity: 0.5; z-index: 1;"></div>` :
                                '';

                            listtable.append(`
                                    <div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-4">
                                        <div class="position-relative ${isDisabled ? 'opacity-50' : ''}">
                                            ${overlay}
                                            <div class="p-3 shadow rounded-4 bg-dark text-white position-relative" style="z-index: 2;">
                                                <div class="card-body d-flex flex-column justify-content-between h-100">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="fw-bold mb-0 fs-3 ${isDisabled ? 'text-white' : 'text-white'}">${table.name}</h5>
                                                        <span class="badge ${stateBadge}">${stateText}</span>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <label class="form-check-label me-2 fs-5">สถานะ</label>
                                                        <div class="form-check form-switch fs-4">
                                                          <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            role="switch"
                                                            onchange="event.preventDefault(); changeStatus(this, ${table.id}, ${table.status}, ${table.table_state})"
                                                            ${table.status === 1 ? 'checked' : ''}>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between">
                                                        <button class="btn btn-warning text-white btn-sm w-50 me-1" id="btn-edit" data-table='${JSON.stringify(table)}'><i class="fa-solid fa-pencil"></i></button>
                                                        <button  class="btn btn-danger btn-sm w-50 ms-1" onClick="deleteTable('${table.name}',${table.table_state},${table.id})"><i class="fa-solid fa-trash"></i></button>
                                                    </div>
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

    function changeStatus(checkbox, tableId, currentStatus, state) {
        const newStatus = currentStatus === 1 ? 0 : 1;
        if (state === 1) {
            Swal.fire('ผิดพลาด', 'ยังมีลูกค้านั่งอยู่ไม่สามารถปิดโต๊ะได้', 'error');
            checkbox.checked = currentStatus === 1;
            return
        }
        Swal.fire({
            title: `คุณต้องการ ${newStatus === 1 ? 'เปิด' : 'ปิด'} โต๊ะใช่หรือไม่?`,
            showDenyButton: true,
            confirmButtonText: "ใช่",
            denyButtonText: `ยกเลิก`
        }).then((result) => {
            if (result.isConfirmed) {
                // เรียก API ไปอัปเดต status
                $.ajax({
                    url: '/api/api_manage_table.php',
                    method: 'POST',
                    data: {
                        case: 'toggle_status',
                        id: tableId,
                        status: newStatus
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('สำเร็จ', res.message, 'success');
                            // เซ็ต checkbox ใหม่ด้วย JS (ถ้าจำเป็น)
                            checkbox.checked = newStatus === 1;
                        } else {
                            Swal.fire('ผิดพลาด', res.message, 'error');
                            checkbox.checked = currentStatus === 1; // ย้อนกลับ
                        }
                        getTable();
                    },
                    error: function() {
                        Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อ API ได้', 'error');
                        checkbox.checked = currentStatus === 1; // ย้อนกลับ
                    }
                });
            } else {
                // ถ้าผู้ใช้กดปิดหรือยกเลิก => ย้อน checkbox กลับ
                checkbox.checked = currentStatus === 1;
            }
        });
    }

    function deleteTable(name, state, id) {
        if (state === 1) {
            Swal.fire('ผิดพลาด', 'ยังมีลูกค้านั่งอยู่ไม่สามารถลบโต๊ะได้', 'error');
            return
        }
        Swal.fire({
            title: `คุณต้องการลบ ${name} ?`,
            showDenyButton: true,
            confirmButtonText: "ใช่",
            denyButtonText: `ยกเลิก`
        }).then((result) => {
            if (result.isConfirmed) {
                let data = {
                    case: 'delete_table',
                    id: id
                }

                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/api_manage_table.php',
                        method: 'POST',
                        data: data,
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('สำเร็จ', response.message, 'success');
                                getTable();
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                        }
                    })
                }
            }
        })
    }

    $('#addTable').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: `คุณต้องการเพิ่มโต๊ะ ?`,
            showDenyButton: true,
            confirmButtonText: "ใช่",
            denyButtonText: `ยกเลิก`
        }).then((result) => {
            let data = {
                case: 'create_table',
                nametable: $('#tableName').val(),
                status: $('#status').is(':checked') ? 1 : 0
            }

            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/api_manage_table.php',
                    method: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('สำเร็จ', response.message, 'success');
                            getTable();
                            $('#form-add')[0].reset();
                            $('#canvasaddtable').offcanvas('hide');
                        }else{
                            Swal.fire('ผิดพลาด', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                    }
                })
            }
        });
    });

    $(document).on('click', '#btn-edit', function(e) {
        e.preventDefault();
        const data = JSON.parse($(this).attr('data-table'));
        $('#edittableName').val(data['name']);
        $('#editstatus').prop('checked', data['status'] === 1);
        $('#canvasedittable').offcanvas('show');
        $('#editTableId').val(data['id']);
    })
    $('#editTable').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: `คุณต้องการแก้ไขโต๊ะ ?`,
            showDenyButton: true,
            confirmButtonText: "ใช่",
            denyButtonText: `ยกเลิก`
        }).then((result) => {
            let data = {
                case: 'edit_table',
                nametable: $('#edittableName').val(),
                status: $('#editstatus').is(':checked') ? 1 : 0,
                id: $('#editTableId').val()
            }

            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/api_manage_table.php',
                    method: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('สำเร็จ', response.message, 'success');
                            getTable();
                            $('#form-edit')[0].reset();
                            $('#canvasedittable').offcanvas('hide');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                    }
                })
            }
        });
    });
</script>