# Distributed Database Setup Guide

This guide explains how each team member can run the application on their own machine while connecting to distributed databases.

## Overview

The application uses **5 separate databases** hosted on different machines:

| Team Member | Database | Engine | Port | Module |
|------------|----------|--------|------|---------|
| Taufiq | `taufiq` | PostgreSQL | 5434 | User Management |
| Eilya | `eilya` | MySQL | 3307 | Stray Reporting |
| Shafiqah | `shafiqah` | MySQL | 3309 | Animal Management |
| Atiqah | `atiqah` | MySQL | 3308 | Shelter Management |
| Danish | `danish` | SQL Server | 1434 | Booking & Adoption |

---

## Setup for Each Team Member

### 1. Configure Your .env File

Each team member has a custom `.env` template. Copy YOUR template to `.env`:

**For Taufiq:**
```bash
cp .env.taufiq.example .env
```

**For Danish:**
```bash
cp .env.danish.example .env
```

**For Eilya:**
```bash
cp .env.eilya.example .env
```

**For Shafiqah:**
```bash
cp .env.shafiqah.example .env
```

**For Atiqah:**
```bash
cp .env.atiqah.example .env
```

### 2. Update Remote Database IPs

Open your `.env` file and replace the placeholder IP addresses:

```env
# Example for Taufiq's .env
DB1_HOST=192.168.1.100  # Replace with Eilya's actual IP
DB2_HOST=192.168.1.101  # Replace with Atiqah's actual IP
DB3_HOST=192.168.1.102  # Replace with Shafiqah's actual IP
DB4_HOST=192.168.1.103  # Replace with Danish's actual IP
DB5_HOST=127.0.0.1      # Your local database
```

**Important:** Replace `XXX_IP_ADDRESS_HERE` with actual IP addresses or SSH tunnel addresses.

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Install Dependencies

```bash
composer install
npm install
```

---

## Database Setup Options

### Option A: Work on Your Database Only (Recommended for Individual Development)

**Advantages:**
- No need for remote database connections
- Fast development
- No network issues
- Can work offline

**Steps:**

1. Start your local database (PostgreSQL, MySQL, or SQL Server on the correct port)

2. Run migrations for YOUR database only:
   ```bash
   # For Taufiq
   php artisan db:fresh-one taufiq --seed

   # For Danish
   php artisan db:fresh-one danish --seed

   # For Eilya
   php artisan db:fresh-one eilya --seed

   # For Shafiqah
   php artisan db:fresh-one shafiqah --seed

   # For Atiqah
   php artisan db:fresh-one atiqah --seed
   ```

3. Start the development server:
   ```bash
   composer dev
   ```

4. **What works:**
   - Your module features work fully
   - UI will show other modules as "offline" (graceful degradation)
   - No errors or crashes

5. **What doesn't work:**
   - Cross-database features (e.g., viewing animals with bookings)
   - Features that depend on other modules

---

### Option B: Full Distributed Setup (For Integration Testing)

**Advantages:**
- Full application functionality
- Test cross-database features
- Production-like environment

**Prerequisites:**
- All team members' databases must be accessible via:
  - SSH tunnels, OR
  - Same network (LAN/VPN), OR
  - Port forwarding

**Steps:**

1. **Setup SSH Tunnels** (if databases are remote):
   ```bash
   # Example: Taufiq connecting to Eilya's database
   ssh -L 3307:localhost:3307 eilya@192.168.1.100

   # Repeat for all remote databases
   ```

2. **Verify all connections:**
   ```bash
   php artisan db:check-connections
   ```

3. **Run migrations on all databases:**
   ```bash
   php artisan db:fresh-all --seed
   ```

   **Note:** This requires ALL databases to be online.

4. **Start the development server:**
   ```bash
   composer dev
   ```

---

## New Commands for Individual Database Management

### Migrate a Single Database
```bash
php artisan db:migrate-one {connection}

# Examples:
php artisan db:migrate-one taufiq
php artisan db:migrate-one danish
php artisan db:migrate-one shafiqah
```

### Refresh a Single Database
```bash
php artisan db:fresh-one {connection} --seed

# Examples:
php artisan db:fresh-one eilya --seed
php artisan db:fresh-one atiqah --seed
```

### Check All Database Connections
```bash
php artisan db:check-connections
```

### Clear Database Status Cache
```bash
php artisan db:clear-status-cache
```

---

## Network Configuration Examples

### Same LAN (Local Network)

**Requirements:**
- All team members on same WiFi/network
- Firewall allows database ports
- Know each other's local IPs

**Configuration:**
```env
DB1_HOST=192.168.1.100  # Eilya's local IP
DB2_HOST=192.168.1.101  # Atiqah's local IP
```

### SSH Tunnels (Remote/Internet)

**Requirements:**
- SSH access to remote machines
- Port forwarding configured

**Setup:**
```bash
# On your machine, create tunnels to all remote databases
ssh -L 3307:localhost:3307 user@eilya-machine.com
ssh -L 3308:localhost:3308 user@atiqah-machine.com
ssh -L 3309:localhost:3309 user@shafiqah-machine.com
ssh -L 1434:localhost:1434 user@danish-machine.com
ssh -L 5434:localhost:5434 user@taufiq-machine.com
```

**Configuration:**
```env
# All remote databases now accessible via localhost through tunnels
DB1_HOST=127.0.0.1
DB2_HOST=127.0.0.1
DB3_HOST=127.0.0.1
DB4_HOST=127.0.0.1
DB5_HOST=127.0.0.1
```

### VPN (Hamachi, ZeroTier, etc.)

**Requirements:**
- All team members on same VPN
- VPN software installed
- VPN IP addresses assigned

**Configuration:**
```env
DB1_HOST=25.100.100.1  # Eilya's VPN IP
DB2_HOST=25.100.100.2  # Atiqah's VPN IP
```

---

## Graceful Offline Handling

The application is designed to work gracefully when some databases are offline:

### What Happens When a Database is Offline?

1. **Database Status Indicator:** Shows which databases are offline (bottom-right corner)
2. **Controllers:** Use `safeQuery()` to skip offline databases
3. **Views:** Display partial data without errors
4. **No Crashes:** Application continues to function

### Testing Offline Behavior

1. Stop a remote database connection
2. Refresh the page
3. Observe:
   - Status indicator shows database as offline
   - Related features show "No data" gracefully
   - No error messages or crashes

---

## Troubleshooting

### Issue: "Connection refused" on remote database

**Solutions:**
1. Check if remote machine's database is running
2. Verify firewall allows the database port
3. Confirm IP address is correct
4. Test connection: `telnet REMOTE_IP PORT`

### Issue: `db:fresh-all` fails

**Solution:** Use individual database commands instead:
```bash
php artisan db:fresh-one taufiq --seed
```

### Issue: Seeding fails with foreign key errors

**Cause:** Cross-database dependencies (e.g., Animal references Rescue from different database)

**Solutions:**
1. Make sure ALL databases are online before seeding
2. OR seed only your database (expect warnings about missing cross-database data)

### Issue: Database shows "offline" but connection works

**Solution:** Clear stale cache:
```bash
php artisan db:clear-status-cache
# OR add to URL:
http://localhost:8000/?refresh_db_status=1
```

---

## Best Practices

### For Individual Development
1. ✅ Work on your own database only
2. ✅ Use `db:fresh-one {your-connection}` command
3. ✅ Test your module features in isolation
4. ✅ Commit database migrations for your tables only

### For Integration Testing
1. ✅ Coordinate with team to ensure databases are online
2. ✅ Use SSH tunnels or VPN for security
3. ✅ Run `db:fresh-all` only when all databases are accessible
4. ✅ Test cross-database features (e.g., Animal + Booking)

### For Production/Deployment
1. ✅ All databases should be on same secure network
2. ✅ Use production `.env` with actual server IPs
3. ✅ Enable firewall rules for database ports
4. ✅ Setup database backups (already configured)

---

## Summary of Changes

### New Commands
- `php artisan db:migrate-one {connection}` - Migrate single database
- `php artisan db:fresh-one {connection}` - Refresh single database

### Modified Commands
- `php artisan db:fresh-all` - Now skips offline databases instead of failing

### New Files
- `.env.taufiq.example` - Taufiq's environment template
- `.env.danish.example` - Danish's environment template
- `.env.eilya.example` - Eilya's environment template
- `.env.shafiqah.example` - Shafiqah's environment template
- `.env.atiqah.example` - Atiqah's environment template

### Improved Error Handling
- `FreshAllDatabases` command gracefully handles offline databases
- Controllers already use `safeQuery()` for resilient database access
- Views display partial data when databases are offline

---

## Quick Start

**For Individual Work:**
```bash
# 1. Copy your .env template
cp .env.{your-name}.example .env

# 2. Start your database
# (PostgreSQL, MySQL, or SQL Server)

# 3. Generate key
php artisan key:generate

# 4. Install dependencies
composer install && npm install

# 5. Migrate YOUR database only
php artisan db:fresh-one {your-connection} --seed

# 6. Start development server
composer dev
```

**For Full Integration:**
```bash
# 1. Setup all SSH tunnels or ensure all databases are accessible
# 2. Verify all connections
php artisan db:check-connections

# 3. Migrate all databases
php artisan db:fresh-all --seed

# 4. Start development server
composer dev
```

---

## Support

If you encounter issues:
1. Check `storage/logs/laravel.log` for error details
2. Verify database connections: `php artisan db:check-connections`
3. Clear cache: `php artisan db:clear-status-cache`
4. Test individual database: `php artisan db:fresh-one {connection}`
