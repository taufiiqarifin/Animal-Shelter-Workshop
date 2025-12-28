<?php

/**
 * Fix SSL Certificate Issues on Windows
 * Run with: php fix-ssl.php
 */

echo "\n=======================================================\n";
echo "  FIX SSL CERTIFICATE ISSUE - Windows PHP/cURL\n";
echo "=======================================================\n\n";

// Step 1: Check current PHP configuration
echo "STEP 1: Checking current PHP configuration...\n";
echo "-------------------------------------------------------\n";
$phpIni = php_ini_loaded_file();
$currentCurlCa = ini_get('curl.cainfo');
$currentOpenSslCa = ini_get('openssl.cafile');

echo "Active php.ini file: " . ($phpIni ?: "NONE (using defaults)") . "\n";
echo "Current curl.cainfo: " . ($currentCurlCa ?: "NOT SET") . "\n";
echo "Current openssl.cafile: " . ($currentOpenSslCa ?: "NOT SET") . "\n\n";

// Step 2: Download CA Bundle
echo "STEP 2: Downloading CA Bundle...\n";
echo "-------------------------------------------------------\n";

$caBundleUrl = 'https://curl.se/ca/cacert.pem';
$caBundlePath = __DIR__ . '/storage/cacert.pem';

echo "Source: $caBundleUrl\n";
echo "Target: $caBundlePath\n";

// Create storage directory if it doesn't exist
if (!is_dir(__DIR__ . '/storage')) {
    mkdir(__DIR__ . '/storage', 0755, true);
}

// Try to download with context to handle SSL issues during download
$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

$content = @file_get_contents($caBundleUrl, false, $context);

if ($content === false) {
    echo "\n❌ ERROR: Failed to download CA bundle automatically.\n\n";
    echo "MANUAL DOWNLOAD REQUIRED:\n";
    echo "1. Open this URL in your browser: $caBundleUrl\n";
    echo "2. Save the file to: $caBundlePath\n";
    echo "3. Then run this script again.\n\n";
    exit(1);
}

file_put_contents($caBundlePath, $content);
$fileSize = filesize($caBundlePath);

echo "✅ Downloaded successfully! (Size: " . number_format($fileSize) . " bytes)\n\n";

// Step 3: Provide instructions
echo "STEP 3: Configure php.ini\n";
echo "=======================================================\n\n";

$normalizedPath = str_replace('\\', '/', $caBundlePath);

echo "📝 ADD THESE LINES TO YOUR php.ini:\n";
echo "-------------------------------------------------------\n";
echo "curl.cainfo = \"$normalizedPath\"\n";
echo "openssl.cafile = \"$normalizedPath\"\n";
echo "-------------------------------------------------------\n\n";

echo "📂 EDIT THIS FILE:\n";
echo "   $phpIni\n\n";

echo "🔧 STEPS:\n";
echo "   1. Open the php.ini file above (as Administrator)\n";
echo "   2. Find the lines starting with ';curl.cainfo' and ';openssl.cafile'\n";
echo "   3. Replace them with the lines shown above (remove the ';')\n";
echo "   4. Save the file\n";
echo "   5. Restart your web server / development server\n\n";

echo "⚠️  IMPORTANT NOTES:\n";
echo "   - Use FORWARD slashes (/) in the path, not backslashes (\\)\n";
echo "   - Include the QUOTES around the path\n";
echo "   - Make sure to remove the semicolon (;) at the start\n\n";

// Step 4: Verification
echo "STEP 4: After editing php.ini and restarting...\n";
echo "=======================================================\n\n";
echo "Run this command to verify:\n";
echo "   php -i | findstr \"curl.cainfo\"\n\n";
echo "Or visit this URL in your browser:\n";
echo "   http://localhost:8000/check-php-config.php\n\n";

echo "✅ CA Bundle is ready at: $caBundlePath\n";
echo "\n=======================================================\n";
echo "  NEXT: Edit php.ini and restart your server!\n";
echo "=======================================================\n\n";
