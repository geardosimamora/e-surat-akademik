#!/bin/bash
# WINDOWS POWERSHELL VERSION - Run this to verify all fixes
# Save as: verify_pdf_fixes.ps1
# Run with: .\verify_pdf_fixes.ps1

Write-Host "=== E-SURAT PDF DOWNLOAD BUG FIX VERIFICATION ===" -ForegroundColor Cyan
Write-Host ""

# Step 1: Clear caches
Write-Host "Step 1: Clearing Laravel caches..." -ForegroundColor Yellow
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear
Write-Host "✅ Caches cleared" -ForegroundColor Green
Write-Host ""

# Step 2: Verify observer registration
Write-Host "Step 2: Checking if LetterObserver is registered..." -ForegroundColor Yellow
$appServiceProvider = cat "app\Providers\AppServiceProvider.php"
if ($appServiceProvider -match "Letter::observe") {
    Write-Host "✅ Observer is registered in AppServiceProvider" -ForegroundColor Green
} else {
    Write-Host "❌ CRITICAL: Observer not registered! Apply fix first." -ForegroundColor Red
}
Write-Host ""

# Step 3: Create storage directories
Write-Host "Step 3: Ensuring storage directories exist..." -ForegroundColor Yellow
$privateLettersDir = "storage/app/private/letters"
$manualLettersDir = "storage/app/manual-letters"

if (-not (Test-Path $privateLettersDir)) {
    New-Item -ItemType Directory -Force -Path $privateLettersDir | Out-Null
    Write-Host "✅ Created $privateLettersDir" -ForegroundColor Green
} else {
    Write-Host "✅ $privateLettersDir exists" -ForegroundColor Green
}

if (-not (Test-Path $manualLettersDir)) {
    New-Item -ItemType Directory -Force -Path $manualLettersDir | Out-Null
    Write-Host "✅ Created $manualLettersDir" -ForegroundColor Green
} else {
    Write-Host "✅ $manualLettersDir exists" -ForegroundColor Green
}
Write-Host ""

# Step 4: Verify .env configuration
Write-Host "Step 4: Checking .env configuration..." -ForegroundColor Yellow
$envContent = cat ".env"
if ($envContent -match "FILESYSTEM_DISK=local") {
    Write-Host "✅ FILESYSTEM_DISK=local (correct)" -ForegroundColor Green
} else {
    Write-Host "⚠️  FILESYSTEM_DISK might not be set to 'local'" -ForegroundColor Yellow
}
Write-Host ""

# Step 5: Check if storage link exists
Write-Host "Step 5: Checking storage symlink..." -ForegroundColor Yellow
if (Test-Path "public/storage") {
    Write-Host "✅ Storage symlink exists (public/storage)" -ForegroundColor Green
} else {
    Write-Host "⚠️  Storage symlink missing. Run: php artisan storage:link" -ForegroundColor Yellow
    php artisan storage:link
}
Write-Host ""

# Step 6: Verify migration columns
Write-Host "Step 6: Checking database schema..." -ForegroundColor Yellow
Write-Host "Run this query in your database:"
Write-Host "  SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='letters';" -ForegroundColor Cyan
Write-Host "Should include: file_path, manual_file_path, catatan_admin" -ForegroundColor Cyan
Write-Host ""

Write-Host "=== VERIFICATION COMPLETE ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "NEXT STEPS:" -ForegroundColor Yellow
Write-Host "1. Test the flow: Student submit -> Admin approve -> Download PDF" -ForegroundColor White
Write-Host "2. Check Laravel logs: storage/logs/laravel.log" -ForegroundColor White
Write-Host "3. Verify files in: storage/app/private/letters/" -ForegroundColor White
Write-Host ""
