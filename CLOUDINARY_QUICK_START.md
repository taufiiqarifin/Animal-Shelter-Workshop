# Cloudinary Quick Start Checklist

## For All Team Members

### Step 1: Get Credentials (From Team Lead)
- [ ] Get `CLOUDINARY_CLOUD_NAME`
- [ ] Get `CLOUDINARY_API_KEY`
- [ ] Get `CLOUDINARY_API_SECRET`

**IMPORTANT:** Everyone uses the SAME credentials!

---

### Step 2: Install Dependencies
```bash
composer install
```

---

### Step 3: Update .env File
Find these lines in your `.env` file (around line 97):
```env
FILESYSTEM_DISK=cloudinary
CLOUDINARY_URL=cloudinary://YOUR_API_KEY:YOUR_API_SECRET@YOUR_CLOUD_NAME
CLOUDINARY_CLOUD_NAME=your-cloud-name
CLOUDINARY_API_KEY=your-api-key
CLOUDINARY_API_SECRET=your-api-secret
```

Replace `your-cloud-name`, `your-api-key`, and `your-api-secret` with the actual values from Step 1.

---

## For Windows Users (Danish & Atiqah)

### Step 4: Fix SSL Certificate

#### 4.1 Download Certificate
```bash
php fix-ssl.php
```

#### 4.2 Find php.ini
```bash
php -i | findstr "php.ini"
```
Write down the path shown in "Loaded Configuration File"

#### 4.3 Edit php.ini (as Administrator)
Find these lines:
```ini
;curl.cainfo =
;openssl.cafile=
```

Replace with (remove `;` and update path):
```ini
curl.cainfo = "C:/web-apps/Animal-Shelter-Workshop/storage/cacert.pem"
openssl.cafile = "C:/web-apps/Animal-Shelter-Workshop/storage/cacert.pem"
```

**IMPORTANT:**
- Use forward slashes `/` or double backslashes `\\`
- Update the path to match your actual project location
- Save the file

#### 4.4 Restart Server
```bash
# Press Ctrl+C to stop, then:
php artisan serve
# OR
composer dev
```

---

## For Ubuntu User (Shafiqah)

### Step 4: Verify Prerequisites
```bash
# Check ca-certificates
dpkg -l | grep ca-certificates

# Check PHP cURL
php -m | grep curl
```

If either is missing:
```bash
sudo apt update
sudo apt install ca-certificates php-curl
```

---

## Step 5: Test Setup (All Users)

### Test 1: Run Test Script
```bash
php test-cloudinary.php
```

**Expected:** Should show "SUCCESS!" with a Cloudinary URL

**If error:** See troubleshooting below

### Test 2: Upload in App
1. Start server: `composer dev`
2. Login to http://localhost:8000
3. Create a Stray Report with an image
4. Verify image displays correctly
5. Check https://cloudinary.com/console/media_library (should see your image)

---

## Troubleshooting

### Windows: "cURL error 60: SSL certificate problem"
```bash
# 1. Verify cacert.pem exists
dir storage\cacert.pem

# 2. Check if php.ini was updated correctly
php -i | findstr "curl.cainfo"
# Should show: curl.cainfo => C:/web-apps/Animal-Shelter-Workshop/storage/cacert.pem

# 3. Make sure you edited the CORRECT php.ini
php -i | findstr "Loaded Configuration File"

# 4. Restart the server (Ctrl+C, then php artisan serve)
```

### "Invalid credentials"
```bash
# 1. Clear Laravel cache
php artisan config:clear
php artisan cache:clear

# 2. Verify .env credentials match Cloudinary dashboard
# 3. Restart server
```

### "Class 'Cloudinary' not found"
```bash
composer install
php artisan config:clear
```

---

## Quick Commands Reference

```bash
# Install dependencies
composer install

# Test Cloudinary
php test-cloudinary.php

# Clear cache
php artisan config:clear && php artisan cache:clear

# Start server
composer dev

# Check PHP config (Windows)
php -i | findstr "curl.cainfo"

# Check PHP config (Ubuntu)
php -i | grep curl

# View Laravel logs
tail -f storage/logs/laravel.log
```

---

## Need More Help?

See the full guide: **CLOUDINARY_TEAM_SETUP_GUIDE.md**

Or check: **FIX_SSL_CERTIFICATE_ISSUE.md** (Windows SSL issues)
