# ✅ Manual Test Checklist - CRUD Anggota

## Pre-Test Setup

### 1. Login as Admin
```
URL: http://sim-keanggotaan-imm.test/login
Email: demo@example.com
Password: demo123
```

### 2. Navigate to Anggota Management
```
URL: http://sim-keanggotaan-imm.test/admin/anggota
```

---

## Test Cases

### Test 1: CREATE (Tambah Anggota) ✅

**Steps:**
1. [ ] Click tombol "Tambah" di kanan atas
2. [ ] Verify form muncul dengan fields:
   - [ ] Foto Profil (optional)
   - [ ] NIA (required)
   - [ ] Nama Lengkap (required)
   - [ ] Tempat Lahir (required)
   - [ ] Tanggal Lahir (required)
   - [ ] Alamat (required)
   - [ ] No. Telepon (required)
   - [ ] Status (required - dropdown: Aktif/Tidak Aktif)
3. [ ] Fill all required fields with test data:
   ```
   NIA: TEST001
   Nama: Test User
   Tempat Lahir: Jakarta
   Tanggal Lahir: 2000-01-01
   Alamat: Jl. Test No. 123
   No. Telepon: 081234567890
   Status: Aktif
   ```
4. [ ] (Optional) Upload foto profil (JPG/PNG, max 2MB)
5. [ ] Click tombol "Simpan"

**Expected Result:**
- [ ] Redirect ke halaman list anggota
- [ ] Success message muncul: "Anggota berhasil ditambahkan"
- [ ] Anggota baru muncul di list
- [ ] Foto profil tampil (jika diupload) atau initial huruf pertama nama

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 2: READ (List Anggota) ✅

**Steps:**
1. [ ] Verify list anggota tampil dengan card layout
2. [ ] Each card shows:
   - [ ] Foto profil atau initial
   - [ ] Nama lengkap
   - [ ] NIA
   - [ ] Dropdown menu (3 dots)
3. [ ] Test search functionality:
   - [ ] Type nama anggota di search box
   - [ ] Click search button
   - [ ] Verify hasil filter sesuai
4. [ ] Test pagination:
   - [ ] If more than 10 anggota, pagination muncul
   - [ ] Click next/previous page
   - [ ] Verify pagination works

**Expected Result:**
- [ ] List tampil dengan benar
- [ ] Search berfungsi
- [ ] Pagination berfungsi (jika ada >10 data)

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 3: SHOW (Detail Anggota) ✅

**Steps:**
1. [ ] Click icon 3 dots pada salah satu anggota
2. [ ] Click "Detail" dari dropdown menu
3. [ ] Verify halaman detail muncul dengan:
   - [ ] Foto profil (atau initial jika tidak ada foto)
   - [ ] Nama lengkap (header)
   - [ ] NIA
   - [ ] Status badge (Aktif/Tidak Aktif)
   - [ ] Tempat Lahir
   - [ ] Tanggal Lahir (format: dd Month YYYY)
   - [ ] Alamat
   - [ ] No. Telepon
   - [ ] Email (dari user)
   - [ ] Terdaftar Sejak (created_at)
   - [ ] Terakhir Diupdate (updated_at)
4. [ ] Verify tombol "Edit" ada
5. [ ] Verify tombol "Hapus" ada
6. [ ] Click tombol "Kembali"

**Expected Result:**
- [ ] Semua data tampil dengan benar
- [ ] Format tanggal benar (dd Month YYYY)
- [ ] Tombol Edit dan Hapus berfungsi
- [ ] Tombol Kembali redirect ke list

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 4: EDIT (Update Anggota) ✅

**Steps:**
1. [ ] Click icon 3 dots pada salah satu anggota
2. [ ] Click "Edit" dari dropdown menu
3. [ ] Verify form edit muncul dengan data pre-filled:
   - [ ] Foto profil existing tampil (jika ada)
   - [ ] NIA pre-filled
   - [ ] Nama Lengkap pre-filled
   - [ ] Tempat Lahir pre-filled
   - [ ] Tanggal Lahir pre-filled
   - [ ] Alamat pre-filled
   - [ ] No. Telepon pre-filled
   - [ ] Status pre-selected
4. [ ] Modify beberapa fields:
   ```
   Nama: [Ubah nama]
   No. Telepon: [Ubah nomor]
   Status: [Toggle status]
   ```
5. [ ] (Optional) Upload foto baru
6. [ ] Click tombol "Update"

**Expected Result:**
- [ ] Redirect ke halaman list
- [ ] Success message: "Anggota berhasil diupdate"
- [ ] Data berubah sesuai edit
- [ ] Foto baru tampil (jika diupload)
- [ ] Foto lama terhapus dari storage (jika diganti)

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 5: DELETE (Hapus Anggota) ✅

**Steps:**
1. [ ] Click icon 3 dots pada anggota yang akan dihapus
2. [ ] Click "Hapus" dari dropdown menu
3. [ ] Verify modal konfirmasi muncul dengan:
   - [ ] Judul: "Konfirmasi Hapus"
   - [ ] Pesan warning: "Menghapus anggota ini akan menghapus semua riwayat presensi dan sertifikat terkait."
   - [ ] Tombol "Batal"
   - [ ] Tombol "Hapus" (merah)
4. [ ] Click tombol "Hapus" di modal
5. [ ] Wait for processing

**Expected Result:**
- [ ] Modal tertutup
- [ ] Redirect ke halaman list
- [ ] Success message: "Anggota berhasil dihapus"
- [ ] Anggota tidak muncul di list lagi
- [ ] Foto terhapus dari storage (jika ada)

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 6: VALIDATION ✅

**Steps:**
1. [ ] Go to create form
2. [ ] Try submit empty form
3. [ ] Verify validation errors muncul untuk required fields
4. [ ] Try upload file bukan image
5. [ ] Verify validation error: "File harus berupa gambar"
6. [ ] Try upload image > 2MB
7. [ ] Verify validation error: "File maksimal 2MB"
8. [ ] Try input NIA yang sudah ada
9. [ ] Verify validation error: "NIA sudah digunakan"

**Expected Result:**
- [ ] Validation errors tampil dengan jelas
- [ ] Form tidak submit jika ada error
- [ ] Error messages informatif

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 7: PHOTO UPLOAD ✅

**Steps:**
1. [ ] Create anggota dengan foto
2. [ ] Verify foto tampil di list (thumbnail)
3. [ ] Verify foto tampil di detail (full size)
4. [ ] Edit anggota, upload foto baru
5. [ ] Verify foto lama terhapus, foto baru tampil
6. [ ] Delete anggota yang punya foto
7. [ ] Verify foto terhapus dari storage

**Expected Result:**
- [ ] Foto upload berfungsi
- [ ] Foto tampil dengan benar
- [ ] Foto lama terhapus saat update
- [ ] Foto terhapus saat delete anggota

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 8: EDGE CASES ✅

**Steps:**
1. [ ] Test anggota tanpa foto:
   - [ ] Verify initial huruf pertama nama tampil
   - [ ] Verify initial di list dan detail
2. [ ] Test anggota dengan tanggal_lahir null:
   - [ ] Verify tampil "-" bukan error
3. [ ] Test anggota baru (created_at recent):
   - [ ] Verify format tanggal benar
4. [ ] Test search dengan keyword tidak ada:
   - [ ] Verify pesan "Belum ada data anggota"

**Expected Result:**
- [ ] Tidak ada error untuk edge cases
- [ ] Fallback values tampil dengan benar
- [ ] Empty state tampil dengan baik

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

## Summary

### Test Results

| Test Case | Status | Notes |
|-----------|--------|-------|
| CREATE | ⬜ | |
| READ | ⬜ | |
| SHOW | ⬜ | |
| EDIT | ⬜ | |
| DELETE | ⬜ | |
| VALIDATION | ⬜ | |
| PHOTO UPLOAD | ⬜ | |
| EDGE CASES | ⬜ | |

**Legend:**
- ⬜ Not Tested
- ✅ Passed
- ❌ Failed

---

## Issues Found

| Issue | Severity | Description | Status |
|-------|----------|-------------|--------|
| - | - | - | - |

**Severity Levels:**
- 🔴 Critical - Blocks functionality
- 🟡 Major - Significant impact
- 🟢 Minor - Cosmetic or minor issue

---

## Notes

### Browser Tested:
- [ ] Chrome
- [ ] Firefox
- [ ] Edge
- [ ] Safari

### Screen Sizes Tested:
- [ ] Desktop (1920x1080)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)

### Additional Notes:
```
[Add any additional observations here]
```

---

## Sign-Off

**Tested By:** ___________________
**Date:** ___________________
**Overall Status:** ⬜ Passed | ⬜ Failed | ⬜ Needs Review

---

*Checklist Version: 1.0*
*Last Updated: May 11, 2026*
