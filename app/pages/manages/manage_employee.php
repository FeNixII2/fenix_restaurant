<div class="row mb-3">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="fw-bold fs-3">พนักงาน</div>
    <div class="gap-2">
      <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#canvasaddemployee" aria-controls="offcanvasRight">+ พนักงาน</button>
    </div>
  </div>
</div>
<!-- ตาราง -->
<div class="table-responsive">
  <table class="table table-bordered table-striped text-center align-middle">
    <thead class="table-dark text-nowrap">
      <tr>
        <th>ลำดับ</th>
        <th>ชื่อผู้ใช้</th>
        <th>อีเมล</th>
        <th>ชื่อจริง</th>
        <th>นามสกุล</th>
        <th>ตำแหน่ง (Role)</th>
        <th>จัดการ</th>
      </tr>
    </thead>
    <tbody id="employeeTableBody">

    </tbody>
  </table>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="canvasaddemployee" aria-labelledby="canvasaddemployee">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">ข้อมูลพนักงาน</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form id="formEmployee">

      <div class="mb-3">
        <label class="form-label">ชื่อผู้ใช้</label>
        <input type="text" name="username" id="username" placeholder="ชื่อผู้ใช้..." class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">อีเมล</label>
        <input type="email" name="email" placeholder="อีเมลล์..." class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">ชื่อจริง</label>
        <input type="text" name="firstname" placeholder="ชื่อจริง..." class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">นามสกุล</label>
        <input type="text" name="lastname" placeholder="นามสกุล..." class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">รหัสผ่าน</label>
        <input type="password" name="password" placeholder="******" class="form-control" id="password" required minlength="6">
      </div>
      <div class="mb-3">
        <label class="form-label">ยืนยันรหัสผ่าน</label>
        <input type="password" name="confirmpassword" placeholder="******" class="form-control" id="confirmpassword" required minlength="6">
      </div>
      <div class="mb-3">
        <label class="form-label">ตำแหน่ง</label>
        <select name="role" class="form-select" required>
          <option value="1" text>Admin</option>
          <option value="2">Staff</option>
          <option value="3">Chef</option>
        </select>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">บันทึก</button>
      </div>
    </form>

  </div>
</div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="canvaseditemployee" aria-labelledby="canvaseditemployee">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">แก้ไขข้อมูลพนักงาน</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form id="formEditEmployee">

      <div class="mb-3">
        <label class="form-label">ชื่อผู้ใช้</label>
        <input type="text" name="editusername" id="editusername" class="form-control" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label">อีเมล</label>
        <input type="email" name="editemail" id="editemail" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">ชื่อ</label>
        <input type="text" name="editfirstname" id="editfirstname" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">นามสกุล</label>
        <input type="text" name="editlastname" id="editlastname" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">รหัสผ่าน</label>
        <input type="password" name="editpassword" class="form-control" id="editpassword" minlength="6">
      </div>
      <div class="mb-3">
        <label class="form-label">ยืนยันรหัสผ่าน</label>
        <input type="password" name="editconfirmpassword" class="form-control" id="editconfirmpassword" minlength="6">
      </div>
      <div class="mb-3">
        <label class="form-label">ตำแหน่ง</label>
        <select id="editrole" name="editrole" class="form-select" required <?= ($role !== 4) ? 'disabled' : '' ?>>
          <option value="1">Admin</option>
          <option value="2">Staff</option>
          <option value="3">Chef</option>
        </select>
      </div>

      <div class="modal-footer">
        <button id="btn-ConfirmEdit" class="btn btn-warning text-white" type="submit">ยืนยันการแก้ไข</button>
      </div>
    </form>
  </div>
</div>
</div>


<script>
  let employeeArray = [];
  const currentUserid = <?= json_encode($id) ?>;
  $(document).ready(function() {
    getEmployee();
  })

  function getEmployee() {
    $.ajax({
      url: '/api/api_manage_employee.php',
      method: 'GET',
      data: {
        case: 'getEmployee'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          employeeArray = response.data;
          let table = $('#employeeTableBody');
          table.empty();
          if (response.data.length == 0) {
            table.append(
              `<tr class="border-top">
                  <td colspan="6" class="text-center text-muted">ไม่มีข้อมูล</td>
              </tr>`
            )
          }
          let count = 1;
          response.data.forEach(em => {
            let btnedit = '';

            // ไม่ให้แก้/ลบ user ชื่อ 'admin'
            if (em.username !== 'admin') {
              btnedit = `<button class="btn btn-warning text-white btn-sm" id="btn-edit" data-employee='${JSON.stringify(em)}'>แก้ไข</button>`;

              // ถ้า role คน login ไม่ตรงกับ em → แสดงปุ่มลบ
              if (currentUserid !== em.id) {
                btnedit += ` <button class="btn btn-danger btn-sm" onClick="deleteEmployee(${em.id})">ลบ</button>`;
              }
            }
            table.append(
              `
              <tr>
                <td>${count}</td>
                <td>${em.username}</td>
                <td>${em.email}</td>
                <td>${em.firstname}</td>
                <td>${em.lastname}</td>
                <td>${em.rolename}</td>
                <td>
                    ${btnedit}
                </td>
              </tr>
              `
            )
            count++;
          });
        }
      }
    })
  }
  $('#formEmployee').on('submit', function(e) {
    e.preventDefault();
    console.log(employeeArray);

    const found = employeeArray.find(item => item.username === $("#username").val().toLowerCase());
    console.log(found);

    if (found) {
      Swal.fire('ผิดพลาด', 'username นี้มีผู้ใช้แล้ว', 'error');
      return;
    }

    if ($('#password').val() !== $('#confirmpassword').val()) {
      Swal.fire('ผิดพลาด', 'รหัสผ่านไม่ตรงกัน', 'error');
      return;
    }

    Swal.fire({
      title: `คุณต้องการเพิ่มพนักงานใช่หรือไม่ ?`,
      showDenyButton: true,
      confirmButtonText: "ใช่",
      denyButtonText: `ยกเลิก`
    }).then((result) => {
      if (result.isConfirmed) {
        let formData = new FormData(this);
        let username = $('#username').val().toLowerCase();
        formData.set('username', username);
        formData.append('case', 'create_employee');
        for (let pars of formData.entries()) {
          console.log(pars[0], pars[1]);

        }
        $.ajax({
          url: '/api/api_manage_employee.php',
          method: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function(response) {
            if (response.status === 'success') {
              console.log(response.data);

              Swal.fire('สำเร็จ', response.message, 'success');
              $('#formEmployee')[0].reset();
              bootstrap.Offcanvas.getInstance(document.getElementById("canvasaddemployee")).hide();
              getEmployee();
            } else {
              Swal.fire('ผิดพลาด', response.message, 'error');
            }
          },
          error: function(xhr, status, error) {
            console.error('AJAX error:', status, error);
            Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์', 'error');
          }
        });
      }
    });
  });

  function deleteEmployee(id) {
    Swal.fire({
      title: `คุณต้องการลบข้อมูลพนักงานใช่หรือไม่ ?`,
      showDenyButton: true,
      confirmButtonText: "ใช่",
      denyButtonText: `ยกเลิก`
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '/api/api_manage_employee.php',
          method: 'POST',
          data: {
            id: id,
            case: 'delete_employee'
          },
          dataType: 'json',
          success: function(response) {
            if (response.status === 'success') {
              Swal.fire('สำเร็จ', response.message, 'success')
              getEmployee();
            } else {
              Swal.fire('ผิดพลาด', response.message, 'error')
            }
          },
          error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดบางอย่าง', 'error');
          }


        })
      }
    })
  }



  $(document).on('click', '#btn-edit', function(e) {
    e.preventDefault();
    const em = JSON.parse($(this).attr('data-employee'));
    $('#editusername').val(em.username);
    $('#editemail').val(em.email);
    $('#editfirstname').val(em.firstname);
    $('#editlastname').val(em.lastname);
    $('#editlastname').val(em.lastname);
    $('#editrole').val(em.role);
    $('#canvaseditemployee').offcanvas('show');
  });

  $('#formEditEmployee').on('click', '#btn-ConfirmEdit', function(e) {
    e.preventDefault();
    const password = $('#editpassword').val();
    const confirmPassword = $('#editconfirmpassword').val();

    if (password) {
      if (password.length < 6) {
        Swal.fire('ผิดพลาด', 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร', 'error');
        return;
      }
      if (password !== confirmPassword) {
        Swal.fire('ผิดพลาด', 'password ไม่ตรงกัน', 'error');
        return;
      }
    }

    Swal.fire({
      title: `คุณต้องการแก้ไขข้อมูลพนักงานใช่หรือไม่ ?`,
      showDenyButton: true,
      confirmButtonText: "ใช่",
      denyButtonText: `ยกเลิก`
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.getElementById('formEditEmployee');
        let formData = new FormData(form);
        formData.append('case', 'edit_employee')
        formData.append('editusername', $('#editusername').val())
        for (let par of formData.entries()) {
          console.log(par[0], par[1]);


        }

        $.ajax({
          url: '/api/api_manage_employee.php',
          method: 'POST',
          data: formData,
          dataType: 'json',
          processData: false, // ห้ามแปลงข้อมูล
          contentType: false, // ให้ browser จัดการ header เอง
          success: function(response) {
            if (response.status === 'success') {
              Swal.fire('สำเร็จ', response.message, 'success');
              getEmployee();
              $('#canvaseditemployee').offcanvas('hide')
            } else {
              Swal.fire('ผิดพลาด', response.message, 'error');
            }
          },
          error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดบางอย่าง', 'error');
          }
        })
      }
    });
  });
</script>