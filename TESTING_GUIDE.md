# 🧪 STEP-BY-STEP TESTING GUIDE

## Prerequisites
- [ ] All fixes have been applied (see QUICK_FIX_REFERENCE.md)
- [ ] `php artisan cache:clear` has been run
- [ ] Storage directories exist: `storage/app/private/letters/` and `storage/app/manual-letters/`
- [ ] You have both a Student and Admin account in your system

---

## Test 1: Auto-PDF Generation (Most Critical)

### Setup
1. Open a Terminal and watch the logs:
   ```bash
   cd d:\laragon\www\e-surat-prodi
   tail -f storage/logs/laravel.log
   ```
   (Keep this window open during testing)

### Test Steps
1. **Login as STUDENT**
   - Go to your student portal
   - Click "Ajukan Surat Baru" (Submit New Letter)
   - Select any letter type (e.g., "Aktif Kuliah" / Proof of Enrollment)
   - Fill in any required fields
   - Click Submit
   - **Verify:** Letter appears in "Daftar Surat Saya" with status "Menunggu Diproses"

2. **Login as ADMIN**
   - Go to Filament admin panel (`/admin`)
   - Go to "Transaksi" → "Pengajuan Surat"
   - Find the letter you just created
   - Click "Proses" (Edit)
   - Change Status from "Menunggu Diproses" to "Selesai & Disetujui"
   - In the "Nomor Surat" field, enter: `001/UNIMAL/SI/III/2026`
   - Click "Simpan" (Save)
   
3. **Watch the Logs**
   In your terminal, look for messages like:
   ```
   [2026-03-01 14:23:45] local.INFO: LetterObserver...
   ```
   
   Also check for errors like:
   ```
   PDF gagal disimpan ke storage
   ```

4. **Verify File Was Created**
   - Open File Explorer
   - Navigate to: `D:\laragon\www\e-surat-prodi\storage\app\private\letters\`
   - **Expected:** A file named `surat_<letter-uuid>.pdf` exists
   - **If not:** Check logs for error messages

5. **Verify Database**
   - Open your database client
   - Run: `SELECT id, status, file_path FROM letters WHERE status='approved' ORDER BY updated_at DESC LIMIT 1;`
   - **Expected:** `file_path` column shows: `private/letters/surat_<uuid>.pdf`
   - **If NULL:** Observer didn't save the path

6. **Login as STUDENT and Download**
   - Go back to student portal
   - Refresh the page
   - Find your approved letter
   - Click "Download PDF" button
   - **Expected:** PDF downloads successfully
   - **If error:** File doesn't exist or disk is wrong

### Expected Outcomes ✅
- [ ] Log shows observer was triggered
- [ ] PDF file exists in `storage/app/private/letters/`
- [ ] Database `file_path` column is filled
- [ ] Student can download PDF

### If Test Fails ❌
Go to [Troubleshooting Guide](#troubleshooting-guide) section

---

## Test 2: Manual File Upload

### Setup
- Continue with the same approved letter OR create a new one and approve it

### Test Steps
1. **Login as ADMIN**
   - Go to Filament → Transaksi → Pengajuan Surat
   - Edit the approved letter
   - Scroll down to find "Upload File Manual (Opsional)" section
   - Click the upload area
   - Select a PDF file from your computer (or create a test PDF)
   - Click Upload
   - Click "Simpan" (Save)

2. **Verify File Was Uploaded**
   - Open File Explorer
   - Navigate to: `D:\laragon\www\e-surat-prodi\storage\app\manual-letters\`
   - **Expected:** Your uploaded PDF file appears here
   - Note the filename: `<your-filename>.pdf`

3. **Verify Database**
   - Run: `SELECT id, manual_file_path FROM letters WHERE id='<letter-id>';`
   - **Expected:** `manual_file_path` shows: `manual-letters/<your-filename>.pdf`

4. **Login as STUDENT and Download**
   - Go to student portal
   - Find the letter
   - Click "Download PDF"
   - **Expected:** The manually uploaded file downloads (not the auto-generated one)
   - This should be your PDF, not the system-generated one

### Expected Outcomes ✅
- [ ] File appears in `storage/app/manual-letters/`
- [ ] Database `manual_file_path` is filled
- [ ] Download serves the manual file (takes priority)

### If Test Fails ❌
Go to [Troubleshooting Guide](#troubleshooting-guide) section

---

## Test 3: Multiple Letters & Downloads

### Test Steps
1. **Create 3 Different Letters**
   - Student submits 3 different letter requests (different types if possible)
   
2. **Approve All 3**
   - Admin approves all 3 with different "Nomor Surat" values
   - Each should generate its own PDF
   
3. **Verify All PDFs Generated**
   - Check `storage/app/private/letters/` folder
   - **Expected:** 3 PDF files exist
   - **Commands:**
     ```powershell
     # Count files
     (Get-ChildItem storage\app\private\letters\ | Measure-Object).Count
     # Should show: 3
     ```

4. **Test Download Each One**
   - Student downloads each PDF separately
   - **Expected:** All 3 download without error

### Expected Outcomes ✅
- [ ] 3 PDFs generated
- [ ] All 3 download successfully
- [ ] Each has different content (different letter numbers)

---

## Test 4: Concurrent Operations

### Test Steps
1. **Two Students, Two Admins (If available)**
   - Student A submits letter
   - Student B submits letter
   - Admin A approves Student A's letter
   - Admin B approves Student B's letter (simultaneously)
   - Both should generate PDFs without conflict

2. **Or Rapid Succession**
   - Submit 5 letters
   - Approve all 5 within 10 seconds
   - Check that all PDFs are created without corruption

### Expected Outcomes ✅
- [ ] No file conflicts
- [ ] All PDFs are valid (not corrupted)
- [ ] No duplicate files

---

## Troubleshooting Guide

### Issue: "File not found" when clicking Download

#### Check #1: Does the file exist?
```powershell
# Check auto-generated
dir storage\app\private\letters\

# Check manual uploads
dir storage\app\manual-letters\
```
- If files don't exist → Observer didn't run (see Check #2)
- If files exist but download fails → Disk specification issue (see Check #3)

#### Check #2: Is the observer being triggered?
1. Open: `app/Observers/LetterObserver.php`
2. Add this line at the start of `updated()`:
   ```php
   \Illuminate\Support\Facades\Log::info('LetterObserver triggered for: ' . $letter->id);
   ```
3. Approve a letter
4. Check: `tail -f storage/logs/laravel.log`
5. **Should see:** The log message
6. **If not:** Observer isn't registered

#### Check #3: View the error details
1. In `app/Http/Controllers/Student/LetterController.php`, temporarily modify:
   ```php
   return back()->with('error', 'File: ' . $pathToDownload . ' | Exists: ' . (Storage::disk('local')->exists($pathToDownload) ? 'YES' : 'NO'));
   ```
2. Click download
3. See the actual path and existence status

#### Check #4: Storage disk configuration
Check `.env`:
```
FILESYSTEM_DISK=local
```
Should be `local`, NOT `public`.

#### Check #5: Directory permissions (if Windows permissions are weird)
```powershell
# Give full permissions to storage folder
icacls "storage\app" /grant Users:F /T /C
```

---

### Issue: Observer triggers but says "PDF gagal disimpan"

#### Problem: Directory doesn't exist
```powershell
# Create directories
mkdir storage\app\private\letters
mkdir storage\app\manual-letters
```

#### Problem: Permission denied
```powershell
# Check/fix permissions
icacls "storage\app" /grant Users:F /T /C
```

#### Problem: DomPDF error
Check logs for blade template errors:
```
[2026-03-01 14:23:45] local.ERROR: Blade template not found: ...
```

---

### Issue: File exists but download fails with "403 Forbidden"

#### Likely cause: Authentication check is too strict
Check: `app/Http/Controllers/Student/LetterController.php`

Verify:
```php
if ($letter->user_id !== Auth::id()) {
    abort(403, 'Akses Ditolak.');  // ← Are you the right user?
}
```

Test as the same user who submitted the letter, or as admin.

---

### Issue: Filament download button just refreshes page

This happens when the download method returns `back()` instead of a file response.

Solutions:
1. Check file actually exists (see Check #1)
2. Check observer is registered (see Check #2)
3. Check database has the file path (see Check #3)

---

### Issue: Download works but PDF is blank/corrupted

#### Possible causes:
1. **Blade template has error:** Check logs for template errors
2. **DomPDF installation issue:** `composer require barryvdh/laravel-dompdf`
3. **PDF output method issue:** Verify `$pdf->output()` works

#### Verification:
```php
// In observer, before saving:
$pdfOutput = $pdf->output();
if (strlen($pdfOutput) < 100) {
    throw new \Exception('PDF output too small: ' . strlen($pdfOutput) . ' bytes');
}
```

---

## Success Checklist ✅

After all tests pass:

- [ ] Auto-PDF generation works
- [ ] Manual file upload works  
- [ ] Both files can be downloaded
- [ ] Manual file takes priority over auto-generated
- [ ] Multiple letters don't conflict
- [ ] No "file not found" errors
- [ ] PDFs are valid and non-corrupted
- [ ] All files use `'local'` disk
- [ ] Observer is registered in AppServiceProvider
- [ ] Storage directories exist and are writable

## Performance Benchmarks

These are expected response times:

| Operation | Expected Time | Notes |
|-----------|--------------|-------|
| Submit letter | < 500ms | Quick DB insert |
| Approve letter (PDF gen) | 2-5 seconds | DomPDF rendering takes time |
| Download PDF | < 1 second | File serving |
| Manual upload | 1-2 seconds | File storage + DB update |

If times are much slower, check server logs for bottlenecks.

---

## Logging Tips

To monitor PDF generation in real-time:

```bash
# Watch logs
tail -f storage/logs/laravel.log

# Or filter for just PDF generation
tail -f storage/logs/laravel.log | grep -i "observer\|pdf\|storage"
```

To add more detailed logging, edit `LetterObserver.php`:

```php
public function updated(Letter $letter): void
{
    \Log::info('Letter updated', ['id' => $letter->id, 'status' => $letter->status]);
    
    if($letter->status === 'approved' && empty($letter->file_path)) {
        try {
            \Log::info('Starting PDF generation', ['letter_id' => $letter->id]);
            
            // ... PDF generation code ...
            
            \Log::info('PDF saved successfully', ['path' => $path]);
        } catch (\Exception $e) {
            \Log::error('PDF generation failed', ['error' => $e->getMessage()]);
        }
    }
}
```

---

## Next Steps After Successful Testing

1. **Clean up debug code** - Remove any `.with('error', 'File: ...)` debug strings
2. **Monitor logs** - Watch for any errors during normal usage  
3. **Backup strategy** - Consider backing up generated PDFs periodically
4. **Archive old PDFs** - Consider deleting PDFs older than X months
5. **User communication** - Notify users that PDF download now works

