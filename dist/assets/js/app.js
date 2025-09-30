/**======================================================================
=========================================================================
Template Name: Berry - Bootstrap Admin Template
Author: codedthemes
Support: https://codedthemes.authordesk.app
File: app.js - Application JavaScript Functions
=========================================================================
=================================================================================== */

/* Application Initialize */
document.addEventListener('DOMContentLoaded', function () {
    // Check if we're on the menu page
    if (document.getElementById('formTambahMenu')) {
        initializeMenuForm();
    }
    
    // Check if we're on the kategori page
    if (document.getElementById('formTambahKategori')) {
        initializeKategoriForm();
    }
});

/* Menu Management Functions */
function initializeMenuForm() {
    // Form elements
    const form = document.getElementById('formTambahMenu');
    const namaMenu = document.getElementById('namaMenu');
    const kategori = document.getElementById('kategori');
    const harga = document.getElementById('harga');
    const deskripsi = document.getElementById('deskripsi');
    const status = document.getElementById('status');
    const gambarInput = document.getElementById('gambarMenu');

    // Preview elements
    const previewNama = document.getElementById('previewNama');
    const previewKategori = document.getElementById('previewKategori');
    const previewHarga = document.getElementById('previewHarga');
    const previewDeskripsi = document.getElementById('previewDeskripsi');
    const previewLevel = document.getElementById('previewLevel');
    const previewStatus = document.getElementById('previewStatus');
    const previewCardImg = document.getElementById('previewCardImg');

    // Update preview in real-time
    function updatePreview() {
        // Nama menu
        previewNama.textContent = namaMenu.value || 'Nama Menu';

        // Kategori
        const katText = kategori.options[kategori.selectedIndex].text;
        previewKategori.textContent = katText !== 'Pilih Kategori' ? katText : 'Kategori';
        previewKategori.className = `badge position-absolute top-0 start-0 m-2 ${kategori.value === 'makanan' ? 'bg-primary' : kategori.value === 'minuman' ? 'bg-info' : 'bg-secondary'}`;

        // Harga
        const hargaVal = parseInt(harga.value) || 0;
        previewHarga.textContent = 'Rp ' + hargaVal.toLocaleString('id-ID');

        // Deskripsi
        previewDeskripsi.textContent = deskripsi.value || 'Deskripsi menu...';

        // Hide level badge since we removed level pedas
        previewLevel.style.display = 'none';

        // Status
        previewStatus.textContent = status.value === 'aktif' ? 'Aktif' : 'Nonaktif';
        previewStatus.className = `badge position-absolute top-0 end-0 m-2 ${status.value === 'aktif' ? 'bg-success' : 'bg-secondary'}`;
    }

    // Event listeners for real-time preview
    [namaMenu, kategori, harga, deskripsi, status].forEach(element => {
        element.addEventListener('input', updatePreview);
        element.addEventListener('change', updatePreview);
    });

    // Image preview
    gambarInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                gambarInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                // Show preview in form
                document.getElementById('imagePreview').style.display = 'block';
                document.getElementById('previewImg').src = e.target.result;

                // Update card preview
                previewCardImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Form submission
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Validate required fields
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="ti ti-loader rotate"></i> Menyimpan...';
        submitBtn.disabled = true;

        // Simulate save process (replace with actual AJAX call)
        setTimeout(function () {
            // Reset form
            form.reset();
            form.classList.remove('was-validated');
            updatePreview();
            removeImagePreview();

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('modalTambahMenu')).hide();

            // Show success message
            showSuccessMessage('Menu berhasil ditambahkan!');

            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 2000);
    });

    // Initialize preview on modal show
    document.getElementById('modalTambahMenu').addEventListener('shown.bs.modal', function () {
        updatePreview();
    });
}

/* Kategori Management Functions */
function initializeKategoriForm() {
    // Form elements
    const form = document.getElementById('formTambahKategori');
    const namaKategori = document.getElementById('namaKategori');
    const deskripsiKategori = document.getElementById('deskripsiKategori');
    const statusKategori = document.getElementById('statusKategori');

    // Preview elements
    const previewNama = document.getElementById('previewNama');
    const previewDeskripsi = document.getElementById('previewDeskripsi');
    const previewStatus = document.getElementById('previewStatus');
    const previewIcon = document.getElementById('previewIcon');

    // Update preview in real-time
    function updateKategoriPreview() {
        // Nama kategori
        previewNama.textContent = namaKategori.value || 'Nama Kategori';

        // Deskripsi
        previewDeskripsi.textContent = deskripsiKategori.value || 'Deskripsi kategori akan muncul di sini...';

        // Status
        if (statusKategori.value === 'aktif') {
            previewStatus.textContent = 'Aktif';
            previewStatus.className = 'badge bg-success';
        } else {
            previewStatus.textContent = 'Nonaktif';
            previewStatus.className = 'badge bg-secondary';
        }

        // Update icon based on category name
        const nama = namaKategori.value.toLowerCase();
        if (nama.includes('makanan') || nama.includes('food')) {
            previewIcon.className = 'ti ti-chef-hat f-24';
        } else if (nama.includes('minuman') || nama.includes('drink') || nama.includes('beverage')) {
            previewIcon.className = 'ti ti-glass f-24';
        } else if (nama.includes('snack') || nama.includes('cemilan')) {
            previewIcon.className = 'ti ti-cookie f-24';
        } else {
            previewIcon.className = 'ti ti-category f-24';
        }
    }

    // Event listeners for real-time preview
    [namaKategori, deskripsiKategori, statusKategori].forEach(element => {
        element.addEventListener('input', updateKategoriPreview);
        element.addEventListener('change', updateKategoriPreview);
    });

    // Form validation
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Validate required fields
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        // Additional custom validation
        if (namaKategori.value.trim().length < 3) {
            namaKategori.setCustomValidity('Nama kategori minimal 3 karakter');
            namaKategori.classList.add('is-invalid');
            return;
        } else {
            namaKategori.setCustomValidity('');
            namaKategori.classList.remove('is-invalid');
        }

        if (deskripsiKategori.value.trim().length < 10) {
            deskripsiKategori.setCustomValidity('Deskripsi minimal 10 karakter');
            deskripsiKategori.classList.add('is-invalid');
            return;
        } else {
            deskripsiKategori.setCustomValidity('');
            deskripsiKategori.classList.remove('is-invalid');
        }

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="ti ti-loader rotate"></i> Menyimpan...';
        submitBtn.disabled = true;

        // Simulate save process (replace with actual AJAX call)
        setTimeout(function () {
            // Reset form
            form.reset();
            form.classList.remove('was-validated');
            updateKategoriPreview();

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('modalTambahKategori')).hide();

            // Show success message
            showSuccessMessage('Kategori berhasil ditambahkan!');

            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;

            // Optionally refresh the table data here
            // refreshKategoriTable();
        }, 2000);
    });

    // Initialize preview on modal show
    document.getElementById('modalTambahKategori').addEventListener('shown.bs.modal', function () {
        updateKategoriPreview();
        // Focus on first input
        namaKategori.focus();
    });

    // Real-time validation feedback
    namaKategori.addEventListener('input', function() {
        if (this.value.trim().length >= 3) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
        }
    });

    deskripsiKategori.addEventListener('input', function() {
        if (this.value.trim().length >= 10) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
        }
    });
}

// Helper functions
function removeImagePreview() {
    if (document.getElementById('imagePreview')) {
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('gambarMenu').value = '';
        document.getElementById('previewCardImg').src = 'https://via.placeholder.com/400x250/e9ecef/6c757d?text=No+Image';
    }
}

function showSuccessMessage(message) {
    // Create and show success alert
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        <i class="ti ti-check-circle"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);

    // Auto remove after 3 seconds
    setTimeout(function () {
        alertDiv.remove();
    }, 3000);
}

// CSS for rotating loader
const style = document.createElement('style');
style.textContent = `
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .rotate {
        animation: rotate 1s linear infinite;
    }
`;
document.head.appendChild(style);