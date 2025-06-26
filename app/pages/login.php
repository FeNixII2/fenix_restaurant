<div class="bg-login d-flex justify-content-center align-items-center p-4">
    <div class="login-box p-4">
        <form id="login">
            <h1 class="h4 mb-4 fw-bold text-center">Fenix Restaurant</h1>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" placeholder="ชื่อผู้ใช้" required>
                <label for="username">ชื่อผู้ใช้</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" placeholder="รหัสผ่าน" required>
                <label for="password">รหัสผ่าน</label>
            </div>
            <button class="btn btn-primary w-100" type="submit">เข้าสู่ระบบ</button>
        </form>
    </div>
</div>
<script>
    $('#login').on('submit', function(e) {
        e.preventDefault()
        $.ajax({
            url: '/api/api_login.php',
            method: 'POST',
            data: {
                case: 'login',
                username: $('#username').val(),
                password: $('#password').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = '/dashboard';
                } else {
                    Swal.fire('ผิดพลาด', response.message, 'error');
                }
            }
        })
    })
</script>