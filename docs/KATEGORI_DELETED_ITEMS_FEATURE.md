# 📋 Fitur Show Deleted Items - Halaman Kategori

## 🎯 Deskripsi
Fitur untuk menampilkan kategori yang telah di-soft delete (is_active = FALSE) di halaman kategori. Pengguna dapat melihat, memulihkan (restore), atau menghapus permanen (permanent delete) kategori yang telah dihapus.

---

## 🔧 Cara Menggunakan

### 1. **Menampilkan Item yang Terhapus**

**Langkah:**
1. Buka halaman **Kategori** (`/dist/dashboard/pages/kategori.php`)
2. Klik tombol **"Filters"** di bagian atas tabel
3. Centang checkbox **"🗑️ Show Deleted Items"**
4. Tabel akan otomatis refresh dan menampilkan kategori yang terhapus

**Visual Feedback:**
- Badge filter "Show Deleted Items" akan muncul di bawah tombol Filters
- Badge counter pada tombol Filters akan bertambah
- Status badge di kolom Status akan berubah menjadi **"Inactive"** (merah)

---

### 2. **Memulihkan Kategori yang Terhapus (Restore)**

**Langkah:**
1. Pastikan checkbox "Show Deleted Items" sudah dicentang
2. Cari kategori yang ingin dipulihkan di tabel
3. Klik tombol **"🔄 Restore"** (hijau) di kolom Actions
4. Konfirmasi dengan klik **"Ya, Pulihkan!"** di dialog SweetAlert
5. Kategori akan kembali ke status Active

**Hasil:**
- Kategori kembali muncul di list Active (is_active = TRUE)
- Badge status berubah dari "Inactive" ke "Active"
- Tombol action berubah kembali ke Edit dan Soft Delete

---

### 3. **Menghapus Permanen (Permanent Delete)**

**Langkah:**
1. Pastikan checkbox "Show Deleted Items" sudah dicentang
2. Cari kategori yang ingin dihapus permanen
3. Klik tombol **"🗑️ Permanent Delete"** (merah) di kolom Actions
4. Baca peringatan dengan seksama di dialog SweetAlert
5. Ketik **"HAPUS"** untuk konfirmasi
6. Kategori akan dihapus permanen dari database

**Peringatan:**
- ⚠️ Aksi ini **TIDAK DAPAT DIBATALKAN**
- ⚠️ Data akan hilang permanen dari database
- ⚠️ Pastikan data backup tersedia sebelum melakukan permanent delete

---

## 🔍 Implementasi Teknis

### A. Filter Checkbox HTML
```html
<label class="filter-option">
    <input type="checkbox" class="filter-checkbox" 
           data-filter="view_mode" 
           data-value="deleted" 
           onchange="handleViewModeChange(this)">
    <span class="filter-icon">🗑️</span>
    <span class="filter-label">Show Deleted Items</span>
</label>
```

### B. JavaScript Functions

#### 1. handleViewModeChange()
```javascript
function handleViewModeChange(checkbox) {
    const isShowingDeleted = checkbox.checked;

    if (isShowingDeleted) {
        currentViewMode = 'deleted';
        loadCategoryData(true); // Load deleted items

        activeFilters.set('view_mode:deleted', {
            type: 'view_mode',
            value: 'deleted',
            label: 'Show Deleted Items',
            element: checkbox
        });
    } else {
        currentViewMode = 'active';
        loadCategoryData(false); // Load active items
        activeFilters.delete('view_mode:deleted');
    }

    updateFilterBadge();
    updateActiveFiltersDisplay();
}
```

#### 2. loadCategoryData()
```javascript
async function loadCategoryData(showDeleted = false) {
    // Fetch all categories from API
    const response = await fetch('api/menu/categories.php?per_page=1000');
    const result = await response.json();

    if (result.success) {
        allCategoriesData = result.data || [];

        // Filter based on view mode
        if (showDeleted) {
            filteredCategoriesData = allCategoriesData.filter(cat => !cat.is_active);
        } else {
            filteredCategoriesData = allCategoriesData.filter(cat => cat.is_active);
        }

        currentViewMode = showDeleted ? 'deleted' : 'active';
        applyFilters();
    }
}
```

#### 3. applyFilters()
```javascript
function applyFilters() {
    filteredCategoriesData = [...allCategoriesData];

    // Filter by view mode first
    if (currentViewMode === 'deleted') {
        filteredCategoriesData = filteredCategoriesData.filter(cat => !cat.is_active);
    } else {
        filteredCategoriesData = filteredCategoriesData.filter(cat => cat.is_active);
    }

    // Apply other filters (type, search, etc.)
    // ...
}
```

#### 4. displayTableView()
```javascript
function displayTableView() {
    paginatedData.forEach((item) => {
        // Show different action buttons based on view mode
        ${currentViewMode === 'active' ? `
            <!-- Edit and Soft Delete buttons -->
            <button onclick="editCategory('${item.id}')">Edit</button>
            <button onclick="deleteCategory('${item.id}')">Delete</button>
        ` : `
            <!-- Restore and Permanent Delete buttons -->
            <button onclick="restoreCategory('${item.id}')">Restore</button>
            <button onclick="permanentDeleteCategory('${item.id}')">Permanent Delete</button>
        `}
    });
}
```

---

## 📊 State Management

### Global Variables
```javascript
let currentViewMode = 'active'; // 'active' or 'deleted'
let allCategoriesData = [];     // All categories from API
let filteredCategoriesData = []; // Filtered categories
let activeFilters = new Map();   // Active filter tracking
```

### View Mode States
| State | Description | Filter Applied |
|-------|-------------|----------------|
| `active` | Default view | `is_active = TRUE` |
| `deleted` | Deleted items view | `is_active = FALSE` |

---

## 🎨 UI Elements

### 1. Filter Badge
- **Location:** Below "Filters" button
- **Display:** Shows "Show Deleted Items" with X button
- **Color:** Badge dengan background subtle

### 2. Status Badge
| Status | Badge Color | Text |
|--------|-------------|------|
| Active | Green | Active |
| Deleted | Red | Inactive |

### 3. Action Buttons

**Active View:**
| Button | Icon | Color | Action |
|--------|------|-------|--------|
| Edit | ✏️ ti-edit | Warning (Yellow) | Edit kategori |
| Delete | 🗑️ ti-trash | Danger (Red) | Soft delete |

**Deleted View:**
| Button | Icon | Color | Action |
|--------|------|-------|--------|
| Restore | 🔄 ti-refresh | Success (Green) | Restore kategori |
| Permanent Delete | ❌ ti-trash-x | Danger (Red) | Hapus permanen |

---

## 🔐 API Endpoints

### 1. Soft Delete (DELETE)
```http
DELETE /api/menu/categories.php?id={category_id}
```

**Response:**
```json
{
    "success": true,
    "message": "Category deleted successfully"
}
```

**Database:** Set `is_active = FALSE`

---

### 2. Restore (PATCH)
```http
PATCH /api/menu/categories.php?id={category_id}&action=restore
```

**Response:**
```json
{
    "success": true,
    "message": "Category restored successfully"
}
```

**Database:** Set `is_active = TRUE`

---

### 3. Permanent Delete (PATCH)
```http
PATCH /api/menu/categories.php?id={category_id}&action=permanent-delete
```

**Response:**
```json
{
    "success": true,
    "message": "Category permanently deleted"
}
```

**Database:** `DELETE FROM categories WHERE id = ?`

---

## ✅ Testing Checklist

### Scenario 1: Show Deleted Items
- [ ] Klik tombol "Filters"
- [ ] Centang "Show Deleted Items"
- [ ] Verifikasi badge filter muncul
- [ ] Verifikasi counter filter bertambah
- [ ] Verifikasi tabel menampilkan item deleted
- [ ] Verifikasi status badge = "Inactive" (merah)
- [ ] Verifikasi tombol action = Restore + Permanent Delete

### Scenario 2: Restore Category
- [ ] Pilih kategori deleted
- [ ] Klik tombol "Restore"
- [ ] Konfirmasi di SweetAlert
- [ ] Verifikasi success notification
- [ ] Uncheck "Show Deleted Items"
- [ ] Verifikasi kategori kembali di list active

### Scenario 3: Permanent Delete
- [ ] Pilih kategori deleted
- [ ] Klik tombol "Permanent Delete"
- [ ] Baca peringatan
- [ ] Ketik "HAPUS" untuk konfirmasi
- [ ] Verifikasi success notification
- [ ] Verifikasi kategori hilang dari list deleted
- [ ] Verifikasi kategori hilang dari database

### Scenario 4: Filter Combination
- [ ] Centang "Show Deleted Items"
- [ ] Pilih filter Type (Product/Topping)
- [ ] Verifikasi filtering berjalan kumulatif
- [ ] Cari dengan search input
- [ ] Verifikasi search bekerja pada deleted items
- [ ] Ubah sorting
- [ ] Verifikasi sorting bekerja pada deleted items

---

## 🚨 Error Handling

### 1. API Error
```javascript
try {
    const response = await fetch(url);
    const result = await response.json();
    
    if (!result.success) {
        showError('Gagal!', result.message);
    }
} catch (error) {
    console.error('Error:', error);
    showError('Kesalahan!', 'Terjadi kesalahan saat menghubungi server');
}
```

### 2. Empty State
```javascript
if (filteredCategoriesData.length === 0) {
    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center">
                <p class="mb-0">Tidak ada data yang sesuai dengan filter</p>
            </td>
        </tr>
    `;
}
```

---

## 📌 Notes

1. **Soft Delete First:** Kategori harus di-soft delete dulu sebelum bisa di-permanent delete
2. **Backup Data:** Selalu backup database sebelum permanent delete
3. **Check Dependencies:** API akan check apakah kategori memiliki produk aktif sebelum delete
4. **UI Consistency:** Implementasi sama seperti halaman Menu untuk consistency
5. **Filter Persistence:** Filter state tidak persisten setelah page reload

---

## 🔗 Related Files

- **Frontend:** `dist/dashboard/pages/kategori.php`
- **API:** `api/menu/categories.php`
- **Documentation:** Lihat halaman Menu untuk referensi implementasi yang sama

---

## 📝 Changelog

**Version 1.0 - October 21, 2025**
- ✅ Implement "Show Deleted Items" filter checkbox
- ✅ Add handleViewModeChange() function
- ✅ Add Restore and Permanent Delete buttons
- ✅ Add filter badge display
- ✅ Add proper error handling
- ✅ Sync implementation with Menu page

---

## 🤝 Support

Jika ada masalah dengan fitur ini:
1. Check browser console untuk error messages
2. Verify API response di Network tab
3. Check database untuk state kategori (is_active field)
4. Pastikan API endpoint tersedia dan berfungsi

---

**Last Updated:** October 21, 2025  
**Author:** Development Team  
**Status:** ✅ Implemented & Working
