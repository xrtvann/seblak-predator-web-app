<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-header-title">
                    <h5 class="m-b-10" id="pageTitleText">Role Management</h5>
                </div>
            </div>
            <div class="col-auto">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page" id="breadcrumbText">Role</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Information Cards ] start -->
<div class="row mb-4" id="informationCards">
    <div class="col-md-6 col-xxl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-primary">
                            <i class="ti ti-shield f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Role</h6>
                        <b class="text-primary" id="totalRolesCount">0</b>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xxl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-danger">
                            <i class="ti ti-crown f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Owner Users</h6>
                        <b class="text-danger" id="ownerUsersCount">0</b>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xxl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-warning">
                            <i class="ti ti-user-shield f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Admin Users</h6>
                        <b class="text-warning" id="adminUsersCount">0</b>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xxl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-info">
                            <i class="ti ti-users f-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Customer Users</h6>
                        <b class="text-info" id="customerUsersCount">0</b>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Information Cards ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- [ Data Role ] start -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 id="cardTitleText">Data Role</h5>
                        <p class="text-muted mb-0 d-none" id="cardSubtitleText">Isi form di bawah untuk menambahkan
                            role
                            baru</p>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center" id="headerActions">
                            <!-- Action Buttons -->
                            <button type="button" class="btn btn-primary d-flex" id="btnTambahRole"
                                onclick="showFormTambah()">
                                <i class="ti ti-plus me-2"></i> Tambah Role
                            </button>
                            <button type="button" class="btn btn-secondary d-none" id="btnKembali"
                                onclick="showDataRole()">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Main Content Area -->
                <div id="mainContentArea">
                    <!-- Content will be loaded here dynamically -->
                </div>
            </div>
        </div>
    </div>
    <!-- [ Data Role ] end -->
</div>
<!-- [ Main Content ] end -->

<!-- Enhanced Image Display Styles -->
<style>
    /* Table View Image Enhancements */
    .role-image-container {
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .role-image-container:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .role-image {
        transition: opacity 0.3s ease;
    }

    .role-image:hover {
        opacity: 0.9;
    }

    /* Card View Image Enhancements */
    .role-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .role-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .role-card-image {
        transition: transform 0.3s ease;
    }

    .role-card:hover .role-card-image {
        transform: scale(1.1);
    }

    .card-image-container {
        border-radius: 0.375rem 0.375rem 0 0;
    }

    .image-overlay {
        background: linear-gradient(45deg, transparent 70%, rgba(0, 0, 0, 0.1) 100%);
        border-radius: 0 0.375rem 0 0;
    }

    /* Loading states */
    .image-loading {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .role-image-container {
            width: 50px !important;
            height: 35px !important;
        }

        .role-image {
            width: 50px !important;
            height: 35px !important;
        }

        .role-card-image {
            height: 120px !important;
        }
    }

    /* Image error state */
    .role-image[src*="unsplash"] {
        filter: sepia(20%) saturate(120%) hue-rotate(15deg);
    }

    /* Topping badge styling */
    .badge.bg-light-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
        border: 1px solid rgba(255, 193, 7, 0.2);
    }

    /* Table container */
    .table-container {
        position: relative;
        border: 1px solid #dee2e6;
        border-radius: 0 0 0.375rem 0.375rem;
        border-top: none;
        overflow: hidden;
    }

    /* Scrollable table container */
    .table-responsive {
        border: none;
        border-radius: 0;
        position: relative;
        background: white;
    }

    /* Sticky table header */
    .table-header-sticky {
        position: sticky;
        top: 0;
        z-index: 1020;
        background-color: var(--bs-gray-100) !important;
    }

    .table-header-sticky th {
        background-color: var(--bs-gray-100) !important;
        border-bottom: 2px solid #dee2e6;
        border-top: 1px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 1rem 0.75rem;
        position: sticky;
        top: 0;
        z-index: 1020;
        border-left: none;
        border-right: none;
        background-clip: padding-box;
    }

    .table-header-sticky th:last-child {
        border-right: none;
    }

    /* Ensure table borders don't scroll with content */
    .table-responsive table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-responsive .table thead th {
        border-left: none;
        border-right: none;
    }

    .table-responsive .table thead th:first-child {
        border-left: none;
    }

    /* External controls section styling */
    .table-controls-section {
        background-color: var(--bs-gray-100) !important;
        border: 1px solid #dee2e6 !important;
        border-bottom: none !important;
        border-radius: 0.375rem 0.375rem 0 0 !important;
    }

    /* Custom scrollbar styling */
    .table-responsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Table header controls layout */
    .table-header-controls {
        display: flex;
        align-items: center;
        justify-content: start;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* Search section */
    .search-section {
        flex: 1;
        min-width: 250px;
        max-width: 400px;
    }

    .search-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-input {
        background: white;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 8px 12px 8px 35px;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        width: 100%;
    }

    .search-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .search-icon {
        position: absolute;
        left: 10px;
        color: #6c757d;
        font-size: 16px;
        z-index: 1;
    }

    /* Filter section */
    .filter-section {
        flex-shrink: 0;
    }

    .filter-dropdown {
        position: relative;
    }

    .filter-btn {
        background: white;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 8px 16px;
        color: #495057;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        position: relative;
        min-width: 90px;
        font-size: 14px;
    }

    .filter-btn:hover {
        border-color: #667eea;
        background: #f8f9fa;
        color: #667eea;
    }

    .filter-btn.active {
        background: #667eea;
        border-color: #667eea;
        color: white;
    }

    .filter-badge {
        background: #dc3545;
        color: white;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 5px;
        border-radius: 8px;
        position: absolute;
        top: -3px;
        right: -3px;
        min-width: 16px;
        text-align: center;
        line-height: 1;
    }

    /* Filter Dropdown */
    .filter-dropdown-menu {
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        background: white;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        min-width: 280px;
        z-index: 1050;
        display: none;
        animation: dropdownSlide 0.2s ease;
    }

    .filter-dropdown-menu.show {
        display: block;
    }

    @keyframes dropdownSlide {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .filter-dropdown-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
        border-radius: 8px 8px 0 0;
    }

    .filter-dropdown-header h6 {
        color: #495057;
        font-weight: 600;
        font-size: 14px;
        margin: 0;
    }

    .btn-close-filter {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 14px;
        cursor: pointer;
        padding: 2px;
        border-radius: 3px;
        transition: all 0.2s ease;
    }

    .btn-close-filter:hover {
        background: #e9ecef;
        color: #495057;
    }

    .filter-dropdown-body {
        padding: 16px;
        max-height: 300px;
        overflow-y: auto;
    }

    .filter-group {
        margin-bottom: 1.5rem;
    }

    .filter-group:last-child {
        margin-bottom: 0;
    }

    .filter-group-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.75rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
    }

    .filter-options {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .filter-option {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.15s ease;
        margin-bottom: 0;
    }

    .filter-option:hover {
        background: #f8f9fa;
    }

    .filter-checkbox {
        margin: 0;
    }

    .filter-icon {
        font-size: 1rem;
        width: 20px;
        text-align: center;
    }

    .filter-label {
        flex: 1;
        font-size: 0.875rem;
        color: #495057;
        margin: 0;
    }

    /* Sort section */
    .sort-section {
        flex-shrink: 0;
    }

    .sort-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .sort-icon {
        position: absolute;
        left: 8px;
        color: #6c757d;
        font-size: 14px;
        z-index: 1;
    }

    .sort-select {
        background: white;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 8px 12px 8px 30px;
        font-size: 13px;
        min-width: 120px;
        transition: all 0.3s ease;
        appearance: none;
        background-image: url('data:image/svg+xml;utf8,<svg fill="%23666" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 12px;
    }

    .sort-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    /* Active Filters */
    .active-filters-container {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #e9ecef;
    }

    .active-filters-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .active-filters-label {
        font-size: 13px;
        color: #6c757d;
        font-weight: 500;
    }

    .active-filters-tags {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .active-filter-tag {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
        animation: tagFadeIn 0.2s ease;
    }

    .remove-filter {
        background: rgba(255, 255, 255, 0.3);
        border: none;
        color: white;
        border-radius: 50%;
        width: 12px;
        height: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .remove-filter:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    /* Enhanced table styling */
    .table td,
    .table th {
        vertical-align: middle;
        padding: 12px 8px;
    }

    /* Row hover effects */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
        transform: translateY(-1px);
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Button group spacing */
    .btn-group .btn {
        margin: 0 1px;
    }

    /* Table light styling */
    .table-light {
        background-color: var(--bs-gray-100) !important;
    }

    .column-headers th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 1rem 0.75rem;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .table-header-controls {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .search-section,
        .sort-section {
            min-width: unset;
            max-width: unset;
        }

        .filter-section {
            align-self: center;
        }

        .filter-dropdown-menu {
            left: 50%;
            transform: translateX(-50%) translateY(-10px);
            width: calc(100vw - 2rem);
            max-width: 300px;
        }

        .filter-dropdown-menu.show {
            transform: translateX(-50%) translateY(0);
        }

        .active-filters-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }

        .table-responsive {
            max-height: 400px;
            font-size: 0.875rem;
        }

        .table td,
        .table th {
            padding: 8px 6px;
        }
    }

    /* Pagination styling */
    .pagination-sm .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }

    .pagination .page-link {
        color: #6c757d;
        border: 1px solid #dee2e6;
        margin: 0 2px;
        transition: all 0.2s ease;
    }

    .pagination .page-link:hover {
        color: #0d6efd;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        transform: translateY(-1px);
    }

    .pagination .page-item.disabled .page-link {
        color: #adb5bd;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }

    #paginationInfo {
        font-size: 0.875rem;
        color: #6c757d;
    }

    /* Pagination container responsive */
    @media (max-width: 576px) {
        .pagination-sm .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        #paginationInfo {
            font-size: 0.75rem;
        }
    }

    /* Table Controls Section */
    .table-controls-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        border: 1px solid #dee2e6;
    }

    .table-header-controls {
        display: flex;
        align-items: center;
        justify-content: start;
        gap: 15px;
        flex-wrap: wrap;
    }

    .search-section {
        flex: 1;
        min-width: 250px;
        max-width: 400px;
    }

    .search-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-input {
        background: white;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 8px 12px 8px 35px;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        width: 100%;
    }

    .search-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .search-icon {
        position: absolute;
        left: 10px;
        color: #6c757d;
        font-size: 16px;
        z-index: 1;
    }

    /* Filter Section */
    .filter-section {
        flex-shrink: 0;
    }

    .filter-dropdown {
        position: relative;
    }

    .filter-btn {
        background: white;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 8px 16px;
        color: #495057;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        position: relative;
        min-width: 90px;
        font-size: 14px;
    }

    .filter-btn:hover {
        border-color: #667eea;
        background: #f8f9fa;
        color: #667eea;
    }

    .filter-btn.active {
        background: #667eea;
        border-color: #667eea;
        color: white;
    }

    .filter-badge {
        background: #dc3545;
        color: white;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 5px;
        border-radius: 8px;
        position: absolute;
        top: -3px;
        right: -3px;
        min-width: 16px;
        text-align: center;
        line-height: 1;
    }

    /* Filter Dropdown */
    .filter-dropdown-menu {
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        background: white;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        min-width: 280px;
        z-index: 1050;
        display: none;
        animation: dropdownSlide 0.2s ease;
    }

    .filter-dropdown-menu.show {
        display: block;
    }

    @keyframes dropdownSlide {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .filter-dropdown-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
        border-radius: 8px 8px 0 0;
    }

    .filter-dropdown-header h6 {
        color: #495057;
        font-weight: 600;
        font-size: 14px;
        margin: 0;
    }

    .btn-close-filter {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 14px;
        cursor: pointer;
        padding: 2px;
        border-radius: 3px;
        transition: all 0.2s ease;
    }

    .btn-close-filter:hover {
        background: #e9ecef;
        color: #495057;
    }

    .filter-dropdown-body {
        padding: 16px;
        max-height: 250px;
        overflow-y: auto;
    }

    .filter-group {
        margin-bottom: 16px;
    }

    .filter-group:last-child {
        margin-bottom: 0;
    }

    .filter-group-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-options {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 8px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin: 0;
        font-size: 13px;
    }

    .filter-option:hover {
        background: #f8f9fa;
    }

    .filter-checkbox {
        width: 14px;
        height: 14px;
        margin: 0;
        accent-color: #667eea;
    }

    .filter-icon {
        font-size: 12px;
    }

    .filter-label {
        font-size: 13px;
        color: #495057;
        font-weight: 500;
    }

    /* Sort Section */
    .sort-section {
        flex-shrink: 0;
    }

    .sort-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .sort-icon {
        position: absolute;
        left: 8px;
        color: #6c757d;
        font-size: 14px;
        z-index: 1;
    }

    .sort-select {
        background: white;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 8px 12px 8px 30px;
        font-size: 13px;
        min-width: 120px;
        transition: all 0.3s ease;
        appearance: none;
        background-image: url('data:image/svg+xml;utf8,<svg fill="%23666" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 12px;
    }

    .sort-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    /* Active Filters */
    .active-filters-container {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #e9ecef;
    }

    .active-filters-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .active-filters-label {
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        white-space: nowrap;
    }

    .active-filters-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    .active-filter-tag {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
        animation: tagFadeIn 0.2s ease;
    }

    .remove-filter {
        background: rgba(255, 255, 255, 0.3);
        border: none;
        color: white;
        border-radius: 50%;
        width: 12px;
        height: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .remove-filter:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    @keyframes tagFadeIn {
        from {
            opacity: 0;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Table Container */
    .table-container {
        position: relative;
        border: 1px solid #dee2e6;
        border-radius: 0 0 0.375rem 0.375rem;
        border-top: none;
        overflow: hidden;
    }

    .table-responsive {
        border: none;
        border-radius: 0;
        position: relative;
        background: white;
    }

    .table-header-sticky {
        position: sticky;
        top: 0;
        z-index: 1020;
        background-color: var(--bs-gray-100) !important;
    }

    .table-header-sticky th {
        background-color: var(--bs-gray-100) !important;
        border-bottom: 2px solid #dee2e6;
        border-top: 1px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 1rem 0.75rem;
        position: sticky;
        top: 0;
        z-index: 1020;
        border-left: none;
        border-right: none;
    }

    /* Column Headers */
    .column-headers {
        background: #f8f9fa !important;
        border-top: 1px solid #dee2e6;
    }

    .column-headers th {
        color: #495057 !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        padding: 12px 8px !important;
        border-bottom: 2px solid #dee2e6 !important;
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        .table-header-controls {
            flex-direction: column;
            gap: 10px;
        }

        .search-section,
        .filter-section,
        .sort-section {
            width: 100%;
        }
    }
</style>

<!-- JavaScript untuk CRUD Role -->
<script>
    let currentEditId = null;
    let allRolesData = [];
    let filteredRolesData = [];
    let currentPage = 1;
    const itemsPerPage = 10;
    let activeFilters = new Map();
    let currentSort = 'created_at_desc';
    let currentViewMode = 'active';

    // Filter Dropdown Management
    function toggleFilterDropdown() {
        const dropdown = document.getElementById('filterDropdown');
        const button = document.getElementById('filterButton');

        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
            button.classList.remove('active');
        } else {
            dropdown.classList.add('show');
            button.classList.add('active');
        }
    }

    // Handle filter checkbox changes
    function handleFilterChange(checkbox) {
        const filterType = checkbox.dataset.filter;
        const filterValue = checkbox.dataset.value;
        const filterKey = `${filterType}:${filterValue}`;

        console.log('Filter change:', filterType, filterValue, checkbox.checked);

        if (checkbox.checked) {
            const filterLabel = checkbox.parentElement.querySelector('.filter-label').textContent;
            activeFilters.set(filterKey, {
                type: filterType,
                value: filterValue,
                label: filterLabel,
                element: checkbox
            });
            console.log('Added filter:', filterKey, filterLabel);
        } else {
            activeFilters.delete(filterKey);
            console.log('Removed filter:', filterKey);
        }

        console.log('Active filters:', Array.from(activeFilters.keys()));
        updateFilterBadge();
        updateActiveFiltersDisplay();
        applyFilters();
    }

    // Update filter badge count
    function updateFilterBadge() {
        const badge = document.getElementById('filterBadge');
        const count = activeFilters.size;

        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }

    // Update active filters display
    function updateActiveFiltersDisplay() {
        const container = document.getElementById('activeFiltersContainer');
        const tagsContainer = document.getElementById('activeFiltersTags');

        if (activeFilters.size === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';
        tagsContainer.innerHTML = '';

        activeFilters.forEach((filter, key) => {
            const tag = document.createElement('div');
            tag.className = 'active-filter-tag';
            tag.innerHTML = `
                <span>${filter.label}</span>
                <button class="remove-filter" onclick="removeFilter('${key}')">×</button>
            `;
            tagsContainer.appendChild(tag);
        });
    }

    // Remove specific filter
    function removeFilter(filterKey) {
        const filter = activeFilters.get(filterKey);
        if (filter && filter.element) {
            filter.element.checked = false;
        }

        if (filterKey === 'view_mode:deleted') {
            currentViewMode = 'active';
            loadRoleData(false);
        }

        activeFilters.delete(filterKey);
        updateFilterBadge();
        updateActiveFiltersDisplay();

        if (filterKey !== 'view_mode:deleted') {
            applyFilters();
        }
    }

    // Handle view mode change
    // View mode change not needed for roles (no soft delete)
    function handleViewModeChange(checkbox) {
        // Roles don't have soft delete, so this function is not used
        console.log('handleViewModeChange called but not implemented for roles');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        const dropdown = document.getElementById('filterDropdown');
        const button = document.getElementById('filterButton');

        if (dropdown && dropdown.classList.contains('show')) {
            if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                toggleFilterDropdown();
            }
        }
    });

    // Show form for adding new role
    function showFormTambah() {
        currentEditId = null;
        showForm('Tambah Role', 'Form Tambah Role');
    }

    // Load role data from API
    async function loadRoleData(showDeleted = false) {
        try {
            let url = 'api/roles.php?per_page=1000';

            console.log('Loading role data from:', url);
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            console.log('API Response:', result);

            if (result.success) {
                allRolesData = result.data || [];
                filteredRolesData = [...allRolesData];

                currentPage = 1;

                console.log('Role data loaded:', allRolesData.length, 'items');
                console.log('Statistics:', result.statistics);

                // Update information cards with statistics
                if (result.statistics) {
                    updateInformationCards(result.statistics);
                } else {
                    console.warn('No statistics in API response');
                    updateInformationCards({
                        total: allRolesData.length,
                        owner_roles: 0,
                        admin_roles: 0,
                        customer_roles: 0
                    });
                }

                applyFilters();
            } else {
                throw new Error(result.message || 'Failed to load data');
            }
        } catch (error) {
            console.error('Error loading role data:', error);
            showError('Gagal memuat data role: ' + error.message);
        }
    }

    // Restore view mode checkbox state
    function restoreViewModeCheckbox() {
        const checkbox = document.querySelector('input[data-filter="view_mode"][data-value="deleted"]');
        if (checkbox) {
            checkbox.checked = currentViewMode === 'deleted';
            console.log('Checkbox state restored:', checkbox.checked, 'Current view mode:', currentViewMode);
        }
    }

    // Show data role view
    function showDataRole() {
        console.log('showDataRole called');
        const mainContent = document.getElementById('mainContentArea');
        console.log('Main content element:', mainContent);

        document.getElementById('pageTitleText').textContent = 'Role';
        document.getElementById('breadcrumbText').textContent = 'Role';
        document.getElementById('cardTitleText').textContent = 'Data Role';
        document.getElementById('cardSubtitleText').classList.add('d-none');

        document.getElementById('btnTambahRole').classList.remove('d-none');
        document.getElementById('btnKembali').classList.add('d-none');

        mainContent.innerHTML = getDataRoleHTML();

        loadRoleData();
    }

    // Apply filters
    function applyFilters() {
        filteredRolesData = [...allRolesData];

        // Apply search filter
        const searchInput = document.getElementById('searchInput');
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

        if (searchTerm) {
            filteredRolesData = filteredRolesData.filter(role =>
                (role.name || '').toLowerCase().includes(searchTerm) ||
                (role.id || '').toLowerCase().includes(searchTerm)
            );
        }

        // Apply sorting
        applySorting();
    }

    // Apply sorting
    function applySorting() {
        const sortBy = document.getElementById('sortBy');
        if (sortBy) {
            currentSort = sortBy.value;
        }

        filteredRolesData.sort((a, b) => {
            switch (currentSort) {
                case 'name_asc':
                    return (a.name || '').localeCompare(b.name || '');
                case 'name_desc':
                    return (b.name || '').localeCompare(a.name || '');
                case 'role_count_asc':
                    return (a.role_count || 0) - (b.role_count || 0);
                case 'role_count_desc':
                    return (b.role_count || 0) - (a.role_count || 0);
                case 'created_at_asc':
                    return new Date(a.created_at) - new Date(b.created_at);
                case 'created_at_desc':
                default:
                    return new Date(b.created_at) - new Date(a.created_at);
            }
        });

        currentPage = 1;
        displayTableView();
        updatePagination();
    }

    // Show form view
    function showForm(title, subtitle, data = null) {
        const mainContent = document.getElementById('mainContentArea');

        document.getElementById('pageTitleText').textContent = title;
        document.getElementById('breadcrumbText').textContent = title;
        document.getElementById('cardTitleText').textContent = title;
        document.getElementById('cardSubtitleText').textContent = subtitle;
        document.getElementById('cardSubtitleText').classList.remove('d-none');

        document.getElementById('btnTambahRole').classList.add('d-none');
        document.getElementById('btnKembali').classList.remove('d-none');

        mainContent.innerHTML = getFormHTML();

        const form = document.getElementById('roleForm');
        form.addEventListener('submit', handleFormSubmit);

        if (data) {
            populateForm(data);
        } else {
            // Reset form for new role
            const passwordField = document.getElementById('rolePassword');
            passwordField.required = true;
            passwordField.placeholder = 'Masukkan password (minimal 6 karakter)';
            document.getElementById('submitText').textContent = 'Simpan Role';
        }
    }

    // Get data role HTML
    function getDataRoleHTML() {
        return `
            <!-- Table Controls Section -->
            <div class="table-controls-section table-light p-3 mb-0 border rounded-top">
                <div class="table-header-controls">
                    <div class="search-section">
                        <div class="search-input-wrapper">
                            <i class="ti ti-search search-icon"></i>
                            <input type="text" class="form-control search-input" id="searchInput" 
                                   placeholder="Cari role berdasarkan nama atau ID..." onkeyup="applyFilters()" onchange="applyFilters()">
                        </div>
                    </div>
                    
                    <div class="sort-section">
                        <div class="sort-wrapper">
                            <i class="ti ti-sort-descending sort-icon"></i>
                            <select class="form-select sort-select" id="sortBy" onchange="applySorting()">
                                <option value="created_at_desc">Terbaru</option>
                                <option value="created_at_asc">Terlama</option>
                                <option value="name_asc">Nama A-Z</option>
                                <option value="name_desc">Nama Z-A</option>
                                <option value="role_count_desc">User Terbanyak</option>
                                <option value="role_count_asc">User Tersedikit</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Table Container -->
            <div class="table-container">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead class="table-light table-header-sticky">
                            <tr class="column-headers">
                                <th style="min-width: 50px;">#</th>
                                <th style="min-width: 150px;">Role ID</th>
                                <th style="min-width: 200px;">Nama Role</th>
                                <th style="min-width: 120px;">Jumlah User</th>
                                <th style="min-width: 150px;">Dibuat Pada</th>
                                <th style="min-width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="roleTableBody">
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    Loading role data...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="d-flex align-items-center">
                    <small class="text-muted" id="paginationInfo">Showing 0 - 0 of 0 entries</small>
                </div>
                <nav aria-label="Role pagination">
                    <ul class="pagination pagination-sm mb-0" id="paginationControls"></ul>
                </nav>
            </div>
        `;
    }

    // Get form HTML
    function getFormHTML() {
        return `
            <div class="card">
                <div class="card-body table-light p-4 rounded">
                    <form id="roleForm" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="roleName" class="form-label">Nama Role <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="roleName" name="name" required 
                                           placeholder="Masukkan nama role (contoh: Staff, Manager, dll)"
                                           maxlength="30">
                                    <small class="text-muted">Maksimal 30 karakter</small>
                                    <div class="invalid-feedback">Nama role wajib diisi (maksimal 30 karakter)</div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Informasi:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Role default (Owner, Admin, Customer) tidak dapat diubah atau dihapus</li>
                                <li>Role yang sudah memiliki user tidak dapat dihapus</li>
                                <li>Nama role harus unik dan belum digunakan</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-light border" onclick="showDataRole()">
                                <i class="ti ti-arrow-left me-1"></i> Kembali
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-check me-2"></i> <span id="submitText">Simpan Role</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;
    }

    // Display table view
    function displayTableView() {
        const tableBody = document.getElementById('roleTableBody');
        if (!tableBody) return;

        tableBody.innerHTML = '';

        if (filteredRolesData.length === 0) {
            const message = allRolesData.length === 0 ? 'Tidak ada data role' : 'Tidak ada data yang sesuai dengan filter';
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">
                        <p class="mb-0">${message}</p>
                    </td>
                </tr>
            `;
            return;
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedData = filteredRolesData.slice(startIndex, endIndex);

        paginatedData.forEach((item, index) => {
            const actualIndex = startIndex + index + 1;
            const isSystemRole = ['role_owner', 'role_admin', 'role_customer'].includes(item.id);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${actualIndex}</td>
                <td>
                    <code class="text-primary">${escapeHtml(item.id)}</code>
                </td>
                <td>
                    <h6 class="mb-0">${escapeHtml(item.name)}</h6>
                    ${isSystemRole ? '<small class="text-muted"><i class="ti ti-lock"></i> System Role</small>' : ''}
                </td>
                <td>
                    <span class="badge bg-light-info text-info">
                        <i class="ti ti-users"></i> ${item.user_count || 0} user${item.user_count !== 1 ? 's' : ''}
                    </span>
                </td>
                <td>
                    <small class="text-muted">${new Date(item.created_at).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            })}</small>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        ${!isSystemRole ? `
                            <button type="button" class="btn btn-sm btn-outline-warning me-2" onclick="editRole('${item.id}')" title="Edit Role">
                                <i class="ti ti-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRole('${item.id}', '${escapeHtml(item.name)}')" title="Delete Role">
                                <i class="ti ti-trash"></i>
                            </button>
                        ` : `
                            <span class="badge bg-light-secondary text-secondary">
                                <i class="ti ti-lock"></i> Protected
                            </span>
                        `}
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Update information cards
    function updateInformationCards(stats) {
        document.getElementById('totalRolesCount').textContent = stats.total || 0;
        document.getElementById('ownerUsersCount').textContent = stats.owner_users || 0;
        document.getElementById('adminUsersCount').textContent = stats.admin_users || 0;
        document.getElementById('customerUsersCount').textContent = stats.customer_users || 0;
    }

    // Handle form submit
    async function handleFormSubmit(e) {
        e.preventDefault();

        const form = e.target;
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        showLoading('Menyimpan...', 'Sedang memproses data role...');

        try {
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            // Validate name length
            if (!data.name || data.name.trim().length === 0) {
                hideAlert();
                showError('Gagal!', 'Nama role wajib diisi');
                return;
            }

            if (data.name.length > 30) {
                hideAlert();
                showError('Gagal!', 'Nama role maksimal 30 karakter');
                return;
            }

            let url = 'api/roles.php';
            let method = 'POST';

            if (currentEditId) {
                url += '?id=' + currentEditId;
                method = 'PUT';
            }

            const response = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            hideAlert();

            if (result.success) {
                currentEditId = null;
                showSuccess('Berhasil!', result.message, () => {
                    showDataRole();
                });
            } else {
                showError('Gagal!', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            hideAlert();
            showError('Kesalahan!', 'Terjadi kesalahan saat menghubungi server');
        }
    }

    // Edit role
    async function editRole(id) {
        try {
            // Check if system role
            const systemRoles = ['role_owner', 'role_admin', 'role_customer'];
            if (systemRoles.includes(id)) {
                showError('Tidak Diizinkan!', 'Role system tidak dapat diubah');
                return;
            }

            showLoading('Memuat...', 'Sedang memuat data role...');

            // Get single role by filtering from all data
            const role = allRolesData.find(r => r.id === id);

            if (role) {
                currentEditId = id;
                hideAlert();
                showForm('Edit Role', 'Form Edit Role', role);
            } else {
                hideAlert();
                showError('Gagal!', 'Data role tidak ditemukan');
            }
        } catch (error) {
            console.error('Error:', error);
            hideAlert();
            showError('Kesalahan!', 'Terjadi kesalahan saat memuat data');
        }
    }

    // Populate form for editing
    function populateForm(data) {
        console.log('Populating form with data:', data);
        document.getElementById('roleName').value = data.name || '';
        document.getElementById('submitText').textContent = 'Update Role';
    }

    // Delete role
    function deleteRole(id, name) {
        // Check if system role
        const systemRoles = ['role_owner', 'role_admin', 'role_customer'];
        if (systemRoles.includes(id)) {
            showError('Tidak Diizinkan!', 'Role system tidak dapat dihapus');
            return;
        }

        showDeleteConfirmation(name, async () => {
            showLoading('Menghapus...', 'Sedang menghapus role...');

            try {
                const response = await fetch(`api/roles.php?id=${id}`, {
                    method: 'DELETE'
                });

                const result = await response.json();
                hideAlert();

                if (result.success) {
                    showSuccess('Berhasil!', result.message, () => {
                        loadRoleData();
                    });
                } else {
                    showError('Gagal!', result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                hideAlert();
                showError('Kesalahan!', 'Terjadi kesalahan saat menghubungi server');
            }
        });
    }

    // Note: Roles don't have restore functionality
    // Roles are permanently deleted, not soft deleted

    // Permanently delete role
    function permanentDeleteRole(id, name) {
        showPermanentDeleteConfirmation(name, async () => {
            showLoading('Menghapus Permanen...', 'Sedang menghapus role secara permanen...');

            try {
                const response = await fetch(`api/roles.php?id=${id}&action=permanent-delete`, {
                    method: 'PATCH'
                });

                const result = await response.json();
                hideAlert();

                if (result.success) {
                    showSuccess('Berhasil!', result.message, () => {
                        loadRoleData(true);
                    });
                } else {
                    showError('Gagal!', result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                hideAlert();
                showError('Kesalahan!', 'Terjadi kesalahan saat menghubungi server');
            }
        });
    }

    // SweetAlert helper functions
    function showDeleteConfirmation(itemName, onConfirm) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus "${itemName}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    onConfirm();
                }
            });
        } else {
            if (confirm(`Apakah Anda yakin ingin menghapus "${itemName}"?`)) {
                onConfirm();
            }
        }
    }

    function showRestoreConfirmation(itemName, onConfirm) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi Pulihkan',
                text: `Apakah Anda yakin ingin memulihkan "${itemName}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Pulihkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    onConfirm();
                }
            });
        } else {
            if (confirm(`Apakah Anda yakin ingin memulihkan "${itemName}"?`)) {
                onConfirm();
            }
        }
    }

    function showPermanentDeleteConfirmation(itemName, onConfirm) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Peringatan!',
                html: `<div style="text-align: left;">
                    <p><strong>Anda akan menghapus PERMANEN:</strong></p>
                    <p style="color: #dc3545; font-weight: bold;">"${itemName}"</p>
                    <br>
                    <p style="color: #dc3545;"><i class="ti ti-alert-triangle"></i> <strong>PERHATIAN:</strong></p>
                    <ul style="text-align: left; color: #dc3545;">
                        <li>Data akan hilang SELAMANYA</li>
                        <li>Tidak dapat dikembalikan</li>
                        <li>Semua riwayat akan terhapus</li>
                    </ul>
                    <p style="margin-top: 15px;"><strong>Apakah Anda yakin?</strong></p>
                </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="ti ti-trash-x"></i> Ya, Hapus Permanen!',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    onConfirm();
                }
            });
        } else {
            if (confirm(`PERINGATAN: Anda akan menghapus PERMANEN "${itemName}". Data tidak dapat dikembalikan. Apakah Anda yakin?`)) {
                onConfirm();
            }
        }
    }

    function showLoading(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: text,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
    }

    function showSuccess(title, text, callback) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                if (callback) callback();
            });
        } else {
            alert(`${title}: ${text}`);
            if (callback) callback();
        }
    }

    function showError(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else {
            alert(`${title}: ${text}`);
        }
    }

    function hideAlert() {
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
    }

    // Utility functions
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    }

    // Pagination functions
    function updatePagination() {
        const totalItems = filteredRolesData.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        const startItem = totalItems > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);
        document.getElementById('paginationInfo').textContent =
            `Showing ${startItem} - ${endItem} of ${totalItems} entries`;

        generatePaginationControls(totalPages);
    }

    function generatePaginationControls(totalPages) {
        const paginationContainer = document.getElementById('paginationControls');
        if (!paginationContainer) return;

        paginationContainer.innerHTML = '';

        if (totalPages <= 1) return;

        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1})" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        `;
        paginationContainer.appendChild(prevLi);

        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            const firstLi = document.createElement('li');
            firstLi.className = 'page-item';
            firstLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(1)">1</a>`;
            paginationContainer.appendChild(firstLi);

            if (startPage > 2) {
                const dotsLi = document.createElement('li');
                dotsLi.className = 'page-item disabled';
                dotsLi.innerHTML = `<span class="page-link">...</span>`;
                paginationContainer.appendChild(dotsLi);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
            paginationContainer.appendChild(li);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dotsLi = document.createElement('li');
                dotsLi.className = 'page-item disabled';
                dotsLi.innerHTML = `<span class="page-link">...</span>`;
                paginationContainer.appendChild(dotsLi);
            }

            const lastLi = document.createElement('li');
            lastLi.className = 'page-item';
            lastLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${totalPages})">${totalPages}</a>`;
            paginationContainer.appendChild(lastLi);
        }

        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1})" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        `;
        paginationContainer.appendChild(nextLi);
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredRolesData.length / itemsPerPage);

        if (page < 1 || page > totalPages || page === currentPage) {
            return;
        }

        currentPage = page;
        displayTableView();
        updatePagination();

        const tableContainer = document.querySelector('.table-container');
        if (tableContainer) {
            tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function () {
        showDataRole();
    });
</script>