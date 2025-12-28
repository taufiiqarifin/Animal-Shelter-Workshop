# Cloudinary Setup Guide for Team Members

## Overview

This guide helps **Danish (Windows)**, **Atiqah (Windows)**, and **Shafiqah (Ubuntu)** set up Cloudinary for the Animal Shelter project after pulling the latest changes.

**IMPORTANT:** All team members MUST use the **SAME Cloudinary account credentials** so everyone can access the same uploaded images across the distributed database system.

---

## Step 1: Get Cloudinary Credentials (Team Lead Only)

**One person should create the Cloudinary account and share credentials with everyone.**

### Option A: Use Existing Account (Recommended)
If someone already created a Cloudinary account, **skip to Step 2** and ask them for the credentials.

### Option B: Create New Account
1. Go to https://cloudinary.com/users/register_free
2. Sign up for a free account
3. After login, go to **Dashboard** (https://cloudinary.com/console)
4. You'll see:
   - **Cloud Name**: e.g., `dxxxxxx`
   - **API Key**: e.g., `123456789012345`
   - **API Secret**: Click "Show" to reveal

**SHARE THESE CREDENTIALS WITH ALL TEAM MEMBERS!**

---

## Step 2: Install Dependencies

Everyone should run this after pulling the latest code:

```bash
composer install
```

This installs the `cloudinary-labs/cloudinary-laravel` package (already in composer.json).

---

## Step 3: Configure Environment Variables

### For Everyone (Windows & Ubuntu)

1. **Open your `.env` file** in the project root
2. **Find these lines** (around line 97-108):
   ```env
   FILESYSTEM_DISK=cloudinary

   # Cloudinary Configuration (for distributed image storage)
   # Get these from https://cloudinary.com/console
   # ALL TEAM MEMBERS SHOULD USE THE SAME CLOUDINARY CREDENTIALS
   CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
   CLOUDINARY_CLOUD_NAME=your-cloud-name
   CLOUDINARY_API_KEY=your-api-key
   CLOUDINARY_API_SECRET=your-api-secret
   CLOUDINARY_UPLOAD_PRESET=
   CLOUDINARY_NOTIFICATION_URL=
   ```

3. **Replace the placeholder values** with the actual credentials from Step 1:
   ```env
   FILESYSTEM_DISK=cloudinary

   CLOUDINARY_URL=cloudinary://YOUR_API_KEY:YOUR_API_SECRET@YOUR_CLOUD_NAME
   CLOUDINARY_CLOUD_NAME=dxxxxxx
   CLOUDINARY_API_KEY=123456789012345
   CLOUDINARY_API_SECRET=abcdefghijklmnopqrstuvwxyz
   CLOUDINARY_UPLOAD_PRESET=
   CLOUDINARY_NOTIFICATION_URL=
   ```

4. **Save the .env file**

**IMPORTANT:** All three team members (Danish, Atiqah, Shafiqah) must use the EXACT SAME credentials!

---

## Step 4: Platform-Specific Setup

### For Danish & Atiqah (Windows Users)

**Windows requires SSL certificate configuration to connect to Cloudinary.**

#### Step 4.1: Download SSL Certificate Bundle

Run this command in your project directory:
```bash
php fix-ssl.php
```

This downloads `cacert.pem` to `storage/cacert.pem`.

**If the script fails**, download manually:
- URL: https://curl.se/ca/cacert.pem
- Save to: `C:\web-apps\Animal-Shelter-Workshop\storage\cacert.pem`

#### Step 4.2: Find Your Active php.ini File

Run this command:
```bash
php -i | findstr "php.ini"
```

Look for the line that says:
```
Loaded Configuration File => C:\path\to\php.ini
```

**Write down this path!** (You need to edit this specific file)

#### Step 4.3: Edit php.ini

1. **Open php.ini as Administrator**:
   - Right-click **Notepad** or **VS Code** → "Run as Administrator"
   - Open the php.ini file from Step 4.2

2. **Find these lines** (use Ctrl+F to search):
   ```ini
   ;curl.cainfo =
   ;openssl.cafile=
   ```

3. **Replace them with** (remove the `;` semicolon):
   ```ini
   curl.cainfo = "C:/web-apps/Animal-Shelter-Workshop/storage/cacert.pem"
   openssl.cafile = "C:/web-apps/Animal-Shelter-Workshop/storage/cacert.pem"
   ```

   **IMPORTANT NOTES:**
   - ✅ Use **forward slashes** (`/`) or **double backslashes** (`\\`)
   - ✅ Use **quotes** around the path
   - ✅ Replace `C:/web-apps/Animal-Shelter-Workshop` with YOUR actual project path
   - ❌ Don't use single backslashes (`\`)

4. **Save php.ini**

#### Step 4.4: Restart Development Server

Stop your Laravel server (Ctrl+C) and restart:
```bash
php artisan serve
# OR
composer dev
```

#### Step 4.5: Verify Configuration

Visit this in your browser:
```
http://localhost:8000/check-php-config.php
```

You should see:
```
curl.cainfo: C:/web-apps/Animal-Shelter-Workshop/storage/cacert.pem
✓ Found at: C:/web-apps/Animal-Shelter-Workshop/storage/cacert.pem
```

---

### For Shafiqah (Ubuntu User)

**Ubuntu usually works out-of-the-box, but verify your setup:**

#### Step 4.1: Verify CA Certificates

```bash
# Check if ca-certificates package is installed
dpkg -l | grep ca-certificates

# If not installed, install it:
sudo apt update
sudo apt install ca-certificates
```

#### Step 4.2: Verify PHP cURL Extension

```bash
# Check if cURL extension is enabled
php -m | grep curl

# If not installed:
sudo apt install php-curl
sudo systemctl restart apache2  # or php-fpm if using nginx
```

#### Step 4.3: Test Configuration

```bash
# Check PHP configuration
php -i | grep "curl.cainfo"
```

**If it shows empty or not found**, it's OK on Ubuntu (system certificates are used automatically).

---

## Step 5: Test Cloudinary Upload

### For Everyone (Windows & Ubuntu)

Run the test script to verify everything works:

```bash
php test-cloudinary.php
```

**Expected Output (Success):**
```
Testing Cloudinary upload...
Uploading C:\web-apps\Animal-Shelter-Workshop\storage\app\public\reports\cat1.jpg to Cloudinary...
SUCCESS!
Secure URL: https://res.cloudinary.com/dxxxxxx/image/upload/v1234567890/test/test-upload.jpg
Public ID: test/test-upload
URL from image()->toUrl(): https://res.cloudinary.com/dxxxxxx/image/upload/test/test-upload
```

**If you see "ERROR: Test file not found"**, create a test image:
1. Place any JPG image in `storage/app/public/reports/`
2. Rename it to `cat1.jpg`
3. Run the test script again

**If you see "cURL error 60: SSL certificate problem"** (Windows only):
- Go back to **Step 4 (Windows Setup)** and verify:
  - cacert.pem exists in `storage/cacert.pem`
  - php.ini was edited correctly
  - Server was restarted
  - Correct php.ini file was edited (check `php -i | findstr "php.ini"`)

---

## Step 6: Test in the Application

### Upload Images via Stray Reporting

1. **Start your development server**:
   ```bash
   composer dev
   # OR
   php artisan serve
   ```

2. **Login to the application**:
   - URL: http://localhost:8000
   - Login with your account

3. **Create a Stray Report** with an image:
   - Go to **Stray Reporting** → **Create New Report**
   - Fill in the form
   - **Upload an image** (JPEG/PNG)
   - Submit the report

4. **Verify the image uploaded to Cloudinary**:
   - Go to https://cloudinary.com/console/media_library
   - You should see your uploaded image in the `reports/` folder

5. **Check if the image displays correctly**:
   - View the report you just created
   - The image should load from Cloudinary (URL starts with `res.cloudinary.com`)

---

## Troubleshooting

### Issue 1: "cURL error 60: SSL certificate problem" (Windows)

**Solution:** Follow the **FIX_SSL_CERTIFICATE_ISSUE.md** guide in the project root. Key points:
1. Download `cacert.pem` using `php fix-ssl.php`
2. Edit **the correct php.ini file** (check with `php -i | findstr "php.ini"`)
3. Use forward slashes in the path: `C:/path/to/storage/cacert.pem`
4. Restart the server

---

### Issue 2: "Class 'Cloudinary' not found"

**Solution:**
```bash
# Re-install Composer dependencies
composer install

# Clear Laravel config cache
php artisan config:clear
php artisan cache:clear
```

---

### Issue 3: "Invalid credentials" or "Unauthorized"

**Solution:**
1. **Verify credentials** in your `.env` file match the Cloudinary Dashboard
2. **Check for typos** in `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`
3. **Clear config cache**:
   ```bash
   php artisan config:clear
   ```
4. **Restart the server**

---

### Issue 4: Images not uploading (no error message)

**Solution:**
1. **Check filesystem disk** in `.env`:
   ```env
   FILESYSTEM_DISK=cloudinary
   ```
2. **Clear cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```
3. **Verify Cloudinary config**:
   ```bash
   php artisan tinker
   >>> config('filesystems.default')
   => "cloudinary"
   >>> config('filesystems.disks.cloudinary')
   ```

---

### Issue 5: "Connection timed out" (Ubuntu)

**Solution:**
1. **Check firewall** (might be blocking HTTPS):
   ```bash
   sudo ufw status
   ```
2. **Test direct connection** to Cloudinary:
   ```bash
   curl https://api.cloudinary.com/v1_1/YOUR_CLOUD_NAME/image/upload
   ```
3. **Check DNS resolution**:
   ```bash
   ping api.cloudinary.com
   ```

---

## Verification Checklist

After completing the setup, verify:

- [ ] Ran `composer install` successfully
- [ ] Updated `.env` with Cloudinary credentials (same for all team members)
- [ ] **Windows (Danish & Atiqah):** Configured php.ini with cacert.pem path
- [ ] **Ubuntu (Shafiqah):** Verified ca-certificates package is installed
- [ ] Restarted development server
- [ ] Ran `php test-cloudinary.php` successfully (shows "SUCCESS!")
- [ ] Created a stray report with an image upload
- [ ] Verified image appears in Cloudinary Media Library (https://cloudinary.com/console/media_library)
- [ ] Image displays correctly on the website

---

## Important Notes

1. **Use the SAME credentials** across all team members (Danish, Atiqah, Shafiqah) so everyone can access the same Cloudinary storage.

2. **Don't commit .env** to Git - it contains sensitive API credentials!

3. **Cloudinary Free Tier Limits**:
   - 25 GB storage
   - 25 GB bandwidth/month
   - Should be enough for development/testing

4. **Image URLs** will look like:
   ```
   https://res.cloudinary.com/YOUR_CLOUD_NAME/image/upload/v1234567890/reports/filename.jpg
   ```

5. **All uploaded images** are accessible by anyone with the Cloudinary account credentials.

---

## Need Help?

If you're still stuck:

1. **Check the error logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Run the configuration checker**:
   ```bash
   # Windows:
   http://localhost:8000/check-php-config.php

   # Check PHP info:
   php -i | findstr "curl"  # Windows
   php -i | grep "curl"     # Ubuntu
   ```

3. **Share your error message** with the team, including:
   - Operating system (Windows/Ubuntu)
   - Error message (full stack trace)
   - Output of `php -i | findstr "curl.cainfo"` (Windows) or `php -i | grep "curl"` (Ubuntu)

---

## Quick Reference

### Environment Variables (.env)
```env
FILESYSTEM_DISK=cloudinary
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
CLOUDINARY_CLOUD_NAME=your-cloud-name
CLOUDINARY_API_KEY=your-api-key
CLOUDINARY_API_SECRET=your-api-secret
```

### Test Commands
```bash
# Install dependencies
composer install

# Test Cloudinary upload
php test-cloudinary.php

# Check PHP configuration (Windows)
php -i | findstr "curl.cainfo"

# Check PHP configuration (Ubuntu)
php -i | grep "curl"

# Clear Laravel cache
php artisan config:clear
php artisan cache:clear

# Start development server
composer dev
# OR
php artisan serve
```

### Windows SSL Fix (Quick Version)
```bash
# 1. Download certificate
php fix-ssl.php

# 2. Find php.ini
php -i | findstr "php.ini"

# 3. Edit php.ini (as Administrator) and add:
curl.cainfo = "C:/web-apps/Animal-Shelter-Workshop/storage/cacert.pem"
openssl.cafile = "C:/web-apps/Animal-Shelter-Workshop/storage/cacert.pem"

# 4. Restart server
```

---

**Good luck with the setup! Once everyone completes this, you'll all be able to upload and share images through Cloudinary.**
