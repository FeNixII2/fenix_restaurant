<div class="row mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="fw-bold fs-3">โต๊ะ</div>
        <div class="gap-2">
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#canvasaddtable" aria-controls="offcanvasRight">+ โต๊ะ</button>
        </div>
    </div>
</div>

<div class="row">
   

    <div class="col-sm-12 col-md-4 col-lg-3 mb-4">
        <div class="p-3 shadow rounded-4">
            <div class="card-body d-flex flex-column justify-content-between h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary mb-0 fs-3 text-danger">โต๊ะ 1</h5>
                    <span class="badge bg-success">ว่าง</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-check-label me-2 fs-5" for="status1">เปิดใช้งาน</label>
                    <div class="form-check form-switch fs-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="status1" name="status" checked>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button class="btn btn-warning text-white btn-sm w-50 me-1">แก้ไข</button>
                    <button class="btn btn-danger btn-sm w-50 ms-1">ลบ</button>
                </div>
            </div>
        </div>
    </div>
   
</div>


<div class="offcanvas offcanvas-end" tabindex="-1" id="canvasaddtable" aria-labelledby="canvasaddtable">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">ข้อมูลโต๊ะ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="addCategory">
        <form class="">
            <div class="mb-3">
                <label for="tableName" class="form-label">ชื่อโต๊ะ</label>
                <input type="text" class="form-control mb-3" placeholder="โต๊ะ1..,โต๊ะ2..,โต๊ะVip1.." id="tableName" name="tableName" required>
            </div>
            <div class="form-check form-switch mb-3">
                <label for="status" class="form-lable">สถานะ</label>
                <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" checked>
            </div>
            <button type="submit" id="btn-addCategory" class="btn btn-primary">เพิ่มประเภท</button>
            <button type="submit" id="btn-editCategory" class="btn btn-warning text-white" style="display:none;">ยืนยันการแก้ไข</button>
            <button id="btn-cancelCategory" class="btn btn-danger" onClick="resetFormCategory()">ยกเลิก</button>
        </form>
        <div class="mt-3" id="list-catagory">
        </div>
    </div>
</div>

<script>
    $('#addCategory').on('submit', function(e) {
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
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                        console.log(xhr.responseText);
                    }
                })
            }
        });
    });
</script>