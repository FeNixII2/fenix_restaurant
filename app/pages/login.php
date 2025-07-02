<div class=" d-flex justify-content-center align-items-center p-4 bg-login">
    <div class="login-box p-4">
        <form id="login">
            <h1 class="h4 mb-4 fw-bold text-center"><img src="/assets/images/letter-f.png" alt="" style="width:3rem;height:3rem"> enix Restaurant</h1>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" placeholder="ชื่อผู้ใช้" required>
                <label for="username">ชื่อผู้ใช้</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" placeholder="รหัสผ่าน" required>
                <label for="password">รหัสผ่าน</label>
            </div>
            <button class="btn btn-warning text-white w-100" type="submit">เข้าสู่ระบบ</button>
        </form>
    </div>
</div>


<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">user_test</strong>
            <small>30 s left</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            ชื่อผู้ใช้ admin รหัสผ่าน 123456
        </div>
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

    document.addEventListener('DOMContentLoaded', function() {
        var toastEl = document.getElementById('liveToast');
        var toast = new bootstrap.Toast(toastEl, {
            delay: 30000
        });
        toast.show();
    });


  
</script>