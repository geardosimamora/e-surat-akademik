<?php
/**
 * Quick Diagnostics - Run this to get exact data
 * Usage: php diagnostic.php
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Storage;
use App\Models\Letter;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║         E-SURAT PDF DOWNLOAD - BUG DIAGNOSTICS            ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ============================================================
// TEST 1: Check Database
// ============================================================
echo "TEST #1: CHECK DATABASE FILE PATHS\n";
echo "═════════════════════════════════════════════════════════════\n";

$approvedLetters = Letter::where('status', 'approved')
    ->limit(5)
    ->get(['id', 'user_id', 'file_path', 'manual_file_path', 'status']);

if ($approvedLetters->isEmpty()) {
    echo "❌ NO APPROVED LETTERS FOUND IN DATABASE\n";
} else {
    echo "✅ Found " . $approvedLetters->count() . " approved letter(s):\n\n";
    
    foreach ($approvedLetters as $idx => $letter) {
        echo "Letter #" . ($idx + 1) . ":\n";
        echo "  ID:                 " . $letter->id . "\n";
        echo "  User ID:            " . $letter->user_id . "\n";
        echo "  file_path:          " . ($letter->file_path ?? '[NULL]') . "\n";
        echo "  manual_file_path:   " . ($letter->manual_file_path ?? '[NULL]') . "\n";
        echo "\n";
    }
}

// ============================================================
// TEST 2: Check File System
// ============================================================
echo "\nTEST #2: CHECK STORAGE FILE SYSTEM\n";
echo "═════════════════════════════════════════════════════════════\n";

$autoGenPath = storage_path('app/private/letters');
$manualUploadPath = storage_path('app/manual-letters');

echo "Auto-generated PDFs folder: $autoGenPath\n";
echo "  Exists: " . (is_dir($autoGenPath) ? '✅ YES' : '❌ NO') . "\n";
echo "  Readable: " . (is_readable($autoGenPath) ? '✅ YES' : '❌ NO') . "\n";

if (is_dir($autoGenPath)) {
    $files = array_diff(scandir($autoGenPath), ['.', '..']);
    echo "  Files: " . count($files) . "\n";
    if (count($files) > 0) {
        echo "  First 3 files:\n";
        foreach (array_slice($files, 0, 3) as $file) {
            echo "    - $file\n";
        }
    }
}

echo "\nManual uploads folder: $manualUploadPath\n";
echo "  Exists: " . (is_dir($manualUploadPath) ? '✅ YES' : '❌ NO') . "\n";
echo "  Readable: " . (is_readable($manualUploadPath) ? '✅ YES' : '❌ NO') . "\n";

if (is_dir($manualUploadPath)) {
    $files = array_diff(scandir($manualUploadPath), ['.', '..']);
    echo "  Files: " . count($files) . "\n";
}

// ============================================================
// TEST 3: Test File Access via Storage::disk('local')
// ============================================================
echo "\n\nTEST #3: TEST Storage::disk('local')->exists() METHOD\n";
echo "═════════════════════════════════════════════════════════════\n";

if (!$approvedLetters->isEmpty()) {
    $testLetter = $approvedLetters->first();
    
    if ($testLetter->file_path) {
        echo "Testing path: " . $testLetter->file_path . "\n";
        
        $exists = Storage::disk('local')->exists($testLetter->file_path);
        echo "  Storage::disk('local')->exists(): " . ($exists ? '✅ TRUE' : '❌ FALSE') . "\n";
        
        if ($exists) {
            try {
                $content = Storage::disk('local')->get($testLetter->file_path);
                echo "  File size: " . strlen($content) . " bytes\n";
                echo "  First 4 bytes (PDF header): " . bin2hex(substr($content, 0, 4)) . "\n";
                
                if (substr($content, 0, 4) === "%PDF") {
                    echo "  ✅ Valid PDF file (starts with %PDF magic number)\n";
                } else {
                    echo "  ⚠️  File doesn't start with PDF magic number!\n";
                }
            } catch (\Exception $e) {
                echo "  ❌ Error reading file: " . $e->getMessage() . "\n";
            }
        }
    }
}

// ============================================================
// TEST 4: Authorization Check
// ============================================================
echo "\n\nTEST #4: CHECK USER AUTHORIZATION ISSUE\n";
echo "═════════════════════════════════════════════════════════════\n";

if (!$approvedLetters->isEmpty()) {
    $testLetter = $approvedLetters->first();
    $owner = $testLetter->user;
    
    echo "Letter owner:\n";
    echo "  Student ID:   " . $testLetter->user_id . "\n";
    echo "  Student Name: " . ($owner->name ?? '[NOT FOUND]') . "\n";
    echo "  Student Role: " . ($owner->role ?? '[NOT FOUND]') . "\n";
    
    echo "\nAdmins in system:\n";
    $admins = \App\Models\User::where('role', 'admin')->get(['id', 'name', 'role']);
    if ($admins->isEmpty()) {
        echo "  ❌ NO ADMIN USERS FOUND!\n";
    } else {
        foreach ($admins as $admin) {
            echo "  - ID: " . $admin->id . ", Name: " . $admin->name . ", Role: " . $admin->role . "\n";
        }
    }
}

// ============================================================
// SUMMARY
// ============================================================
echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                       DIAGNOSIS SUMMARY                    ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";

$issues = [];

// Check issue 1: Admin can't download
if (!empty($admins) && !$approvedLetters->isEmpty()) {
    $issues[] = "⚠️  Admin authorization issue: Check if admin role is checked in download controller";
}

// Check issue 2: File not found
if (!$approvedLetters->isEmpty()) {
    $testLetter = $approvedLetters->first();
    if ($testLetter->file_path) {
        $exists = Storage::disk('local')->exists($testLetter->file_path);
        if (!$exists) {
            $issues[] = "❌ CRITICAL: File path in database doesn't exist on filesystem!";
            $issues[] = "   Path: " . $testLetter->file_path;
        }
    } else {
        $issues[] = "❌ CRITICAL: file_path column is NULL despite approved status!";
    }
}

if (empty($issues)) {
    echo "\n✅ No obvious issues detected!\n";
    echo "   The PDFs appear to be correctly generated and accessible.\n";
    echo "   Problem might be in the authorization logic in the controller.\n";
} else {
    echo "\n" . count($issues) . " issue(s) detected:\n\n";
    foreach ($issues as $idx => $issue) {
        echo ($idx + 1) . ". " . $issue . "\n";
    }
}

echo "\n";
?>
