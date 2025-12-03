<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-header-title">
                    <h5 class="m-b-10">Pengaturan Akun</h5>
                </div>
            </div>
            <div class="col-auto">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page">Pengaturan Akun</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- Profile Information -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5>Informasi Profil</h5>
            </div>
            <div class="card-body">
                <form id="formUpdateProfile">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" id="username"
                            value="<?= htmlspecialchars($current_user['username'] ?? '') ?>" readonly>
                        <small class="text-muted">Username tidak dapat diubah</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name"
                            value="<?= htmlspecialchars($current_user['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email"
                            value="<?= htmlspecialchars($current_user['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No Telepon </label>
                        <input type="text" class="form-control" id="phone"
                            value="<?= htmlspecialchars($current_user['phone'] ?? '') ?>">
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="resetProfileForm()">Reset</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>
<!-- [ Main Content ] end -->

<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = event.target.closest('button').querySelector('i');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('ti-eye');
            icon.classList.add('ti-eye-off');
        } else {
            field.type = 'password';
            icon.classList.remove('ti-eye-off');
            icon.classList.add('ti-eye');
        }
    }

    // Reset profile form
    function resetProfileForm() {
        document.getElementById('formUpdateProfile').reset();
        document.getElementById('name').value = '<?= htmlspecialchars($current_user['name'] ?? '') ?>';
        document.getElementById('email').value = '<?= htmlspecialchars($current_user['email'] ?? '') ?>';
        document.getElementById('phone').value = '<?= htmlspecialchars($current_user['phone'] ?? '') ?>';
    }

    // Reset password form
    function resetPasswordForm() {
        document.getElementById('formChangePassword').reset();
    }

    // Handle profile update
    document.getElementById('formUpdateProfile').addEventListener('submit', function (e) {
        e.preventDefault();

        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;

        showLoading('Memperbarui Profil', 'Mohon tunggu...');

        fetch('api/account-settings.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                email: email,
                phone: phone
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Berhasil!', 'Profil berhasil diperbarui');
                    // Reload page after 1 second to update session data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showError('Gagal!', data.message || 'Gagal memperbarui profil');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error!', 'Terjadi kesalahan saat memperbarui profil');
            });
    });

    // Handle password change
    document.getElementById('formChangePassword').addEventListener('submit', function (e) {
        e.preventDefault();

        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        // Validate password match
        if (newPassword !== confirmPassword) {
            showError('Error!', 'Password baru dan konfirmasi password tidak sama');
            return;
        }

        // Validate password length
        if (newPassword.length < 6) {
            showError('Error!', 'Password baru minimal 6 karakter');
            return;
        }

        showLoading('Mengubah Password', 'Mohon tunggu...');

        fetch('api/account-settings.php', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Berhasil!', 'Password berhasil diubah');
                    resetPasswordForm();
                } else {
                    showError('Gagal!', data.message || 'Gagal mengubah password');
                }
   })
            .catch(error => {
                console.error('Error:', error);
                showError('Error!', 'Terjadi kesalahan saat mengubah password');
            });
    });
</script>