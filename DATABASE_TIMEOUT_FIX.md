# Database Connection Timeout Fix

## ⚠️ CRITICAL REQUIREMENT

**TAUFIQ DATABASE (PostgreSQL) MUST BE ONLINE**

The taufiq database is **required** for the application to function because it:
- Stores the `users` table (authentication/login)
- Provides the primary cache storage
- Handles session management

**Other databases** (eilya, shafiqah, atiqah, danish) can be offline - their modules will be unavailable but the application remains functional.

---

## Problem Summary

When one or more remote databases (especially SQL Server/danish) are offline, the application would hang for 60+ seconds on every page load, making it completely unusable. This happened because:

1. **Long connection timeouts**: SQL Server had 5-second timeout, with retry logic attempting 3 times = 15 seconds per check
2. **Multiple retries**: The connection check had retry logic that multiplied timeout duration
3. **No circuit breaker**: Failed connections were repeatedly attempted on every request
4. **Wrong cache priority**: Application tried database cache first (which fails when databases are offline)

## Solutions Implemented

### 1. Reduced Connection Timeouts (config/database.php)

**SQL Server (Danish)**
```php
'danish' => [
    'options' => [
        'ConnectTimeout' => 2, // Reduced from 5 to 2 seconds
        'LoginTimeout' => 2,   // Reduced from 5 to 2 seconds
    ],
],
```

**MySQL Connections (Eilya, Atiqah, Shafiqah)**
```php
'options' => [
    PDO::ATTR_TIMEOUT => 0.5, // Already set to 0.5 seconds
],
```

**PostgreSQL (Taufiq)**
```php
'options' => [
    PDO::ATTR_TIMEOUT => 0.5,
],
'connect_timeout' => 0.5, // PostgreSQL-specific timeout
```

### 2. Removed Retry Logic (DatabaseConnectionChecker.php)

**Before:**
- Attempted connection 3 times (maxRetries = 2)
- SQL Server: 5 seconds × 3 attempts = 15 seconds total
- Had sleep delays between retries

**After:**
- Single attempt only - fail fast
- SQL Server: 2 seconds max (or less if fails faster)
- No retry delays

### 3. Implemented Circuit Breaker Pattern

**How it works:**
```php
// If a connection fails, it's marked as "broken" for 30 seconds
// During this time, we don't attempt to connect (instant failure)
// After 30 seconds, we try again (maybe it came back online)
```

**Benefits:**
- Prevents repeated connection attempts to offline databases
- First request after failure takes ~2 seconds
- Subsequent requests for next 30 seconds: **instant** (0ms)
- Auto-recovers when database comes back online

**Cache key:** `db_circuit_breaker_{connection}` (stored in file cache)

### 4. Taufiq Database Cache Priority

**Cache Strategy:**
- **Primary Cache**: Taufiq database (PostgreSQL) - Fast, always available since auth requires it
- **Fallback Cache**: File cache - Used when taufiq temporarily unreachable

**Why taufiq database for cache:**
- Taufiq database **must be online** for authentication (stores users table)
- If taufiq is offline, users cannot login anyway
- Database cache is faster than file cache
- File cache only used as backup for temporary taufiq issues

**Configuration (config/cache.php):**
```php
'default' => env('CACHE_STORE', 'database'),
'database' => [
    'connection' => 'taufiq',  // Uses taufiq PostgreSQL database
    'table' => 'cache',
]
```

### 5. Request Mutex/Lock

**Problem:** Multiple simultaneous requests all trying to check connections

**Solution:** Mutex lock prevents multiple connection checks at once
```php
// First request: Checks connections, sets lock
// Other requests: See lock, wait briefly for cache, use cached result
```

**Lock key:** `db_connection_check_in_progress` (expires after 10 seconds)

### 6. Real-Time Status Monitoring

**Problem:** Team members need to know immediately when a database goes offline or comes back online

**Solution:** Auto-refresh system with toast notifications

**Features:**
- **Automatic polling**: Checks database status every 20 seconds via API
- **Toast notifications**: Alerts when database status changes
  - 🟢 Green notification: "Database is back online!"
  - 🟡 Yellow notification: "Database went offline!"
- **Instant UI update**: Page reloads automatically when status changes
- **Console logging**: Developer-friendly status logging in browser console

**How it works:**
```javascript
// Every 20 seconds
1. Fetch /api/database-status
2. Compare with last known status
3. If changed:
   - Show toast notification
   - Update UI after 1.5 seconds
   - Reload page for fresh state
```

**Benefits for team:**
- **Immediate awareness** when teammate's database goes down
- **No manual refreshing** needed
- **Smooth collaboration** - everyone sees real-time status
- **Quick recovery detection** - know when database comes back (15-20 seconds max)

## Performance Impact

### Before Fix
| Scenario | First Request | Subsequent Requests | Status Change Detection |
|----------|--------------|-------------------|----------------------|
| All databases online | ~1s | Fast (cached) | Manual refresh only |
| 1 database offline | 15-60s | 15-60s (cache expired) | Manual refresh only |
| All databases offline | 75-300s | 75-300s (cache expired) | Manual refresh only |

### After Fix (With Real-Time Monitoring)
| Scenario | First Request | Subsequent Requests | Status Change Detection |
|----------|--------------|-------------------|----------------------|
| All databases online | ~1s | Instant (cached 5 min) | Auto-check every 20s |
| 1 database offline | ~2s | Instant (cached 15s) | Auto-notify within 15-20s |
| All databases offline | ~5s | Instant (cached 15s) | Auto-notify within 15-20s |

**Key improvements:**
- **First failure**: 2-5 seconds instead of 60+ seconds
- **Subsequent requests**: **Instant** for 30 seconds (circuit breaker active)
- **Cache duration (stable)**: 5 minutes when all online (periodic checks)
- **Cache duration (unstable)**: 15 seconds when offline (rapid recovery detection)
- **Real-time alerts**: Toast notifications within 15-20 seconds of status change
- **Team awareness**: Everyone sees status changes automatically

## How Circuit Breaker Works

```
Request 1 (T=0s):
  ├─ Check cache: MISS
  ├─ Try SQL Server connection: FAIL after 2s
  ├─ Set circuit breaker: ACTIVE for 30s
  └─ Cache result: danish = offline (60s)

Request 2 (T=5s):
  ├─ Check cache: HIT (danish = offline)
  └─ Return cached result: 0ms

Request 3 (T=10s):
  ├─ Check cache: HIT (danish = offline)
  └─ Return cached result: 0ms

Request N (T=35s):
  ├─ Check cache: MISS (expired after 60s)
  ├─ Circuit breaker: EXPIRED (30s passed)
  ├─ Try SQL Server connection: FAIL after 2s
  ├─ Set circuit breaker: ACTIVE for 30s
  └─ Cache result: danish = offline (60s)
```

## Testing Instructions

### Test 1: Offline Database Handling
1. Ensure one or more remote databases are offline
2. Restart the development server: `Ctrl+C` then `composer dev`
3. Access http://127.0.0.1:8000 in browser
4. **Expected:** Page loads within 2-5 seconds (not 60+ seconds)
5. Refresh the page
6. **Expected:** Page loads instantly (circuit breaker + cache active)

### Test 2: Database Recovery
1. With databases offline, access the application
2. Wait 60 seconds for cache to expire
3. Bring databases back online
4. Access the application again
5. **Expected:** Application detects databases are online and updates status

### Test 3: Cache Clear
1. Run: `php artisan db:clear-status-cache`
2. **Expected:** Command completes quickly (< 5 seconds)
3. Access application
4. **Expected:** Page loads quickly with updated status

### Test 4: Real-Time Status Monitoring
1. With application open in browser, open browser console (F12)
2. **Expected:** See log message: `[DB Monitor] Auto-refresh started (every 20 seconds)`
3. Simulate database failure:
   - Stop one of your teammate's database services
   - OR disconnect SSH tunnel to a remote database
4. Wait 15-20 seconds
5. **Expected:**
   - Toast notification appears: "⚠️ [Database] went offline!"
   - Page reloads automatically
   - Status indicator updates to show database offline
6. Restore database connection
7. Wait 15-20 seconds
8. **Expected:**
   - Toast notification appears: "✅ [Database] is back online!"
   - Page reloads automatically
   - Status indicator updates to show database online

**Console Monitoring:**
```
[DB Monitor] Auto-refresh started (every 20 seconds)
[DB Monitor] 1 database(s) currently offline
[DB Monitor] Status changed, reloading page...
```

## Cache Architecture

### IMPORTANT: Taufiq Database Requirement

**Taufiq database (PostgreSQL) MUST be online for the project to work:**
- ✅ Stores `users` table - required for authentication/login
- ✅ Stores cache data - primary cache location
- ✅ Stores cache locks - concurrency control

**If taufiq is offline:**
- ❌ Users cannot login (authentication fails)
- ❌ Application cannot function
- ⚠️ File cache provides limited fallback for connection status only

**All other databases can be offline:**
- Eilya (MySQL) - Stray Reporting module unavailable
- Shafiqah (MySQL) - Animal Management module unavailable
- Atiqah (MySQL) - Shelter Management module unavailable
- Danish (SQL Server) - Booking & Adoption module unavailable

Application will show warning modal but remain functional for available modules.

### Cache Locations (Priority Order)
1. **Taufiq Database Cache** (cache table in PostgreSQL)
   - Primary cache location
   - Fast access via database queries
   - Always available (taufiq must be online for auth)
   - Stores connection status, session data

2. **File Cache** (storage/framework/cache/data/)
   - Fallback when taufiq temporarily unreachable
   - Used for circuit breaker state (more reliable for mutex/locks)
   - Survives database restarts

### Cache Keys
| Key | Purpose | Duration | Storage |
|-----|---------|----------|---------|
| `db_connection_status` | All database statuses | 60s (offline) / 30m (online) | Taufiq DB (primary) + File (backup) |
| `db_connection_status_{connection}` | Single connection status | 60s | Taufiq DB (primary) + File (backup) |
| `db_circuit_breaker_{connection}` | Circuit breaker state | 30s | File only (more reliable for locks) |
| `db_connection_check_in_progress` | Request mutex/lock | 10s | File only (more reliable for locks) |

## Manual Cache Management

### Clear All Caches
```bash
php artisan db:clear-status-cache
```

### Force Immediate Refresh (via URL)
```
http://127.0.0.1:8000/?refresh_db_status=1
```

### View Connection Status
```bash
php artisan db:check-connections
```

### Monitor Connections (Real-time)
```bash
php artisan db:monitor
```

## For All Team Members

**IMPORTANT:** All team members should pull these changes to prevent timeout issues on their machines.

```bash
git pull origin feature/distributed-architecture
composer install
php artisan db:clear-status-cache
```

**What this fixes for you:**
- ✅ Application loads quickly even when remote databases are offline
- ✅ No more 60+ second page load timeouts
- ✅ Can work on your module even when other databases are down (except taufiq)
- ✅ Automatic recovery detection when databases come back online

**CRITICAL for all team members:**
- 🔴 **Taufiq database MUST be online** - Without it, nobody can login
- 🟢 **Your own database should be online** - To work on your module
- 🟡 **Other databases can be offline** - Their modules won't work but app stays responsive

**Who needs what database online:**
| Team Member | Required Databases | Optional Databases |
|-------------|-------------------|-------------------|
| **Taufiq** | Taufiq (PostgreSQL) | Eilya, Shafiqah, Atiqah, Danish |
| **Eilya** | Taufiq + Eilya (MySQL) | Shafiqah, Atiqah, Danish |
| **Shafiqah** | Taufiq + Shafiqah (MySQL) | Eilya, Atiqah, Danish |
| **Atiqah** | Taufiq + Atiqah (MySQL) | Eilya, Shafiqah, Danish |
| **Danish** | Taufiq + Danish (SQL Server) | Eilya, Shafiqah, Atiqah |

## Files Modified

1. **config/database.php** - Reduced SQL Server timeout from 5s to 2s
2. **app/Services/DatabaseConnectionChecker.php** - Added circuit breaker, removed retries, prioritized taufiq database cache, reduced cache duration for real-time updates
3. **app/Http/Middleware/InjectDatabaseStatus.php** - Updated cache duration (5 min online / 15s offline)
4. **routes/web.php** - Added `/api/database-status` endpoint for real-time monitoring
5. **resources/views/components/database-status-indicator.blade.php** - Added auto-refresh JavaScript with toast notifications
6. **DATABASE_TIMEOUT_FIX.md** - Complete documentation for team (this file)

## Technical Details

### Connection Timeout Configuration by Engine

**MySQL (PDO_MYSQL)**
- Uses `PDO::ATTR_TIMEOUT` option
- Set to 0.5 seconds
- Affects both connection and query timeouts

**PostgreSQL (PDO_PGSQL)**
- Uses both `PDO::ATTR_TIMEOUT` and `connect_timeout`
- Set to 0.5 seconds
- `connect_timeout` is PostgreSQL-specific parameter

**SQL Server (PDO_SQLSRV)**
- Does NOT support `PDO::ATTR_TIMEOUT`
- Uses `ConnectTimeout` and `LoginTimeout` options
- Set to 2 seconds (minimum practical value)
- Note: SQL Server timeouts are less reliable on Windows

### Why Circuit Breaker Uses 30 Seconds

**30-second duration chosen because:**
1. Long enough to prevent excessive connection attempts
2. Short enough to detect recovery reasonably quickly
3. Balances between performance and recovery detection
4. Combined with 60-second cache, provides good UX

### Why Cache Duration is Adaptive

**30 minutes (all online):**
- Stable state - connections unlikely to change
- Minimizes overhead
- Good performance

**60 seconds (any offline):**
- Frequent checks for recovery
- Quick detection when databases come back online
- Still prevents excessive connection attempts (circuit breaker active)

## Troubleshooting

### Issue: Page still hangs for 60 seconds
**Solution:**
```bash
# Clear cache and restart server
php artisan db:clear-status-cache
# Stop server (Ctrl+C)
composer dev
```

### Issue: Application doesn't detect database is back online
**Solution:**
- Wait up to 60 seconds for cache to expire
- OR force refresh: http://127.0.0.1:8000/?refresh_db_status=1
- OR clear cache: `php artisan db:clear-status-cache`

### Issue: "Circuit breaker active" message persists
**Solution:**
- Wait 30 seconds for circuit breaker to reset
- Circuit breaker will automatically retry after expiry
- OR clear file cache: `php artisan cache:clear`

## Future Improvements

1. **Configurable timeouts** - Allow environment-based timeout configuration
2. **Health check endpoint** - Dedicated API endpoint for database status
3. **Admin dashboard** - Real-time connection monitoring UI
4. **Alerting** - Notify when databases go offline/online
5. **Connection pooling** - Reuse connections to reduce overhead

## Summary

This fix ensures the application remains responsive even when remote databases (eilya, shafiqah, atiqah, danish) are offline. The multi-layered approach (reduced timeouts + no retries + circuit breaker + taufiq database cache + request mutex) provides:

- **Fast failures** (2-5 seconds instead of 60+)
- **Instant subsequent requests** (circuit breaker)
- **Automatic recovery** (cache expiry + retry logic)
- **Resilient caching** (taufiq database + file fallback)
- **Prevents stampede** (request mutex/lock)

### Team Member Requirements

**For all team members:**
- ✅ **Taufiq database MUST be online** (required for login and cache)
- ✅ Pull these changes to prevent timeout issues
- ✅ Your own database connection should be online
- ⚠️ Other team members' databases can be offline (their modules unavailable)

**Example scenarios:**
- **Taufiq working alone**: Taufiq DB must be online. Others can be offline. ✅
- **Danish working alone**: Taufiq + Danish DBs must be online. Others can be offline. ✅
- **All working together**: All databases online for full functionality. ✅
- **Taufiq DB offline**: Nobody can work (authentication fails). ❌
