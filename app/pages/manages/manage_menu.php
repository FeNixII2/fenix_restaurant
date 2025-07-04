<div class="row mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="fw-bold fs-3 text-warning">เมนู</div>
        <div class="gap-2">
            <button class="btn btn-warning text-white " type="button" data-bs-toggle="offcanvas" data-bs-target="#canvasfoodcategory" aria-controls="offcanvasRight"><i class="fa-solid fa-list"></i> ประเภท</button>
            <button class="btn btn-warning  text-white " type="button" data-bs-toggle="offcanvas" data-bs-target="#canvasaddfood" aria-controls="offcanvasRight"><i class="fa-solid fa-bowl-food"></i> อาหาร</button>
            <button class="btn btn-warning  text-white " type="button" data-bs-toggle="offcanvas" data-bs-target="#canvasstock" aria-controls="offcanvasRight"><i class="fa-solid fa-clipboard-list"></i> สต๊อก</button>
        </div>
    </div>
</div>


<div class="offcanvas offcanvas-end" tabindex="-1" id="canvasfoodcategory" aria-labelledby="canvasfoodcategory">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold text-warning">ข้อมูลประเภท</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="addCategory">
        <form class="">
            <div class="mb-3">
                <label for="categoryName" class="form-label">ชื่อประเภท</label>
                <label type="hidden" id="categoryId" name="categoryId"></label>
                <input type="text" class="form-control mb-3" placeholder="ต้ม..,ผัด..,แกง.." id="categoryName" name="categoryName" required>
            </div>
            <button type="submit" id="btn-addCategory" class="btn btn-warning text-white">เพิ่มประเภท</button>
            <button type="submit" id="btn-editCategory" class="btn btn-warning text-white" style="display:none;">ยืนยันการแก้ไข</button>
            <button id="btn-cancelCategory" class="btn btn-danger" onClick="resetFormCategory()">ยกเลิก</button>
        </form>
        <div class="mt-3" id="list-catagory">

        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="canvasaddfood" aria-labelledby="canvasaddfood">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold text-warning">ข้อมูลอาหาร</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <form class="offcanvas-body" id="addMenu" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="MenuName" class="form-label">ชื่ออาหาร</label>
            <label type="hidden" id="menuId" name="menuId"></label>
            <input type="text" class="form-control" placeholder="ชื่ออาหาร" id="menuName" name="menuName" required>
        </div>
        <div class="mb-3">
            <label for="menuCategory" class="form-label">ประเภท</label>
            <select class="form-select" aria-label="Default select example" name="menuCategory" required>
            </select>
        </div>
        <div class="mb-3">
            <label for="menuServetype" class="form-label">เสิร์ฟ</label>
            <select class="form-select" name="menuServetype" id="menuServetype" required>
                <option value="1" selected>ต้องเตรียมก่อนเสิร์ฟ</option>
                <option value="0">พร้อมเสิร์ฟทันที</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="details" class="form-label">รายละเอียด</label>
            <textarea class="form-control" id="menuDetails" name="menuDetails" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">ราคา</label>
            <input type="number" step="1" class="form-control" placeholder="ราคา" id="price" name="price" min="0" required>
        </div>
        <img id="previewImage" src="/assets/images/picture.png" style="width: 200px;height:130; display: block;">
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">เลือกรูปภาพ</label>
            <input type="file" class="form-control mb-3" id="imageUpload" name="foodImage">
        </div>
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" checked>
            <label class="form-check-label" for="status">สถานะการเปิดขาย</label>
        </div>
        <button type="submit" id="btn-addMenu" class="btn btn-warning text-white">เพิ่มอาหาร</button>
        <button type="submit" id="btn-editMenu" class="btn btn-warning text-white" style="display:none;">ยืนยันการแก้ไข</button>
        <button id="btn-cancelMenu" class="btn btn-danger" onClick="resetFormMenu()">ยกเลิก</button>
    </form>
</div>


<div class="offcanvas offcanvas-end" tabindex="-1" id="canvasstock" aria-labelledby="canvasstock">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold text-warning">จัดการสต๊อก</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="mx-2">
        <div class="table-responsive mb-3" style="max-height: 85vh; overflow-y: auto; display: block;">
            <table class="table table-borderless text-nowrap">
                <thead>
                    <tr>
                        <th>เมนู</th>
                        <th class="text-end">จำนวน</th>
                    </tr>
                </thead>
                <tbody id="table-stock" class=" align-middle">
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-start">
            <button class="btn btn-warning text-white" id="btn-confirm-stock">ยืนยันแก้ไขสต๊อก</button>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table id="table-menu" class="display table mb-0">
        <thead>
            <tr>
                <th>เมนู</th>
                <th>ประเภท</th>
                <th>ราคา</th>
                <th>สถานะ</th>
                <th>สต๊อก</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody id="table-listmenu">
        </tbody>
    </table>
</div>

<script>
    let CategoryArray = [];
    let MenuArray = [];
    let Image_Id;
    let searchTimeout;

    $(document).ready(function() {
        getCategory();
        getMenu();
        resetFormCategory();
        resetFormMenu();
    });

    function findMenu() {

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {

            const keyword = document.getElementById("searchInput").value.trim().toLowerCase();
            const results = MenuArray.filter(item =>
                item.name.toLowerCase().includes(keyword) ||
                item.price.toString().includes(keyword) ||
                item.category_name.toLowerCase().includes(keyword) ||
                item.details.toLowerCase().includes(keyword)

            );

            showMenu(results);
        }, 600);
    }

    function showMenu(data) {


        if ($.fn.DataTable.isDataTable('#table-menu')) {
            $('#table-menu').DataTable().destroy();
        }

        const tbody = $('#table-listmenu');
        tbody.empty();

        data.forEach((menu, index) => {
            let statusText = menu.status === 1 ? 'เปิดขาย' : 'ปิดขาย';
            tbody.append(`
                                 <tr class="border-top  align-middle">
                                     <td>
                                         <div class="d-flex align-items-center">
                                             <div class="rounded-3 overflow-hidden" style="width:5rem; height:5rem;">
                                                 <img src="${menu.path}" style="width:100%; height:100%; object-fit:cover;" alt="" onerror="this.onerror=null;this.src='/assets/images/picture.png';">
                                            </div>
                                            <div class="text-content ms-3">
                                                 <div class="fw-bold">${menu.name}</div>
                                                 <div class="text-truncate" style="max-width: 120px;color:var(--bs-secondary-color)">
                                                     ${menu.details}
                                                 </div>
                                             </div>
                                     </td>
                                     <td >${menu.category_name}</td>
                                     <td >฿${menu.price}</td>

                                     <td > ${statusText}</td>
                                     <td >
                                       ${menu.stock}
                                     </td>
                                     <td >
                                        <div class="d-flex flex-wrap justify-content-end gap-2">
                                            <button class="btn btn-warning text-white" id="btn-edit-menu" data-menu='${JSON.stringify(menu)}'>
                                            <i class="fa-solid fa-pencil"></i>
                                            </button>
                                            <button class="btn btn-danger" onClick="deleteMenu(${menu.id},'${menu.name}')">
                                            <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                     </td>
                                 </tr> 
        `);
        });

        $('#table-menu').DataTable({
            responsive: true,
            scrollX: false,
            autoWidth: false,
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


    function resetFormCategory() {
        $('#categoryName').val('');
        $('#btn-addCategory').show();
        $('#btn-editCategory').hide();
        $('#btn-cancelCategory').hide();
    }

    function resetFormMenu() {

        $('#previewImage').attr('src', '/assets/images/picture.png');
        $('#addMenu')[0].reset();
        $('#btn-addMenu').show();
        $('#btn-editMenu').hide();
        $('#btn-cancelMenu').hide();

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
                    CategoryArray = response.data;
                    let listCategory = $('#list-catagory');
                    listCategory.empty();
                    listCategory.append(``);
                    response.data.forEach(function(category) {
                        listCategory.append(`
                            <hr>
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <label for="">${category.name}</label>
                                <div class="btn-group">
                                    <button class="btn btn-warning text-white btn-sm"  onClick="editCategory(${category.id},'${category.name}')"><i class="fa-solid fa-pencil"></i></button>
                                    <button class="btn btn-danger btn-sm" onClick="deleteCategory(${category.id})"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                            `);
                    });
                    listCategory.append(`</div">`);
                    let $select = $('select[name="menuCategory"]');
                    $select.empty();
                    $select.append('<option value="" selected disabled>เลือกประเภทอาหาร</option>');
                    response.data.forEach(function(category) {
                        $select.append(`<option value="${category.id}">${category.name}</option>`);
                    });
                }
            },
            error: function(xhr) {
                Swal.fire('ผิดพลาด', xhr.responseText, 'error');
            }
        });
    }

    function getMenu() {
        $.ajax({
            url: '/api/api_manage_menu.php',
            method: 'GET',
            data: {
                case: 'getAllMenu'
            },
            dataType: 'json',
            beforeSend: function() {
                $('#tableLoader').removeClass('d-none');
                $('#table-listmenu').empty(); // เคลียร์ table ก่อน
            },
            success: function(response) {
                if (response.status === 'success') {
                    MenuArray = response.data
                    showMenu(response.data);
                }
            },
            complete: function() {
                $('#tableLoader').addClass('d-none');
            },
        });
    }

    function deleteMenu(id, name) {
        Swal.fire({
            title: `คุณต้องการลบเมนู ${name} ?`,
            showDenyButton: true,
            confirmButtonText: "ใช่",
            denyButtonText: `ยกเลิก`
        }).then((result) => {
            if (result.isConfirmed) {
                // Call API to delete menu
                $.ajax({
                    url: '/api/api_manage_menu.php',
                    method: 'POST',
                    data: {
                        case: 'deleteMenu',
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {


                        if (response.status === 'success') {
                            Swal.fire('สำเร็จ', response.message, 'success');
                            getMenu();
                        } else {
                            Swal.fire('ผิดพลาด', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                    }
                });
            }
        });
    }

    function deleteCategory(id) {
        Swal.fire({
            title: "คุณต้องการลบประเภทเมนูนี้ ?",
            showDenyButton: true,
            confirmButtonText: "ใช่",
            denyButtonText: `ยกเลิก`
        }).then((result) => {
            if (result.isConfirmed) {
                // Call API to delete category
                $.ajax({
                    url: '/api/api_manage_menu.php',
                    method: 'POST',
                    data: {
                        case: 'deleteCategory',
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('สำเร็จ', response.message, 'success');
                            getCategory();
                            resetFormCategory();
                        } else {
                            Swal.fire('ผิดพลาด', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                    }
                });
            }
        });
    }

    function editCategory(id, name) {
        $('#btn-addCategory').hide();
        $('#btn-editCategory').show();
        $('#btn-cancelCategory').show();
        $('#categoryName').val(name);
        $('#categoryId').val(id)
    }

    $(document).on('click', '#btn-edit-menu', function() {
        const menu = JSON.parse($(this).attr('data-menu'));
        
        let imageSrc = menu.path && menu.path.trim() !== "" ? menu.path : "/assets/images/picture.png";
        $('#canvasaddfood').offcanvas('show');
        $('#menuName').val(menu.name);
        $('select[name="menuCategory"]').val(menu.category_id);
        $('select[name="menuServetype"]').val(menu.serve_type);
        $('#menuDetails').val(menu.details);
        $('#price').val(menu.price);
        $('#previewImage').attr('src', imageSrc);
        $('#status').prop('checked', menu.status === 1);
        $('#btn-addMenu').hide();
        $('#btn-editMenu').show();
        $('#btn-cancelMenu').show();
        $('#menuId').val(menu.id);
        Image_Id = menu.image_id || null;
    });



    $('#imageUpload').on('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            if (!file.type.startsWith('image/')) {
                Swal.fire('รูปภาพไม่ถูกต้อง', 'กรุณาเลือกรูปภาพที่ถูกต้อง (เช่น .jpg, .png)', 'error');
                $(this).val(''); // reset input
                $('#previewImage').attr('src', '/assets/images/picture.png');
                return;
            }
            $('#previewImage').attr('src', URL.createObjectURL(file)).show();
        } else {
            $('#previewImage').attr('src', "/assets/images/picture.png").show();
        }
    });

    $('#canvasfoodcategory').on('shown.bs.offcanvas', function() {
        getCategory();
    });

    $('#canvasstock').on('shown.bs.offcanvas', function() {
        $.ajax({
            url: '/api/api_manage_menu.php',
            method: 'GET',
            data: {
                case: 'getAllMenu'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    let listmenu = $('#table-stock');
                    listmenu.empty();
                    if (response.data.length === 0) {
                        listmenu.append(`
                                        <tr class="border-top">
                                            <td colspan="6" class="text-center text-muted">ไม่มีข้อมูล</td>
                                        </tr>
                                    `);
                    } else {
                        response.data.forEach(function(menu) {
                            listmenu.append(`
                                <tr class="border-top" data-id="${menu.id}">
                                    <td>${menu.name}</td>
                                    <td class="d-flex justify-content-end">
                                        <button class="btn btn-danger" onclick="adjustStock(this, -1)"><i class="fa-solid fa-minus"></i></button>
                                        <input  type="number" class="form-control w-50 mx-2 stock-input" value="${menu.stock}" min="0"></input>
                                        <button class="btn btn-warning text-white" onclick="adjustStock(this, 1)"><i class="fa-solid fa-plus"></i></button>
                                    </td>
                                </tr>
                        `);
                        })

                        document.querySelectorAll('.stock-input').forEach(function(input) {
                            input.addEventListener('blur', function() {
                                let value = parseFloat(this.value);

                                if (isNaN(value) || value < 0) {
                                    this.value = 0;
                                } else {
                                    this.value = value.toFixed(2).replace(/^0+(\d)/, '$1');
                                }
                            });
                        });


                    }
                }
            }
        });
    });

    function adjustStock(button, change) {
        const input = button.parentElement.querySelector('input[type="number"]');
        let current = parseInt(input.value) || 0;
        let newValue = current + change;

        if (newValue < 0) {
            newValue = 0; // ป้องกันค่าติดลบ
        }

        input.value = newValue;
    }


    document.getElementById('price').addEventListener('blur', function() {
        let value = parseFloat(this.value);

        if (isNaN(value) || value < 0) {
            this.value = 0;
        } else {
            // แปลงให้ตัด 0 ข้างหน้าอัตโนมัติ และคงทศนิยมไว้สูงสุด 2 ตำแหน่ง
            this.value = value.toFixed(2).replace(/^0+(\d)/, '$1');
        }
    });

    $('#btn-confirm-stock').on('click', function() {
        Swal.fire({
            title: `คุณต้องการแก้ไขรายการสต๊อก ?`,
            showDenyButton: true,
            confirmButtonText: "ใช่",
            denyButtonText: `ยกเลิก`
        }).then((result) => {
            if (result.isConfirmed) {
                let stockData = [];
                $('#table-stock tr').each(function() {
                    let menuId = $(this).data('id');
                    let stockVal = $(this).find('.stock-input').val();

                    if (menuId !== undefined) {
                        stockData.push({
                            id: menuId,
                            stock: parseInt(stockVal) || 0
                        });
                    }
                });

                $.ajax({
                    url: '/api/api_manage_menu.php',
                    method: 'POST',
                    data: {
                        case: 'updateStock',
                        stockItems: JSON.stringify(stockData)
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('สำเร็จ', response.message, 'success');
                            getMenu();
                        } else {
                            Swal.fire('ผิดพลาด', response.message, 'error');
                        }

                    },
                    error: function(xhr) {
                        Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                    }
                });
            }
        });
    });



    $('#addCategory').on('submit', function(e) {
        e.preventDefault();
        var submitter = e.originalEvent && e.originalEvent.submitter;
        if (submitter && submitter.id === 'btn-addCategory') {
            let checkname = $('#categoryName').val();
            let exists = CategoryArray.some(cat => cat.name === checkname);
            if (exists) {
                Swal.fire('ไม่สามารถเพิ่มได้', 'ประเภทเมนูนี้มีอยู่แล้ว', 'error');
                return;
            }
            Swal.fire({
                title: `คุณต้องการเพิ่มประเภทเมนู ${$('#categoryName').val()} ?`,
                showDenyButton: true,
                confirmButtonText: "ใช่",
                denyButtonText: `ยกเลิก`
            }).then((result) => {
                if (result.isConfirmed) {
                    let data = {
                        case: 'addCategory',
                        categoryName: $('#categoryName').val()
                    }
                    $.ajax({
                        url: '/api/api_manage_menu.php',
                        method: 'POST',
                        data: data,
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('สำเร็จ', response.message, 'success');
                                $('#categoryName').val('');
                                getCategory();
                            } else {
                                Swal.fire('ผิดพลาด', response.message, 'error');
                            }

                        },
                        error: function(xhr) {
                            Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                        }
                    });
                }
            });
        } else if (submitter && submitter.id === 'btn-editCategory') {
            let checkname = $('#categoryName').val();
            let exists = CategoryArray.some(cat => cat.name === checkname);
            if (exists) {
                Swal.fire('ไม่สามารถแก้ไขได้', 'ประเภทเมนูนี้มีอยู่แล้ว', 'error');
                return;
            }
            Swal.fire({
                title: "คุณต้องการแก้ไขประเภทเมนู ?",
                showDenyButton: true,
                confirmButtonText: "ใช่",
                denyButtonText: `ยกเลิก`
            }).then((result) => {
                if (result.isConfirmed) {
                    let data = {
                        case: 'editCategory',
                        id: $('#categoryId').val(),
                        categoryName: $('#categoryName').val()
                    }

                    $.ajax({
                        url: '/api/api_manage_menu.php',
                        method: 'POST',
                        data: data,
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('สำเร็จ', response.message, 'success');
                                resetFormCategory();
                                getCategory();
                                getMenu();
                            } else {
                                Swal.fire('ผิดพลาด', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                        }
                    });
                }
            });
        }

    });





    $('#addMenu').on('submit', function(e) {
        e.preventDefault();
        var submitter = e.originalEvent && e.originalEvent.submitter;
        if (submitter && submitter.id === 'btn-addMenu') {
            Swal.fire({
                title: "คุณต้องการเพิ่มประเภทเมนู ?",
                showDenyButton: true,
                confirmButtonText: "ใช่",
                denyButtonText: `ยกเลิก`
            }).then((result) => {

                if (result.isConfirmed) {
                    let formData = new FormData(this);
                    let status = $('#status').is(':checked') ? 1 : 0;
                    formData.set('status', status);
                    formData.append('case', 'addMenu');
                    $.ajax({
                        url: '/api/api_manage_menu.php',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('สำเร็จ', response.message, 'success');
                                $('#addMenu')[0].reset();
                                $('#previewImage').attr('src', '/assets/images/picture.png');
                                getMenu();
                                $('#canvasaddfood').offcanvas('hide');
                            } else {
                                Swal.fire('ผิดพลาด', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                        }
                    });
                }
            });

        } else if (submitter && submitter.id === 'btn-editMenu') {
            Swal.fire({
                title: "คุณต้องการแก้ไขเมนู ?",
                showDenyButton: true,
                confirmButtonText: "ใช่",
                denyButtonText: `ยกเลิก`
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData(this);
                    let status = $('#status').is(':checked') ? 1 : 0;
                    formData.set('status', status);
                    formData.append('case', 'editMenu');
                    formData.append('id', $('#menuId').val());
                    formData.append('image_id', Image_Id || '');
                    $.ajax({
                        url: '/api/api_manage_menu.php',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response) {


                            if (response.status === 'success') {
                                Swal.fire('สำเร็จ', response.message, 'success');
                                resetFormMenu();
                                getMenu();
                                $('#canvasaddfood').offcanvas('hide');
                            } else {
                                Swal.fire('ผิดพลาด', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('ผิดพลาด', xhr.responseText, 'error');
                        }
                    });
                }
            });
        }
    });
</script>