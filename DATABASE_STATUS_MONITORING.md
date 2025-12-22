# Database Status Monitoring & Cache Prevention

This document explains how the database connection monitoring system works and how to prevent stale cache issues.

## The Problem

The application caches database connection status for performance. However, this can lead to:
- **Stale cache**: Database comes back online but UI shows "offline"
- **False negatives**: Temporary network issues mark database as offline
- **User confusion**: Status doesn't update until cache expires

## The Solution: Multi-Layer Prevention System

### Layer 1: Automatic Background Monitoring (Primary Solution)

**What it does:**
- Checks all database connections every minute
- Automatically updates cache when status changes
- Logs connection state changes
- Runs in the background without impacting performance

**How to use:**

1. **Development Mode** - Automatically runs with `composer dev`:
```bash
composer dev
```

This starts 5 concurrent processes:
- Server (Laravel)
- Queue Worker
- Logs (Pail)
- Vite (Assets)
- **Scheduler** (Database monitoring) ← NEW!

2. **Production Mode** - Setup cron job:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

3. **Manual Monitoring** (for debugging):
```bash
php artisan db:monitor
```

This runs continuously and shows real-time connection changes.

---

### Layer 2: Improved Connection Retry Logic

**What it does:**
- Each connection check retries up to 3 times (configurable)
- Reduces false negatives from temporary network blips
- Waits 0.5 seconds between retries

**Benefits:**
- More reliable connection detection
- Fewer false "offline" alerts
- Better handling of network instability

**Location:** `app/Services/DatabaseConnectionChecker.php`

```php
public function checkConnection(string $connection, int $maxRetries = 2): bool
```

---

### Layer 3: Smart Cache Duration

**How it works:**
- **All databases online**: Cache for 30 minutes (stable state)
- **Any database offline**: Cache for 60 seconds (frequent checks)
- Automatically adapts based on system health

**Why this matters:**
- During issues: System checks every 60 seconds for recovery
- During stability: Minimal overhead (checks every 30 minutes)
- Self-healing: Detects recovery faster than manual intervention

---

### Layer 4: Manual Refresh Options

**Option 1: URL Parameter (Instant)**
Add to any URL:
```
http://localhost:8000/?refresh_db_status=1
```

**Option 2: Clear Cache Command**
```bash
php artisan db:clear-status-cache
```

**Option 3: Refresh Button in UI**
Click the "Refresh" button in the database status modal (bottom-right corner).

---

## Commands Reference

### Check Current Status
```bash
php artisan db:check-connections
```
Shows current connection status for all databases.

### Clear Cached Status
```bash
php artisan db:clear-status-cache
```
Forces fresh connection check and clears all caches.

### Monitor Connections (Continuous)
```bash
php artisan db:monitor
```
Real-time monitoring with change detection. Press Ctrl+C to stop.

### Refresh Status (Silent)
```bash
php artisan db:refresh-status --silent
```
Used by scheduler. Runs in background without output.

---

## Scheduled Tasks

The system automatically runs:

| Task | Frequency | Description |
|------|-----------|-------------|
| `db:refresh-status --silent` | Every minute | Updates connection cache and logs changes |

**View scheduled tasks:**
```bash
php artisan schedule:list
```

**Test scheduler (development):**
```bash
php artisan schedule:work
```

**Test specific schedule:**
```bash
php artisan schedule:test
```

---

## Cache Architecture

### Two-Tier Caching System

**Tier 1: Laravel Cache (Database/Redis)**
- Primary cache for connection status
- Fast when databases are online
- Falls back to Tier 2 if database is down

**Tier 2: File Cache**
- Backup/fallback cache
- Works even when database is offline
- Located in `storage/framework/cache/data/`

**Tier 3: Session Storage**
- Per-user browser cache
- Cleared with `?refresh_db_status=1` parameter
- Inherits expiry from Tier 1/2

---

## How to Prevent Stale Cache

### ✅ Best Practices

1. **Run the scheduler in development**
   ```bash
   composer dev  # Includes scheduler
   ```

2. **Setup cron job in production**
   ```bash
   * * * * * cd /path && php artisan schedule:run
   ```

3. **Monitor logs for connection changes**
   ```bash
   tail -f storage/logs/laravel.log | grep "Database connection"
   ```

4. **Use manual refresh when needed**
   - Add `?refresh_db_status=1` to URL
   - Or run `php artisan db:clear-status-cache`

### ❌ What NOT to Do

1. **Don't disable caching** - It's essential for performance
2. **Don't set very short cache durations** - Creates overhead
3. **Don't rely only on manual refresh** - Automate with scheduler
4. **Don't ignore connection logs** - They show real issues

---

## Troubleshooting

### Issue: Status shows offline but database is online

**Solution:**
```bash
php artisan db:clear-status-cache
```

**Or add to URL:**
```
?refresh_db_status=1
```

---

### Issue: Scheduler not running in development

**Solution:**
```bash
# Make sure you're using:
composer dev

# NOT just:
php artisan serve
```

---

### Issue: Status changes not being logged

**Check scheduler is running:**
```bash
ps aux | grep "schedule:work"  # Linux/Mac
tasklist | findstr "artisan"   # Windows
```

**View schedule status:**
```bash
php artisan schedule:list
```

---

### Issue: Cache never expires

**Clear all caches:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan db:clear-status-cache
```

---

## Connection Status Flow

```
┌─────────────────────────────────────────────────────────────┐
│  Every Minute (Automatic)                                    │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  1. Scheduler triggers: db:refresh-status --silent           │
│  2. Check all 5 database connections (with retry logic)      │
│  3. Compare with cached status                               │
│  4. Detect changes (offline → online, online → offline)      │
│  5. Update cache if changes detected                         │
│  6. Log significant changes                                  │
│                                                              │
│  Cache Duration:                                             │
│  • All online: 30 minutes                                    │
│  • Any offline: 60 seconds                                   │
│                                                              │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│  On User Request                                             │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  1. Middleware checks session cache                          │
│  2. If expired or ?refresh_db_status=1:                      │
│     • Get fresh status from DatabaseConnectionChecker        │
│     • Update session cache                                   │
│  3. Share status variables with all views                    │
│  4. Display in database-status-indicator.blade.php           │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## Performance Impact

| Configuration | Checks per Hour | Overhead | Recommended For |
|--------------|-----------------|----------|----------------|
| **Scheduler (every minute)** | 60 | Minimal | ✅ Production & Development |
| **30-min cache (all online)** | 2 | Very Low | ✅ Stable production |
| **60-sec cache (any offline)** | 60 | Low | ✅ Recovery mode |
| **No cache** | Unlimited | High | ❌ Never recommended |

---

## Files Modified/Created

### New Files
- `app/Console/Commands/MonitorDatabaseConnections.php` - Real-time monitoring
- `app/Console/Commands/RefreshDatabaseStatus.php` - Scheduled refresh
- `DATABASE_STATUS_MONITORING.md` - This documentation

### Modified Files
- `app/Services/DatabaseConnectionChecker.php` - Added retry logic
- `routes/console.php` - Added scheduled task
- `composer.json` - Added scheduler to dev script

---

## Summary

**Problem:** Database status cache becomes stale when connections recover.

**Solution:** Multi-layer prevention system:
1. ✅ **Automatic monitoring** (every minute via scheduler)
2. ✅ **Retry logic** (reduces false negatives)
3. ✅ **Smart cache** (adapts to system health)
4. ✅ **Manual refresh** (instant fixes when needed)

**Result:** Database status stays accurate without manual intervention.

**Setup:** Just run `composer dev` - monitoring is automatic!

---

## Questions?

- Check Laravel logs: `storage/logs/laravel.log`
- View scheduled tasks: `php artisan schedule:list`
- Test connection: `DB::connection('atiqah')->getPdo()`
- Clear everything: `php artisan db:clear-status-cache`
