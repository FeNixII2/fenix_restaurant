<div class="row mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="fw-bold fs-3">เมนู</div>
        <div class="gap-2">
            <button class="btn btn-info text-white " type="button" data-bs-toggle="offcanvas" data-bs-target="#canvasfoodcategory" aria-controls="offcanvasRight">+ ประเภท</button>
            <button class="btn btn-primary " type="button" data-bs-toggle="offcanvas" data-bs-target="#canvasaddfood" aria-controls="offcanvasRight">+ อาหาร</button>
            <button class="btn btn-success " type="button" data-bs-toggle="offcanvas" data-bs-target="#canvasstock" aria-controls="offcanvasRight">+ สต๊อก</button>
        </div>
    </div>
</div>

<div class="bg-white p-2 rounded-3 mb-3 shadow-sm">
    <div class="my-3 row">
        <div class="col-sm-12 col-md-6">
            <div class="fw-bold fs-4">รายการทั้งหมด</div>
        </div>
        <div class="col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-start">
            <input type="text" class="form-control " placeholder="ค้นหา..." style="width: 20rem;" id="searchInput" onkeyup="findMenu()">
        </div>
    </div>
    <div class="table-responsive">
        <div class="text-center" id="tableLoader">
            <div class="spinner-border" role="status"></div>
        </div>
        <table class="table table-borderless text-nowrap">
            <thead>
                <tr>
                    <th>เมนู</th>
                    <th>ประเภท</th>
                    <th>ราคา</th>
                    <th>สถานะ</th>
                    <th>สต๊อก</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="table-listmenu" class=" align-middle">
            </tbody>
        </table>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="canvasfoodcategory" aria-labelledby="canvasfoodcategory">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">ข้อมูลประเภท</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="addCategory">
        <form class="">
            <div class="mb-3">
                <label for="categoryName" class="form-label">ชื่อประเภท</label>
                <label type="hidden" id="categoryId" name="categoryId"></label>
                <input type="text" class="form-control mb-3" placeholder="ต้ม..,ผัด..,แกง.." id="categoryName" name="categoryName" required>
            </div>
            <button type="submit" id="btn-addCategory" class="btn btn-primary">เพิ่มประเภท</button>
            <button type="submit" id="btn-editCategory" class="btn btn-warning text-white" style="display:none;">ยืนยันการแก้ไข</button>
            <button id="btn-cancelCategory" class="btn btn-danger" onClick="resetFormCategory()">ยกเลิก</button>
        </form>
        <div class="mt-3" id="list-catagory">

        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="canvasaddfood" aria-labelledby="canvasaddfood">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">ข้อมูลอาหาร</h5>
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
        <button type="submit" id="btn-addMenu" class="btn btn-primary">เพิ่มอาหาร</button>
        <button type="submit" id="btn-editMenu" class="btn btn-warning text-white" style="display:none;">ยืนยันการแก้ไข</button>
        <button id="btn-cancelMenu" class="btn btn-danger" onClick="resetFormMenu()">ยกเลิก</button>
    </form>
</div>


<div class="offcanvas offcanvas-end" tabindex="-1" id="canvasstock" aria-labelledby="canvasstock">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">จัดการสต๊อก</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="mx-2">
        <div class="table-responsive ">
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
        <div class="d-flex justify-content-end px-2">
            <button class="btn btn-primary" id="btn-confirm-stock">ยืนยันแก้ไขสต๊อก</button>
        </div>
    </div>
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
        let listmenu = $('#table-listmenu');
        listmenu.empty();
        if (data.length === 0) {
            listmenu.append(`
                                        <tr class="border-top">
                                            <td colspan="6" class="text-center text-muted">ไม่มีข้อมูล</td>
                                        </tr>
                                    `);
        } else {
            data.forEach(function(menu) {
                let statusText = menu.status === 1 ? 'เปิดขาย' : 'ปิดขาย';
                listmenu.append(`
                                <tr class="border-top">
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
                                    <td>${menu.category_name}</td>
                                    <td>฿${menu.price}</td>

                                    <td> ${statusText}</td>
                                    <td>
                                      ${menu.stock}
                                    </td>
                                    <td>
                                        <div class="text-end gap-2">
                                            <button class="btn btn-warning text-white" id="btn-edit-menu" data-menu='${JSON.stringify(menu)}'>แก้ไข</button>
                                            <button class="btn btn-danger" onClick="deleteMenu(${menu.id},'${menu.name}')">ลบ</button>
                                        </div>
                                    </td>
                                </tr> 
                        `);
            })
        }
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
                                    <button class="btn btn-warning text-white btn-sm"  onClick="editCategory(${category.id},'${category.name}')">แก้ไข</button>
                                    <button class="btn btn-danger btn-sm" onClick="deleteCategory(${category.id})">ลบ</button>
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
                console.log(xhr.responseText);
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
                        console.log(xhr.responseText);
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
                        console.log(xhr.responseText);
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
                                        <button class="btn btn-danger" onclick="adjustStock(this, -1)">-</button>
                                        <input  type="number" class="form-control w-25 mx-2 stock-input" value="${menu.stock}" min="0"></input>
                                        <button class="btn btn-primary" onclick="adjustStock(this, 1)">+</button>
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
                        console.log(xhr.responseText);
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
                            console.log(xhr.responseText);
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
                            console.log(xhr.responseText);
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
                            console.log(xhr.responseText);
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
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        }
    });
</script>