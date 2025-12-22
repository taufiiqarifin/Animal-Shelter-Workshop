# SSH Tunnel Troubleshooting Guide

## Overview

This document identifies potential problems with the SSH tunnel setup for the distributed database architecture and provides comprehensive solutions. This project connects 5 separate databases across team members using SSH tunnels.

---

## Problems Identified

### 1. Configuration Issues

#### Problem 1.1: Naming Inconsistency
**Location:** `ssh-tunnels.md:7`
```bash
ssh -N -L 3309:127.0.0.1:3306 piqa@10.18.26.121
```

**Issue:** The SSH command uses username `piqa`, but the project uses connection name `shafiqah` everywhere else (CLAUDE.md, .env.example, database config). This creates confusion.

**Impact:**
- Team members unsure which username to use
- Documentation inconsistency
- Potential connection failures if username doesn't exist on remote host

---

#### Problem 1.2: Windows Path Escaping
**Location:** `ssh-tunnels.md:10`
```bash
ssh -N -L 1434:127.0.0.1:1433 laptop-4k8hhere\\\\user@10.18.26.18
```

**Issue:** The username contains quadruple backslashes (`\\\\`), which is over-escaped for most terminals.

**Impact:**
- Command will fail in Git Bash, PowerShell, and CMD with incorrect escaping
- Correct escaping varies by shell:
  - Git Bash/Linux: `laptop-4k8hhere\\user@10.18.26.18` (2 backslashes)
  - PowerShell: May need different escaping or quotes
  - CMD: May interpret backslashes differently

---

#### Problem 1.3: Missing SSH Configuration Details
**Issue:** No information about:
- Which SSH port to use (default 22 or custom?)
- Whether SSH keys are configured
- SSH server configuration requirements

**Impact:**
- Connection failures if non-standard SSH port
- Unclear authentication method
- No guidance for first-time setup

---

### 2. Security Issues

#### Problem 2.1: Plain Text Passwords
**Location:** `ssh-tunnels.md` (lines 2, 5, 8, 11, 14)

**Issue:** SSH passwords stored in plain text in repository:
```
eilya0624
atiqah03
piqa0912.
danimran74
taufiq@716544
```

**Impact:**
- **CRITICAL SECURITY RISK**: Anyone with repository access has all SSH passwords
- Passwords exposed in version control history (even if deleted later)
- Violates security best practices
- Potential unauthorized access to all databases

---

#### Problem 2.2: Database Passwords in .env.example
**Location:** `.env.example` (various lines)

**Issue:** Database passwords stored in example file:
```env
DB1_PASSWORD=Efpbts1370624!
DB2_PASSWORD=Atiqah76_
DB4_PASSWORD=danimran74
```

**Impact:**
- Credentials exposed if .env.example is committed (it is)
- Production credentials might be reused
- Security audit failure

---

### 3. Operational Issues

#### Problem 3.1: Foreground SSH Processes
**Issue:** All SSH commands use `-N` flag without background execution (`-f`).

**Impact:**
- Blocks terminal window for each tunnel (need 4 terminals open simultaneously)
- Terminal closure kills the tunnel
- Difficult to manage multiple tunnels
- No way to check if tunnels are still running

**Current behavior:**
```bash
# Runs tunnel but blocks terminal
ssh -N -L 3307:127.0.0.1:3306 eilya@10.18.26.14
# User cannot type more commands here ^
```

---

#### Problem 3.2: No Connection Validation
**Issue:** No verification that:
- SSH tunnel is successfully established
- Remote database service is running
- Port forwarding is working
- Database is accessible through tunnel

**Impact:**
- Silent failures - tunnel appears to work but database unreachable
- Time wasted debugging at application layer instead of network layer
- No clear error messages

---

#### Problem 3.3: No Auto-Reconnect
**Issue:** SSH tunnels can drop due to:
- Network interruptions
- SSH timeout
- Remote server restart
- Idle connection timeout

**Impact:**
- Application suddenly loses database connectivity
- Manual intervention required to restart tunnel
- Data loss if transaction was in progress
- Poor development experience

---

#### Problem 3.4: No Process Management
**Issue:** No documentation for:
- How to check if tunnels are running
- How to stop tunnels gracefully
- How to restart failed tunnels
- Which tunnel corresponds to which process ID

**Impact:**
- Zombie SSH processes accumulate
- Port conflicts from orphaned tunnels
- Difficult to troubleshoot connection issues
- Resource leaks on developer machines

---

### 4. Network Issues

#### Problem 4.1: Port Conflicts
**Issue:** If a tunnel is already running on a local port (e.g., 3307) and someone tries to start it again:

```bash
bind [127.0.0.1]:3307: Address already in use
channel_setup_fwd_listener_tcpip: cannot listen to port: 3307
```

**Impact:**
- Confusing error messages
- Developer doesn't know if tunnel is working
- Application might connect to wrong database
- No guidance on resolving conflicts

---

#### Problem 4.2: Firewall Blocking
**Issue:** Windows Firewall / Corporate firewalls might block:
- Outgoing SSH connections (port 22)
- Local port bindings (3307, 3308, 3309, 1434, 5434)
- Remote database ports on local interface

**Impact:**
- Connection failures without clear error messages
- Different behavior on different developer machines
- Difficult to diagnose

---

#### Problem 4.3: Network Topology Issues
**Issue:** The IP addresses (10.18.26.x) suggest local network topology:
- What if team members are not on the same network?
- What if someone is working from home?
- What if VPN is required?

**Impact:**
- Connection failures outside office network
- Remote work challenges
- No VPN configuration guidance

---

#### Problem 4.4: SSH Timeout Configuration
**Issue:** Default SSH timeout settings can cause:
- Idle connection drops (typically 10-15 minutes)
- Connection resets during long-running queries
- Unexpected disconnections during development

**Impact:**
- Tunnels drop during coffee breaks
- Need to constantly reconnect
- Interrupted work sessions

---

### 5. Database-Specific Issues

#### Problem 5.1: No Database Service Verification
**Issue:** SSH tunnel might work, but remote database service could be:
- Not running
- Configured to bind only to specific interfaces (not 127.0.0.1)
- Using different ports than expected
- Protected by database-level firewall rules

**Impact:**
- SSH tunnel succeeds but database connection fails
- Confusing error messages (connection refused vs authentication failed)
- Time wasted troubleshooting wrong layer

---

#### Problem 5.2: Database Client Requirements
**Issue:** No documentation about required database clients:
- MySQL client for Eilya, Atiqah, Shafiqah databases
- SQL Server client (sqlcmd) for Danish database
- PostgreSQL client (psql) for Taufiq database

**Impact:**
- Can't verify connections manually
- Can't run diagnostic queries
- Laravel connection failures hard to debug

---

#### Problem 5.3: Database Version Compatibility
**Issue:** No documentation about database versions:
- MySQL version (5.7, 8.0, 8.4?)
- PostgreSQL version (12, 13, 14, 15, 16?)
- SQL Server version (2017, 2019, 2022?)

**Impact:**
- PHP extension incompatibilities
- SQL syntax differences
- Migration failures
- Feature availability issues

---

### 6. Team Collaboration Issues

#### Problem 6.1: Unclear Tunnel Requirements
**Issue:** Not clear which team member needs which tunnels:

**Example:** Taufiq needs tunnels to all 4 remote databases:
```bash
ssh -N -L 3307:... eilya@...    # Eilya's DB
ssh -N -L 3308:... atiqah@...   # Atiqah's DB
ssh -N -L 3309:... shafiqah@... # Shafiqah's DB
ssh -N -L 1434:... danish@...   # Danish's DB
# But NOT to his own PostgreSQL (it's local)
```

But Eilya needs tunnels to 4 different databases (all except his own).

**Impact:**
- Team members run wrong commands
- Connect to wrong databases
- Confusion about setup requirements
- Time wasted in team coordination

---

#### Problem 6.2: No Troubleshooting Workflow
**Issue:** When connections fail, no documented steps for:
1. Verifying network connectivity
2. Testing SSH access
3. Checking database service status
4. Validating tunnel establishment
5. Testing database connectivity through tunnel

**Impact:**
- Each team member troubleshoots independently
- Same problems solved multiple times
- Knowledge not shared
- Inefficient debugging

---

#### Problem 6.3: No Team Status Dashboard
**Issue:** No way to know:
- Which databases are currently online
- Who is connected to which database
- Current connection health
- Database performance issues

**Impact:**
- "It works on my machine" problems
- Can't identify if issue is local or remote
- Difficult to coordinate team development

---

### 7. Cross-Platform Issues

#### Problem 7.1: Windows vs Linux Command Differences
**Issue:** SSH command syntax differs between:

**Git Bash (Windows):**
```bash
ssh -N -L 3307:127.0.0.1:3306 eilya@10.18.26.14
```

**PowerShell (Windows):**
```powershell
# May need quotes around connection string
ssh -N -L "3307:127.0.0.1:3306" eilya@10.18.26.14
```

**CMD (Windows):**
```cmd
# Limited SSH support, might need PuTTY
```

**Impact:**
- Commands fail on different shells
- Team members using different terminals get different errors
- Documentation not universally applicable

---

#### Problem 7.2: Path Separators
**Issue:** Danish's username contains Windows path separator:
```
laptop-4k8hhere\user
```

This needs different escaping on different platforms:
- Windows Git Bash: `laptop-4k8hhere\\user`
- Linux/Mac: `laptop-4k8hhere\\user`
- PowerShell: Might need `'laptop-4k8hhere\user'` (quoted)

**Impact:**
- Connection failures on different platforms
- Inconsistent escaping requirements
- Platform-specific debugging needed

---

#### Problem 7.3: Line Endings
**Issue:** `ssh-tunnels.md` might have different line endings (CRLF vs LF) on different platforms.

**Impact:**
- Copy-paste commands might include carriage returns
- Commands fail with invisible characters
- Difficult to debug

---

### 8. Resource Management Issues

#### Problem 8.1: No Cleanup Instructions
**Issue:** No documented process for:
- Stopping all tunnels at end of day
- Cleaning up orphaned SSH processes
- Freeing port bindings
- System shutdown procedures

**Impact:**
```bash
# After days of development:
$ netstat -ano | findstr :3307
  TCP    127.0.0.1:3307    0.0.0.0:0    LISTENING    12345
  TCP    127.0.0.1:3307    0.0.0.0:0    LISTENING    67890
  TCP    127.0.0.1:3307    0.0.0.0:0    LISTENING    11111
# Multiple processes bound to same port (shouldn't happen but does)
```

---

#### Problem 8.2: Zombie SSH Processes
**Issue:** SSH processes can become orphaned when:
- Terminal is force-closed
- System crashes
- SSH client terminates abnormally

**Impact:**
- Ports remain bound
- System resources consumed
- New tunnels can't start
- Requires manual process killing

---

#### Problem 8.3: Memory Leaks
**Issue:** Long-running SSH tunnels can accumulate:
- Connection state in SSH client
- TCP socket buffers
- Authentication tokens

**Impact:**
- Gradual performance degradation
- Need to restart tunnels periodically
- System slowdown over time

---

## Solutions

### Solution Set 1: Configuration Fixes

#### Solution 1.1: Standardize Naming
**Action:** Create a corrected `ssh-tunnels-CORRECTED.md`:

```bash
# Eilya's MySQL Database (Stray Reporting Module)
ssh -N -L 3307:127.0.0.1:3306 eilya@10.18.26.14
# Password: [Use SSH keys instead - see Solution 2.1]

# Atiqah's MySQL Database (Shelter Management Module)
ssh -N -L 3308:127.0.0.1:3306 atiqah@10.18.26.84
# Password: [Use SSH keys instead - see Solution 2.1]

# Shafiqah's MySQL Database (Animal Management Module)
ssh -N -L 3309:127.0.0.1:3306 shafiqah@10.18.26.121
# Password: [Use SSH keys instead - see Solution 2.1]

# Danish's SQL Server Database (Booking & Adoption Module)
ssh -N -L 1434:127.0.0.1:1433 'laptop-4k8hhere\user'@10.18.26.18
# Password: [Use SSH keys instead - see Solution 2.1]
# Note: Username quoted to handle backslash correctly

# Taufiq's PostgreSQL Database (User Management Module)
ssh -N -L 5434:127.0.0.1:5432 taufi@10.18.26.156
# Password: [Use SSH keys instead - see Solution 2.1]
```

---

#### Solution 1.2: Fix Windows Path Escaping
**Platform-Specific Commands:**

**For Git Bash / WSL / Linux:**
```bash
ssh -N -L 1434:127.0.0.1:1433 laptop-4k8hhere\\user@10.18.26.18
```

**For PowerShell:**
```powershell
ssh -N -L 1434:127.0.0.1:1433 'laptop-4k8hhere\user'@10.18.26.18
```

**For PuTTY (Windows alternative):**
```
Host: 10.18.26.18
Username: laptop-4k8hhere\user
Local Port: 1434
Remote Host: 127.0.0.1
Remote Port: 1433
```

---

#### Solution 1.3: Document SSH Configuration
**Create `.ssh/config` entries for each team member:**

```ssh-config
# ~/.ssh/config

# Eilya's Database Server
Host eilya-db
    HostName 10.18.26.14
    User eilya
    LocalForward 3307 127.0.0.1:3306
    ServerAliveInterval 60
    ServerAliveCountMax 3
    IdentityFile ~/.ssh/id_rsa_workshop

# Atiqah's Database Server
Host atiqah-db
    HostName 10.18.26.84
    User atiqah
    LocalForward 3308 127.0.0.1:3306
    ServerAliveInterval 60
    ServerAliveCountMax 3
    IdentityFile ~/.ssh/id_rsa_workshop

# Shafiqah's Database Server
Host shafiqah-db
    HostName 10.18.26.121
    User shafiqah
    LocalForward 3309 127.0.0.1:3306
    ServerAliveInterval 60
    ServerAliveCountMax 3
    IdentityFile ~/.ssh/id_rsa_workshop

# Danish's Database Server
Host danish-db
    HostName 10.18.26.18
    User laptop-4k8hhere\user
    LocalForward 1434 127.0.0.1:1433
    ServerAliveInterval 60
    ServerAliveCountMax 3
    IdentityFile ~/.ssh/id_rsa_workshop

# Taufiq's Database Server
Host taufiq-db
    HostName 10.18.26.156
    User taufi
    LocalForward 5434 127.0.0.1:5432
    ServerAliveInterval 60
    ServerAliveCountMax 3
    IdentityFile ~/.ssh/id_rsa_workshop
```

**Then simplify tunnel commands to:**
```bash
ssh -N eilya-db
ssh -N atiqah-db
ssh -N shafiqah-db
ssh -N danish-db
ssh -N taufiq-db
```

---

### Solution Set 2: Security Improvements

#### Solution 2.1: Implement SSH Key Authentication
**Step 1: Generate SSH Key Pair (one-time per developer)**

```bash
# On developer machine
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa_workshop -C "workshop2-team"
# Press Enter when asked for passphrase (or set a strong one)
```

**Step 2: Distribute Public Keys**

Each team member copies their public key to their own server:

```bash
# Taufiq copies his key to his server
ssh-copy-id -i ~/.ssh/id_rsa_workshop.pub taufi@10.18.26.156

# Eilya copies his key to his server
ssh-copy-id -i ~/.ssh/id_rsa_workshop.pub eilya@10.18.26.14

# Atiqah copies her key to her server
ssh-copy-id -i ~/.ssh/id_rsa_workshop.pub atiqah@10.18.26.84

# Shafiqah copies her key to her server
ssh-copy-id -i ~/.ssh/id_rsa_workshop.pub shafiqah@10.18.26.121

# Danish copies his key to his server
ssh-copy-id -i ~/.ssh/id_rsa_workshop.pub 'laptop-4k8hhere\user'@10.18.26.18
```

**Step 3: Share Public Keys Among Team**

Each team member needs access to all OTHER servers. Create a shared document (NOT in repo) with:
- Taufiq's public key → Eilya, Atiqah, Shafiqah, Danish add to their `~/.ssh/authorized_keys`
- Eilya's public key → Taufiq, Atiqah, Shafiqah, Danish add to their `~/.ssh/authorized_keys`
- Atiqah's public key → Taufiq, Eilya, Shafiqah, Danish add to their `~/.ssh/authorized_keys`
- Shafiqah's public key → Taufiq, Eilya, Atiqah, Danish add to their `~/.ssh/authorized_keys`
- Danish's public key → Taufiq, Eilya, Atiqah, Shafiqah add to their `~/.ssh/authorized_keys`

**Step 4: Test Key Authentication**

```bash
ssh -i ~/.ssh/id_rsa_workshop eilya@10.18.26.14
# Should connect without password
```

**Benefits:**
- No passwords in repository
- More secure authentication
- Can revoke access by removing public key
- Easier automation

---

#### Solution 2.2: Remove Passwords from Repository
**Immediate Actions:**

1. **Delete passwords from `ssh-tunnels.md`:**
```bash
git rm ssh-tunnels.md
git commit -m "Remove SSH passwords from repository"
```

2. **Update `.env.example` to use placeholders:**
```env
# Instead of:
DB1_PASSWORD=Efpbts1370624!

# Use:
DB1_PASSWORD=your_password_here
```

3. **Add to `.gitignore`:**
```gitignore
# SSH credentials (if team still uses password files locally)
ssh-passwords.txt
ssh-tunnels-with-passwords.md
*.password
*.credentials
```

4. **Rotate compromised passwords** on all servers (if this repo was public or shared widely)

---

#### Solution 2.3: Use Environment Variables for Tunnel Management
**Create a secure credential store:**

```bash
# ~/.workshop2-credentials (NOT in repo, in .gitignore)
export EILYA_SSH_KEY=~/.ssh/id_rsa_workshop
export ATIQAH_SSH_KEY=~/.ssh/id_rsa_workshop
export SHAFIQAH_SSH_KEY=~/.ssh/id_rsa_workshop
export DANISH_SSH_KEY=~/.ssh/id_rsa_workshop
export TAUFIQ_SSH_KEY=~/.ssh/id_rsa_workshop
```

---

### Solution Set 3: Operational Improvements

#### Solution 3.1: Background SSH Processes
**Add `-f` flag to run in background:**

```bash
# Runs in background, frees terminal
ssh -f -N -L 3307:127.0.0.1:3306 eilya@10.18.26.14

# Alternative: Use nohup for persistence
nohup ssh -N -L 3307:127.0.0.1:3306 eilya@10.18.26.14 &
```

**Better: Create a tunnel management script (see Solution 3.4)**

---

#### Solution 3.2: Connection Validation Script
**Create `scripts/test-db-tunnels.sh`:**

```bash
#!/bin/bash

echo "Testing SSH Tunnels and Database Connectivity..."
echo "=================================================="
echo ""

# Test Eilya's MySQL (Port 3307)
echo "Testing Eilya's Database (MySQL on port 3307)..."
nc -zv 127.0.0.1 3307 2>&1 | grep succeeded && echo "✓ Tunnel is UP" || echo "✗ Tunnel is DOWN"
mysql -h 127.0.0.1 -P 3307 -u root -p'Efpbts1370624!' -e "SELECT 'Connection successful' as Status;" 2>/dev/null && echo "✓ Database is ACCESSIBLE" || echo "✗ Database is INACCESSIBLE"
echo ""

# Test Atiqah's MySQL (Port 3308)
echo "Testing Atiqah's Database (MySQL on port 3308)..."
nc -zv 127.0.0.1 3308 2>&1 | grep succeeded && echo "✓ Tunnel is UP" || echo "✗ Tunnel is DOWN"
mysql -h 127.0.0.1 -P 3308 -u root -p'Atiqah76_' -e "SELECT 'Connection successful' as Status;" 2>/dev/null && echo "✓ Database is ACCESSIBLE" || echo "✗ Database is INACCESSIBLE"
echo ""

# Test Shafiqah's MySQL (Port 3309)
echo "Testing Shafiqah's Database (MySQL on port 3309)..."
nc -zv 127.0.0.1 3309 2>&1 | grep succeeded && echo "✓ Tunnel is UP" || echo "✗ Tunnel is DOWN"
mysql -h 127.0.0.1 -P 3309 -u root -ppassword -e "SELECT 'Connection successful' as Status;" 2>/dev/null && echo "✓ Database is ACCESSIBLE" || echo "✗ Database is INACCESSIBLE"
echo ""

# Test Danish's SQL Server (Port 1434)
echo "Testing Danish's Database (SQL Server on port 1434)..."
nc -zv 127.0.0.1 1434 2>&1 | grep succeeded && echo "✓ Tunnel is UP" || echo "✗ Tunnel is DOWN"
sqlcmd -S 127.0.0.1,1434 -U sa -P danimran74 -Q "SELECT 'Connection successful' as Status;" 2>/dev/null && echo "✓ Database is ACCESSIBLE" || echo "✗ Database is INACCESSIBLE"
echo ""

# Test Taufiq's PostgreSQL (Port 5434)
echo "Testing Taufiq's Database (PostgreSQL on port 5434)..."
nc -zv 127.0.0.1 5434 2>&1 | grep succeeded && echo "✓ Tunnel is UP" || echo "✗ Tunnel is DOWN"
PGPASSWORD=password psql -h 127.0.0.1 -p 5434 -U postgres -d workshop -c "SELECT 'Connection successful' as Status;" 2>/dev/null && echo "✓ Database is ACCESSIBLE" || echo "✗ Database is INACCESSIBLE"
echo ""

echo "=================================================="
echo "Test complete!"
```

**Windows PowerShell version (`scripts/test-db-tunnels.ps1`):**

```powershell
Write-Host "Testing SSH Tunnels and Database Connectivity..." -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

# Test Eilya's MySQL (Port 3307)
Write-Host "Testing Eilya's Database (MySQL on port 3307)..." -ForegroundColor Yellow
Test-NetConnection -ComputerName 127.0.0.1 -Port 3307 -InformationLevel Quiet
if ($?) { Write-Host "✓ Tunnel is UP" -ForegroundColor Green } else { Write-Host "✗ Tunnel is DOWN" -ForegroundColor Red }

# Test Atiqah's MySQL (Port 3308)
Write-Host "`nTesting Atiqah's Database (MySQL on port 3308)..." -ForegroundColor Yellow
Test-NetConnection -ComputerName 127.0.0.1 -Port 3308 -InformationLevel Quiet
if ($?) { Write-Host "✓ Tunnel is UP" -ForegroundColor Green } else { Write-Host "✗ Tunnel is DOWN" -ForegroundColor Red }

# Test Shafiqah's MySQL (Port 3309)
Write-Host "`nTesting Shafiqah's Database (MySQL on port 3309)..." -ForegroundColor Yellow
Test-NetConnection -ComputerName 127.0.0.1 -Port 3309 -InformationLevel Quiet
if ($?) { Write-Host "✓ Tunnel is UP" -ForegroundColor Green } else { Write-Host "✗ Tunnel is DOWN" -ForegroundColor Red }

# Test Danish's SQL Server (Port 1434)
Write-Host "`nTesting Danish's Database (SQL Server on port 1434)..." -ForegroundColor Yellow
Test-NetConnection -ComputerName 127.0.0.1 -Port 1434 -InformationLevel Quiet
if ($?) { Write-Host "✓ Tunnel is UP" -ForegroundColor Green } else { Write-Host "✗ Tunnel is DOWN" -ForegroundColor Red }

# Test Taufiq's PostgreSQL (Port 5434)
Write-Host "`nTesting Taufiq's Database (PostgreSQL on port 5434)..." -ForegroundColor Yellow
Test-NetConnection -ComputerName 127.0.0.1 -Port 5434 -InformationLevel Quiet
if ($?) { Write-Host "✓ Tunnel is UP" -ForegroundColor Green } else { Write-Host "✗ Tunnel is DOWN" -ForegroundColor Red }

Write-Host "`n==================================================" -ForegroundColor Cyan
Write-Host "Test complete!" -ForegroundColor Cyan
```

---

#### Solution 3.3: Auto-Reconnect with autossh
**Install autossh:**

```bash
# Linux
sudo apt install autossh

# Mac
brew install autossh

# Windows (via WSL or Git Bash)
# Download from https://www.harding.motd.ca/autossh/
```

**Use autossh instead of ssh:**

```bash
# Auto-reconnects if connection drops
autossh -M 0 -N -L 3307:127.0.0.1:3306 eilya@10.18.26.14 \
    -o "ServerAliveInterval 60" \
    -o "ServerAliveCountMax 3" \
    -o "ExitOnForwardFailure yes"
```

**Configure in `~/.ssh/config` for compatibility:**

```ssh-config
Host eilya-db
    HostName 10.18.26.14
    User eilya
    LocalForward 3307 127.0.0.1:3306
    ServerAliveInterval 60
    ServerAliveCountMax 3
    ExitOnForwardFailure yes
    IdentityFile ~/.ssh/id_rsa_workshop
```

**Then use:**
```bash
autossh -M 0 -f -N eilya-db
```

---

#### Solution 3.4: Comprehensive Tunnel Management Script
**Create `scripts/manage-tunnels.sh`:**

```bash
#!/bin/bash

# Workshop 2 - SSH Tunnel Management Script
# Manages all 5 database SSH tunnels

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PID_DIR="$SCRIPT_DIR/../.ssh-tunnels"
mkdir -p "$PID_DIR"

# Define tunnels (format: name:local_port:remote_host:remote_port:ssh_target)
TUNNELS=(
    "eilya:3307:127.0.0.1:3306:eilya@10.18.26.14"
    "atiqah:3308:127.0.0.1:3306:atiqah@10.18.26.84"
    "shafiqah:3309:127.0.0.1:3306:shafiqah@10.18.26.121"
    "danish:1434:127.0.0.1:1433:laptop-4k8hhere\\\\user@10.18.26.18"
    "taufiq:5434:127.0.0.1:5432:taufi@10.18.26.156"
)

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

start_tunnel() {
    local name=$1
    local local_port=$2
    local remote_host=$3
    local remote_port=$4
    local ssh_target=$5
    local pid_file="$PID_DIR/$name.pid"

    echo -e "${YELLOW}Starting tunnel: $name (port $local_port)...${NC}"

    # Check if already running
    if [ -f "$pid_file" ]; then
        local pid=$(cat "$pid_file")
        if ps -p $pid > /dev/null 2>&1; then
            echo -e "${GREEN}✓ Tunnel already running (PID: $pid)${NC}"
            return 0
        else
            rm "$pid_file"
        fi
    fi

    # Start tunnel in background
    ssh -f -N -L "$local_port:$remote_host:$remote_port" \
        -o "ServerAliveInterval=60" \
        -o "ServerAliveCountMax=3" \
        -o "ExitOnForwardFailure=yes" \
        -o "StrictHostKeyChecking=no" \
        "$ssh_target"

    if [ $? -eq 0 ]; then
        # Find and save PID
        sleep 1
        local pid=$(pgrep -f "ssh.*$local_port:$remote_host:$remote_port")
        echo $pid > "$pid_file"
        echo -e "${GREEN}✓ Tunnel started successfully (PID: $pid)${NC}"
    else
        echo -e "${RED}✗ Failed to start tunnel${NC}"
        return 1
    fi
}

stop_tunnel() {
    local name=$1
    local pid_file="$PID_DIR/$name.pid"

    echo -e "${YELLOW}Stopping tunnel: $name...${NC}"

    if [ -f "$pid_file" ]; then
        local pid=$(cat "$pid_file")
        if ps -p $pid > /dev/null 2>&1; then
            kill $pid
            rm "$pid_file"
            echo -e "${GREEN}✓ Tunnel stopped${NC}"
        else
            rm "$pid_file"
            echo -e "${YELLOW}⚠ Tunnel was not running${NC}"
        fi
    else
        echo -e "${YELLOW}⚠ No PID file found${NC}"
    fi
}

status_tunnel() {
    local name=$1
    local local_port=$2
    local pid_file="$PID_DIR/$name.pid"

    if [ -f "$pid_file" ]; then
        local pid=$(cat "$pid_file")
        if ps -p $pid > /dev/null 2>&1; then
            echo -e "${GREEN}✓ $name (port $local_port) - RUNNING (PID: $pid)${NC}"

            # Test port connectivity
            nc -z 127.0.0.1 $local_port 2>&1 | grep succeeded > /dev/null
            if [ $? -eq 0 ]; then
                echo -e "  ${GREEN}✓ Port is accepting connections${NC}"
            else
                echo -e "  ${RED}✗ Port is not responding${NC}"
            fi
        else
            echo -e "${RED}✗ $name (port $local_port) - STOPPED (stale PID)${NC}"
            rm "$pid_file"
        fi
    else
        echo -e "${RED}✗ $name (port $local_port) - STOPPED${NC}"
    fi
}

start_all() {
    echo "Starting all tunnels..."
    for tunnel in "${TUNNELS[@]}"; do
        IFS=':' read -r name local_port remote_host remote_port ssh_target <<< "$tunnel"
        start_tunnel "$name" "$local_port" "$remote_host" "$remote_port" "$ssh_target"
        echo ""
    done
}

stop_all() {
    echo "Stopping all tunnels..."
    for tunnel in "${TUNNELS[@]}"; do
        IFS=':' read -r name local_port remote_host remote_port ssh_target <<< "$tunnel"
        stop_tunnel "$name"
    done
}

restart_all() {
    echo "Restarting all tunnels..."
    stop_all
    sleep 2
    start_all
}

status_all() {
    echo "Tunnel Status:"
    echo "=============="
    for tunnel in "${TUNNELS[@]}"; do
        IFS=':' read -r name local_port remote_host remote_port ssh_target <<< "$tunnel"
        status_tunnel "$name" "$local_port"
        echo ""
    done
}

# Main command dispatcher
case "$1" in
    start)
        if [ -z "$2" ]; then
            start_all
        else
            for tunnel in "${TUNNELS[@]}"; do
                IFS=':' read -r name local_port remote_host remote_port ssh_target <<< "$tunnel"
                if [ "$name" == "$2" ]; then
                    start_tunnel "$name" "$local_port" "$remote_host" "$remote_port" "$ssh_target"
                    exit 0
                fi
            done
            echo "Unknown tunnel: $2"
            exit 1
        fi
        ;;
    stop)
        if [ -z "$2" ]; then
            stop_all
        else
            stop_tunnel "$2"
        fi
        ;;
    restart)
        if [ -z "$2" ]; then
            restart_all
        else
            stop_tunnel "$2"
            sleep 1
            for tunnel in "${TUNNELS[@]}"; do
                IFS=':' read -r name local_port remote_host remote_port ssh_target <<< "$tunnel"
                if [ "$name" == "$2" ]; then
                    start_tunnel "$name" "$local_port" "$remote_host" "$remote_port" "$ssh_target"
                    exit 0
                fi
            done
        fi
        ;;
    status)
        status_all
        ;;
    *)
        echo "Usage: $0 {start|stop|restart|status} [tunnel_name]"
        echo ""
        echo "Available tunnels:"
        for tunnel in "${TUNNELS[@]}"; do
            IFS=':' read -r name local_port remote_host remote_port ssh_target <<< "$tunnel"
            echo "  - $name (port $local_port)"
        done
        exit 1
        ;;
esac
```

**Usage:**

```bash
# Make executable
chmod +x scripts/manage-tunnels.sh

# Start all tunnels
./scripts/manage-tunnels.sh start

# Start specific tunnel
./scripts/manage-tunnels.sh start eilya

# Check status
./scripts/manage-tunnels.sh status

# Stop all tunnels
./scripts/manage-tunnels.sh stop

# Restart all tunnels
./scripts/manage-tunnels.sh restart
```

---

### Solution Set 4: Network Issue Resolutions

#### Solution 4.1: Port Conflict Resolution
**Check for port conflicts before starting:**

```bash
# Linux/Mac/Git Bash
netstat -tuln | grep :3307

# Windows PowerShell
Get-NetTCPConnection -LocalPort 3307

# If port is in use, find and kill the process:
# Linux/Mac
lsof -ti:3307 | xargs kill -9

# Windows
Get-Process -Id (Get-NetTCPConnection -LocalPort 3307).OwningProcess | Stop-Process -Force
```

**Add to tunnel management script:**

```bash
check_port_conflict() {
    local port=$1
    local pid=$(lsof -ti:$port 2>/dev/null)

    if [ ! -z "$pid" ]; then
        echo -e "${YELLOW}⚠ Port $port is already in use by PID $pid${NC}"
        read -p "Kill the process? (y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            kill -9 $pid
            echo -e "${GREEN}✓ Process killed${NC}"
            return 0
        else
            echo -e "${RED}✗ Cannot start tunnel - port conflict${NC}"
            return 1
        fi
    fi
    return 0
}
```

---

#### Solution 4.2: Firewall Configuration
**Windows Firewall Rules:**

```powershell
# Run as Administrator
# Allow outgoing SSH (port 22)
New-NetFirewallRule -DisplayName "SSH Outbound" -Direction Outbound -Protocol TCP -RemotePort 22 -Action Allow

# Allow local tunnel ports (inbound on loopback only)
New-NetFirewallRule -DisplayName "MySQL Tunnel 3307" -Direction Inbound -Protocol TCP -LocalPort 3307 -RemoteAddress 127.0.0.1 -Action Allow
New-NetFirewallRule -DisplayName "MySQL Tunnel 3308" -Direction Inbound -Protocol TCP -LocalPort 3308 -RemoteAddress 127.0.0.1 -Action Allow
New-NetFirewallRule -DisplayName "MySQL Tunnel 3309" -Direction Inbound -Protocol TCP -LocalPort 3309 -RemoteAddress 127.0.0.1 -Action Allow
New-NetFirewallRule -DisplayName "MSSQL Tunnel 1434" -Direction Inbound -Protocol TCP -LocalPort 1434 -RemoteAddress 127.0.0.1 -Action Allow
New-NetFirewallRule -DisplayName "PostgreSQL Tunnel 5434" -Direction Inbound -Protocol TCP -LocalPort 5434 -RemoteAddress 127.0.0.1 -Action Allow
```

**Linux iptables (if applicable):**

```bash
# Allow outgoing SSH
sudo iptables -A OUTPUT -p tcp --dport 22 -j ACCEPT

# Allow local tunnel ports
sudo iptables -A INPUT -p tcp --dport 3307 -s 127.0.0.1 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 3308 -s 127.0.0.1 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 3309 -s 127.0.0.1 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 1434 -s 127.0.0.1 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 5434 -s 127.0.0.1 -j ACCEPT
```

---

#### Solution 4.3: VPN/Network Topology Guidance
**Document network requirements in `NETWORK-SETUP.md`:**

```markdown
# Network Setup Requirements

## On-Campus Development
- All team members must be on UTeM network (10.18.26.x subnet)
- Direct SSH access to team member machines
- No VPN required

## Remote Development (Work from Home)
1. Connect to UTeM VPN first
2. Verify connectivity: `ping 10.18.26.14`
3. Then start SSH tunnels

## VPN Configuration
- VPN Server: [Ask network admin]
- VPN Client: [OpenVPN / Cisco AnyConnect / etc.]
- Username: UTeM student ID
- Password: UTeM portal password

## Troubleshooting Network Connectivity
```bash
# Test if remote host is reachable
ping 10.18.26.14

# Test if SSH port is open
nc -zv 10.18.26.14 22

# Test SSH connection (without tunnel)
ssh eilya@10.18.26.14 echo "Connection successful"
```

## Alternative: Cloud Deployment
If local network is unreliable, consider deploying databases to:
- AWS RDS (MySQL, PostgreSQL, SQL Server)
- Azure Database
- Google Cloud SQL
- Railway.app (free tier)
```

---

#### Solution 4.4: SSH Keep-Alive Configuration
**Add to `~/.ssh/config`:**

```ssh-config
# Global SSH keep-alive settings
Host *
    ServerAliveInterval 60
    ServerAliveCountMax 3
    TCPKeepAlive yes
```

**Or add to tunnel commands:**

```bash
ssh -N -L 3307:127.0.0.1:3306 \
    -o "ServerAliveInterval=60" \
    -o "ServerAliveCountMax=3" \
    -o "TCPKeepAlive=yes" \
    eilya@10.18.26.14
```

**Server-side configuration (on database hosts):**

Each team member should add to their `/etc/ssh/sshd_config`:

```bash
ClientAliveInterval 60
ClientAliveCountMax 3
TCPKeepAlive yes
```

Then restart SSH service:
```bash
sudo systemctl restart sshd
```

---

### Solution Set 5: Database-Specific Fixes

#### Solution 5.1: Database Service Verification Script
**Create `scripts/verify-remote-databases.sh`:**

```bash
#!/bin/bash

echo "Verifying Remote Database Services..."
echo "======================================"
echo ""

# Eilya's MySQL
echo "Checking Eilya's MySQL service..."
ssh eilya@10.18.26.14 "systemctl status mysql | grep 'Active:' || service mysql status" 2>/dev/null
ssh eilya@10.18.26.14 "netstat -tuln | grep :3306 || ss -tuln | grep :3306" 2>/dev/null
echo ""

# Atiqah's MySQL
echo "Checking Atiqah's MySQL service..."
ssh atiqah@10.18.26.84 "systemctl status mysql | grep 'Active:' || service mysql status" 2>/dev/null
ssh atiqah@10.18.26.84 "netstat -tuln | grep :3306 || ss -tuln | grep :3306" 2>/dev/null
echo ""

# Shafiqah's MySQL
echo "Checking Shafiqah's MySQL service..."
ssh shafiqah@10.18.26.121 "systemctl status mysql | grep 'Active:' || service mysql status" 2>/dev/null
ssh shafiqah@10.18.26.121 "netstat -tuln | grep :3306 || ss -tuln | grep :3306" 2>/dev/null
echo ""

# Danish's SQL Server
echo "Checking Danish's SQL Server service..."
ssh 'laptop-4k8hhere\user'@10.18.26.18 "sc query MSSQLSERVER" 2>/dev/null
ssh 'laptop-4k8hhere\user'@10.18.26.18 "netstat -an | findstr :1433" 2>/dev/null
echo ""

# Taufiq's PostgreSQL
echo "Checking Taufiq's PostgreSQL service..."
ssh taufi@10.18.26.156 "systemctl status postgresql | grep 'Active:' || service postgresql status" 2>/dev/null
ssh taufi@10.18.26.156 "netstat -tuln | grep :5432 || ss -tuln | grep :5432" 2>/dev/null
echo ""

echo "======================================"
echo "Verification complete!"
```

---

#### Solution 5.2: Database Client Installation Guide
**Create `DATABASE-CLIENTS-SETUP.md`:**

```markdown
# Database Client Installation Guide

## MySQL Client

### Windows
```bash
# Download MySQL Installer from https://dev.mysql.com/downloads/installer/
# Or use Chocolatey:
choco install mysql-cli
```

### Linux (Ubuntu/Debian)
```bash
sudo apt update
sudo apt install mysql-client
```

### Mac
```bash
brew install mysql-client
echo 'export PATH="/usr/local/opt/mysql-client/bin:$PATH"' >> ~/.zshrc
```

### Test MySQL Connection
```bash
mysql -h 127.0.0.1 -P 3307 -u root -p
# Enter password when prompted
mysql> SELECT VERSION();
mysql> SHOW DATABASES;
```

## SQL Server Client

### Windows
```bash
# Download from https://learn.microsoft.com/en-us/sql/tools/sqlcmd-utility
# Or install SQL Server Management Studio (SSMS)
```

### Linux
```bash
curl https://packages.microsoft.com/keys/microsoft.asc | sudo apt-key add -
sudo add-apt-repository "$(wget -qO- https://packages.microsoft.com/config/ubuntu/20.04/prod.list)"
sudo apt update
sudo apt install mssql-tools unixodbc-dev
echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bashrc
```

### Mac
```bash
brew tap microsoft/mssql-release https://github.com/Microsoft/homebrew-mssql-release
brew install mssql-tools
```

### Test SQL Server Connection
```bash
sqlcmd -S 127.0.0.1,1434 -U sa -P your_password
1> SELECT @@VERSION;
2> GO
```

## PostgreSQL Client

### Windows
```bash
# Download from https://www.postgresql.org/download/windows/
# Or use Chocolatey:
choco install postgresql
```

### Linux (Ubuntu/Debian)
```bash
sudo apt update
sudo apt install postgresql-client
```

### Mac
```bash
brew install postgresql
```

### Test PostgreSQL Connection
```bash
PGPASSWORD=your_password psql -h 127.0.0.1 -p 5434 -U postgres -d workshop
postgres=# SELECT version();
postgres=# \l  # List databases
```

## Quick Connection Test (All Databases)

```bash
# MySQL (Eilya, Atiqah, Shafiqah)
mysql -h 127.0.0.1 -P 3307 -u root -p -e "SELECT 'Eilya DB OK' as status;"
mysql -h 127.0.0.1 -P 3308 -u root -p -e "SELECT 'Atiqah DB OK' as status;"
mysql -h 127.0.0.1 -P 3309 -u root -p -e "SELECT 'Shafiqah DB OK' as status;"

# SQL Server (Danish)
sqlcmd -S 127.0.0.1,1434 -U sa -P password -Q "SELECT 'Danish DB OK' as status;"

# PostgreSQL (Taufiq)
PGPASSWORD=password psql -h 127.0.0.1 -p 5434 -U postgres -d workshop -c "SELECT 'Taufiq DB OK' as status;"
```
```

---

#### Solution 5.3: Database Version Documentation
**Add to `CLAUDE.md` or create `DATABASE-VERSIONS.md`:**

```markdown
# Database Version Requirements

## MySQL Databases
- **Minimum Version:** 8.0.x
- **Recommended:** 8.0.35 or later
- **PHP Extension:** pdo_mysql (included in PHP 8.2+)

**Team Members:**
- Eilya: MySQL 8.0.x on Ubuntu/Windows
- Atiqah: MySQL 8.0.x on Ubuntu/Windows
- Shafiqah: MySQL 8.0.x on Ubuntu/Windows

**Check Version:**
```sql
SELECT VERSION();
```

## SQL Server Database
- **Minimum Version:** SQL Server 2017
- **Recommended:** SQL Server 2019 or 2022
- **PHP Extension:** pdo_sqlsrv, sqlsrv

**Team Member:**
- Danish: SQL Server 2019 on Windows

**Check Version:**
```sql
SELECT @@VERSION;
```

## PostgreSQL Database
- **Minimum Version:** PostgreSQL 12
- **Recommended:** PostgreSQL 14 or later
- **PHP Extension:** pdo_pgsql (included in PHP 8.2+)

**Team Member:**
- Taufiq: PostgreSQL 14.x on Ubuntu/Windows

**Check Version:**
```sql
SELECT version();
```

## PHP Extension Requirements

Ensure these are enabled in `php.ini`:

```ini
extension=pdo_mysql
extension=pdo_pgsql
extension=pdo_sqlsrv
extension=sqlsrv
```

**Check installed extensions:**
```bash
php -m | grep pdo
```
```

---

### Solution Set 6: Team Collaboration Solutions

#### Solution 6.1: Per-Team-Member Tunnel Configuration
**Create `SSH-TUNNELS-BY-MEMBER.md`:**

```markdown
# SSH Tunnel Setup by Team Member

## Taufiq (PostgreSQL Host - 10.18.26.156)

**Your database is LOCAL - no tunnel needed for your own DB**

**Tunnels you need to establish:**

```bash
# Connect to Eilya's MySQL
ssh -f -N -L 3307:127.0.0.1:3306 eilya@10.18.26.14

# Connect to Atiqah's MySQL
ssh -f -N -L 3308:127.0.0.1:3306 atiqah@10.18.26.84

# Connect to Shafiqah's MySQL
ssh -f -N -L 3309:127.0.0.1:3306 shafiqah@10.18.26.121

# Connect to Danish's SQL Server
ssh -f -N -L 1434:127.0.0.1:1433 'laptop-4k8hhere\user'@10.18.26.18
```

**Quick Start:**
```bash
./scripts/manage-tunnels.sh start eilya
./scripts/manage-tunnels.sh start atiqah
./scripts/manage-tunnels.sh start shafiqah
./scripts/manage-tunnels.sh start danish
```

---

## Eilya (MySQL Host - 10.18.26.14)

**Your database is LOCAL - no tunnel needed for your own DB**

**Tunnels you need to establish:**

```bash
# Connect to Atiqah's MySQL
ssh -f -N -L 3308:127.0.0.1:3306 atiqah@10.18.26.84

# Connect to Shafiqah's MySQL
ssh -f -N -L 3309:127.0.0.1:3306 shafiqah@10.18.26.121

# Connect to Danish's SQL Server
ssh -f -N -L 1434:127.0.0.1:1433 'laptop-4k8hhere\user'@10.18.26.18

# Connect to Taufiq's PostgreSQL
ssh -f -N -L 5434:127.0.0.1:5432 taufi@10.18.26.156
```

**Quick Start:**
```bash
./scripts/manage-tunnels.sh start atiqah
./scripts/manage-tunnels.sh start shafiqah
./scripts/manage-tunnels.sh start danish
./scripts/manage-tunnels.sh start taufiq
```

---

## Atiqah (MySQL Host - 10.18.26.84)

**Your database is LOCAL - no tunnel needed for your own DB**

**Tunnels you need to establish:**

```bash
# Connect to Eilya's MySQL
ssh -f -N -L 3307:127.0.0.1:3306 eilya@10.18.26.14

# Connect to Shafiqah's MySQL
ssh -f -N -L 3309:127.0.0.1:3306 shafiqah@10.18.26.121

# Connect to Danish's SQL Server
ssh -f -N -L 1434:127.0.0.1:1433 'laptop-4k8hhere\user'@10.18.26.18

# Connect to Taufiq's PostgreSQL
ssh -f -N -L 5434:127.0.0.1:5432 taufi@10.18.26.156
```

**Quick Start:**
```bash
./scripts/manage-tunnels.sh start eilya
./scripts/manage-tunnels.sh start shafiqah
./scripts/manage-tunnels.sh start danish
./scripts/manage-tunnels.sh start taufiq
```

---

## Shafiqah (MySQL Host - 10.18.26.121)

**Your database is LOCAL - no tunnel needed for your own DB**

**Tunnels you need to establish:**

```bash
# Connect to Eilya's MySQL
ssh -f -N -L 3307:127.0.0.1:3306 eilya@10.18.26.14

# Connect to Atiqah's MySQL
ssh -f -N -L 3308:127.0.0.1:3306 atiqah@10.18.26.84

# Connect to Danish's SQL Server
ssh -f -N -L 1434:127.0.0.1:1433 'laptop-4k8hhere\user'@10.18.26.18

# Connect to Taufiq's PostgreSQL
ssh -f -N -L 5434:127.0.0.1:5432 taufi@10.18.26.156
```

**Quick Start:**
```bash
./scripts/manage-tunnels.sh start eilya
./scripts/manage-tunnels.sh start atiqah
./scripts/manage-tunnels.sh start danish
./scripts/manage-tunnels.sh start taufiq
```

---

## Danish (SQL Server Host - 10.18.26.18)

**Your database is LOCAL - no tunnel needed for your own DB**

**Tunnels you need to establish:**

```bash
# Connect to Eilya's MySQL
ssh -f -N -L 3307:127.0.0.1:3306 eilya@10.18.26.14

# Connect to Atiqah's MySQL
ssh -f -N -L 3308:127.0.0.1:3306 atiqah@10.18.26.84

# Connect to Shafiqah's MySQL
ssh -f -N -L 3309:127.0.0.1:3306 shafiqah@10.18.26.121

# Connect to Taufiq's PostgreSQL
ssh -f -N -L 5434:127.0.0.1:5432 taufi@10.18.26.156
```

**Quick Start:**
```bash
./scripts/manage-tunnels.sh start eilya
./scripts/manage-tunnels.sh start atiqah
./scripts/manage-tunnels.sh start shafiqah
./scripts/manage-tunnels.sh start taufiq
```

---

## Environment Variables (.env file)

**IMPORTANT:** Everyone uses the same `.env` configuration:

```env
# Eilya's Database (via SSH tunnel)
DB1_HOST=127.0.0.1
DB1_PORT=3307
DB1_DATABASE=workshop2
DB1_USERNAME=root
DB1_PASSWORD=Efpbts1370624!

# Atiqah's Database (via SSH tunnel)
DB2_HOST=127.0.0.1
DB2_PORT=3308
DB2_DATABASE=workshop
DB2_USERNAME=root
DB2_PASSWORD=Atiqah76_

# Shafiqah's Database (via SSH tunnel)
DB3_HOST=127.0.0.1
DB3_PORT=3309
DB3_DATABASE=workshop
DB3_USERNAME=root
DB3_PASSWORD=password

# Danish's Database (via SSH tunnel)
DB4_HOST=127.0.0.1
DB4_PORT=1434
DB4_DATABASE=workshop
DB4_USERNAME=sa
DB4_PASSWORD=danimran74

# Taufiq's Database (via SSH tunnel)
DB5_HOST=127.0.0.1
DB5_PORT=5434
DB5_DATABASE=workshop
DB5_USERNAME=postgres
DB5_PASSWORD=password
```

**Note:** Host is always `127.0.0.1` because SSH tunnel makes remote databases appear local!
```

---

#### Solution 6.2: Troubleshooting Workflow Documentation
**Create `TROUBLESHOOTING-WORKFLOW.md`:**

```markdown
# SSH Tunnel Troubleshooting Workflow

When database connections fail, follow this systematic approach:

## Step 1: Verify Network Connectivity

```bash
# Test if remote host is reachable
ping 10.18.26.14  # Replace with problematic host

# Expected: Reply from 10.18.26.14: bytes=32 time<1ms TTL=64
# If failed: Check VPN connection or network connectivity
```

## Step 2: Test SSH Access

```bash
# Test SSH connection (without tunnel)
ssh eilya@10.18.26.14 echo "Connection successful"

# Expected: "Connection successful"
# If failed: Check SSH credentials, key authentication, or SSH service on remote host
```

## Step 3: Verify Remote Database Service

```bash
# Check if database service is running on remote host
ssh eilya@10.18.26.14 "systemctl status mysql"

# Or
ssh eilya@10.18.26.14 "netstat -tuln | grep :3306"

# Expected: Service is active/running and port is listening
# If failed: Ask team member to start their database service
```

## Step 4: Check Local Port Availability

```bash
# Check if local port is already in use
# Linux/Mac/Git Bash:
netstat -tuln | grep :3307

# PowerShell:
Get-NetTCPConnection -LocalPort 3307

# Expected: Empty (port is free) or only one SSH process
# If failed: Kill the process using the port (see Solution 4.1)
```

## Step 5: Start SSH Tunnel

```bash
# Start tunnel manually to see errors
ssh -N -L 3307:127.0.0.1:3306 eilya@10.18.26.14

# Expected: Command hangs (tunnel is running)
# If failed: Read error message carefully

# Common errors:
# - "bind: Address already in use" → Port conflict (Step 4)
# - "Permission denied" → SSH authentication failed (Step 2)
# - "Connection refused" → SSH service not running (Step 2)
# - "No route to host" → Network issue (Step 1)
```

## Step 6: Verify Tunnel is Working

```bash
# Test port connectivity
# Linux/Mac/Git Bash:
nc -zv 127.0.0.1 3307

# PowerShell:
Test-NetConnection -ComputerName 127.0.0.1 -Port 3307

# Expected: "succeeded" or "TcpTestSucceeded: True"
# If failed: Tunnel is not established properly (repeat Step 5)
```

## Step 7: Test Database Connectivity Through Tunnel

```bash
# MySQL (ports 3307, 3308, 3309)
mysql -h 127.0.0.1 -P 3307 -u root -p -e "SELECT 'Connection OK';"

# SQL Server (port 1434)
sqlcmd -S 127.0.0.1,1434 -U sa -P password -Q "SELECT 'Connection OK';"

# PostgreSQL (port 5434)
PGPASSWORD=password psql -h 127.0.0.1 -p 5434 -U postgres -d workshop -c "SELECT 'Connection OK';"

# Expected: "Connection OK"
# If failed: Database credentials or service issue
```

## Step 8: Test Laravel Database Connection

```bash
php artisan tinker

# In tinker:
DB::connection('eilya')->select('SELECT 1 as test');

# Expected: Array with result
# If failed: Check .env configuration
```

## Quick Diagnostic Script

Run this to get a full diagnostic report:

```bash
#!/bin/bash
echo "=== SSH Tunnel Diagnostics ==="
echo ""
echo "1. Network Connectivity:"
ping -c 3 10.18.26.14 2>&1 | grep "bytes from"
echo ""
echo "2. SSH Access:"
ssh -o ConnectTimeout=5 eilya@10.18.26.14 echo "SSH OK" 2>&1
echo ""
echo "3. Local Port Status:"
netstat -tuln | grep -E ":(3307|3308|3309|1434|5434)"
echo ""
echo "4. SSH Tunnel Processes:"
ps aux | grep "ssh.*-L.*:" | grep -v grep
echo ""
echo "5. Database Connectivity:"
nc -zv 127.0.0.1 3307 2>&1 | tail -1
nc -zv 127.0.0.1 3308 2>&1 | tail -1
nc -zv 127.0.0.1 3309 2>&1 | tail -1
nc -zv 127.0.0.1 1434 2>&1 | tail -1
nc -zv 127.0.0.1 5434 2>&1 | tail -1
echo ""
echo "=== End Diagnostics ==="
```

## Common Error Messages and Solutions

| Error Message | Cause | Solution |
|--------------|-------|----------|
| `bind: Address already in use` | Port conflict | Kill process using port (Solution 4.1) |
| `Permission denied (publickey,password)` | SSH auth failed | Check SSH keys or password |
| `Connection refused` | SSH service down | Ask team member to start SSH service |
| `No route to host` | Network issue | Check VPN/network connectivity |
| `Connection timed out` | Firewall blocking | Check firewall rules (Solution 4.2) |
| `channel_setup_fwd_listener_tcpip: cannot listen` | Port already bound | Stop existing tunnel first |
| `SQLSTATE[HY000] [2002] Connection refused` | Database service down | Check remote database service status |
| `Access denied for user` | Wrong DB password | Check .env credentials |

## Getting Help from Team

When asking for help, provide this information:

```bash
# Run and share output:
./scripts/manage-tunnels.sh status
php artisan db:clear-status-cache
cat .env | grep -E "^DB[0-9]"
```

Include:
1. Which database connection is failing (eilya, atiqah, shafiqah, danish, taufiq)
2. Exact error message from Laravel/Artisan
3. Output from diagnostic script above
4. Your operating system and terminal (Git Bash, PowerShell, CMD, etc.)
```

---

#### Solution 6.3: Team Status Monitoring System
**Create `scripts/team-db-status.php` (Laravel Artisan Command):**

```php
<?php
// app/Console/Commands/TeamDatabaseStatus.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TeamDatabaseStatus extends Command
{
    protected $signature = 'db:team-status';
    protected $description = 'Show connection status for all team databases';

    private $connections = [
        'eilya' => ['name' => 'Eilya (MySQL)', 'module' => 'Stray Reporting', 'port' => 3307],
        'atiqah' => ['name' => 'Atiqah (MySQL)', 'module' => 'Shelter Management', 'port' => 3308],
        'shafiqah' => ['name' => 'Shafiqah (MySQL)', 'module' => 'Animal Management', 'port' => 3309],
        'danish' => ['name' => 'Danish (SQL Server)', 'module' => 'Booking & Adoption', 'port' => 1434],
        'taufiq' => ['name' => 'Taufiq (PostgreSQL)', 'module' => 'User Management', 'port' => 5434],
    ];

    public function handle()
    {
        $this->info('Team Database Status Dashboard');
        $this->info('===============================');
        $this->newLine();

        $statuses = [];

        foreach ($this->connections as $connection => $info) {
            $status = $this->checkConnection($connection);
            $statuses[$connection] = array_merge($info, $status);
        }

        $this->displayTable($statuses);
        $this->newLine();
        $this->displaySummary($statuses);
    }

    private function checkConnection(string $connection): array
    {
        $startTime = microtime(true);

        try {
            DB::connection($connection)->getPdo();
            $latency = round((microtime(true) - $startTime) * 1000, 2);

            // Get database version
            $version = $this->getDatabaseVersion($connection);

            return [
                'status' => '✓ Online',
                'latency' => $latency . ' ms',
                'version' => $version,
                'is_online' => true,
            ];
        } catch (\Exception $e) {
            return [
                'status' => '✗ Offline',
                'latency' => 'N/A',
                'version' => 'N/A',
                'is_online' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getDatabaseVersion(string $connection): string
    {
        try {
            $result = DB::connection($connection)->select('SELECT VERSION() as version');
            $version = $result[0]->version ?? 'Unknown';

            // Truncate long version strings
            return strlen($version) > 30 ? substr($version, 0, 30) . '...' : $version;
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function displayTable(array $statuses): void
    {
        $headers = ['Owner', 'Module', 'Port', 'Status', 'Latency', 'Version'];
        $rows = [];

        foreach ($statuses as $connection => $info) {
            $rows[] = [
                $info['name'],
                $info['module'],
                $info['port'],
                $info['is_online']
                    ? "<fg=green>{$info['status']}</>"
                    : "<fg=red>{$info['status']}</>",
                $info['latency'],
                $info['version'],
            ];
        }

        $this->table($headers, $rows);
    }

    private function displaySummary(array $statuses): void
    {
        $online = collect($statuses)->where('is_online', true)->count();
        $offline = collect($statuses)->where('is_online', false)->count();

        if ($offline === 0) {
            $this->info("✓ All databases online ({$online}/{$online})");
        } else {
            $this->warn("⚠ {$offline} database(s) offline, {$online} online");

            $offlineConnections = collect($statuses)
                ->filter(fn($s) => !$s['is_online'])
                ->keys()
                ->toArray();

            $this->error("Offline: " . implode(', ', $offlineConnections));
            $this->newLine();
            $this->comment("Troubleshooting steps:");
            $this->line("1. Check SSH tunnels: ./scripts/manage-tunnels.sh status");
            $this->line("2. Restart tunnels: ./scripts/manage-tunnels.sh restart");
            $this->line("3. See TROUBLESHOOTING-WORKFLOW.md for detailed steps");
        }
    }
}
```

**Register command in `app/Console/Kernel.php`:**

```php
protected $commands = [
    Commands\TeamDatabaseStatus::class,
];
```

**Usage:**

```bash
php artisan db:team-status
```

**Output:**

```
Team Database Status Dashboard
===============================

┌─────────────────────┬────────────────────┬──────┬─────────┬──────────┬──────────────────┐
│ Owner               │ Module             │ Port │ Status  │ Latency  │ Version          │
├─────────────────────┼────────────────────┼──────┼─────────┼──────────┼──────────────────┤
│ Eilya (MySQL)       │ Stray Reporting    │ 3307 │ ✓ Online│ 12.34 ms │ 8.0.35          │
│ Atiqah (MySQL)      │ Shelter Management │ 3308 │ ✓ Online│ 15.67 ms │ 8.0.35          │
│ Shafiqah (MySQL)    │ Animal Management  │ 3309 │ ✗ Offline│ N/A     │ N/A             │
│ Danish (SQL Server) │ Booking & Adoption │ 1434 │ ✓ Online│ 23.45 ms │ Microsoft SQL... │
│ Taufiq (PostgreSQL) │ User Management    │ 5434 │ ✓ Online│ 8.90 ms  │ PostgreSQL 14.5  │
└─────────────────────┴────────────────────┴──────┴─────────┴──────────┴──────────────────┘

⚠ 1 database(s) offline, 4 online
Offline: shafiqah

Troubleshooting steps:
1. Check SSH tunnels: ./scripts/manage-tunnels.sh status
2. Restart tunnels: ./scripts/manage-tunnels.sh restart
3. See TROUBLESHOOTING-WORKFLOW.md for detailed steps
```

---

### Solution Set 7: Cross-Platform Compatibility

#### Solution 7.1: Platform-Specific Tunnel Scripts

**Create `scripts/manage-tunnels.ps1` (PowerShell version):**

```powershell
# Workshop 2 - SSH Tunnel Management Script (PowerShell)
# Manages all 5 database SSH tunnels

param(
    [Parameter(Mandatory=$true, Position=0)]
    [ValidateSet('start', 'stop', 'restart', 'status')]
    [string]$Action,

    [Parameter(Mandatory=$false, Position=1)]
    [ValidateSet('eilya', 'atiqah', 'shafiqah', 'danish', 'taufiq', '')]
    [string]$TunnelName = ''
)

$PidDir = "$PSScriptRoot\..\\.ssh-tunnels"
New-Item -ItemType Directory -Force -Path $PidDir | Out-Null

# Define tunnels
$Tunnels = @{
    'eilya' = @{
        LocalPort = 3307
        RemoteHost = '127.0.0.1'
        RemotePort = 3306
        SshTarget = 'eilya@10.18.26.14'
    }
    'atiqah' = @{
        LocalPort = 3308
        RemoteHost = '127.0.0.1'
        RemotePort = 3306
        SshTarget = 'atiqah@10.18.26.84'
    }
    'shafiqah' = @{
        LocalPort = 3309
        RemoteHost = '127.0.0.1'
        RemotePort = 3306
        SshTarget = 'shafiqah@10.18.26.121'
    }
    'danish' = @{
        LocalPort = 1434
        RemoteHost = '127.0.0.1'
        RemotePort = 1433
        SshTarget = 'laptop-4k8hhere\user@10.18.26.18'
    }
    'taufiq' = @{
        LocalPort = 5434
        RemoteHost = '127.0.0.1'
        RemotePort = 5432
        SshTarget = 'taufi@10.18.26.156'
    }
}

function Start-Tunnel {
    param($Name, $Config)

    Write-Host "Starting tunnel: $Name (port $($Config.LocalPort))..." -ForegroundColor Yellow

    $PidFile = Join-Path $PidDir "$Name.pid"

    # Check if already running
    if (Test-Path $PidFile) {
        $Pid = Get-Content $PidFile
        if (Get-Process -Id $Pid -ErrorAction SilentlyContinue) {
            Write-Host "✓ Tunnel already running (PID: $Pid)" -ForegroundColor Green
            return
        }
        Remove-Item $PidFile
    }

    # Start tunnel
    $ProcessInfo = Start-Process -FilePath "ssh" `
        -ArgumentList "-N", "-L", "$($Config.LocalPort):$($Config.RemoteHost):$($Config.RemotePort)", `
                     "-o", "ServerAliveInterval=60", `
                     "-o", "ServerAliveCountMax=3", `
                     "-o", "ExitOnForwardFailure=yes", `
                     "-o", "StrictHostKeyChecking=no", `
                     $Config.SshTarget `
        -PassThru -WindowStyle Hidden

    if ($ProcessInfo) {
        $ProcessInfo.Id | Out-File -FilePath $PidFile
        Write-Host "✓ Tunnel started successfully (PID: $($ProcessInfo.Id))" -ForegroundColor Green
    } else {
        Write-Host "✗ Failed to start tunnel" -ForegroundColor Red
    }
}

function Stop-Tunnel {
    param($Name)

    Write-Host "Stopping tunnel: $Name..." -ForegroundColor Yellow

    $PidFile = Join-Path $PidDir "$Name.pid"

    if (Test-Path $PidFile) {
        $Pid = Get-Content $PidFile
        if (Get-Process -Id $Pid -ErrorAction SilentlyContinue) {
            Stop-Process -Id $Pid -Force
            Remove-Item $PidFile
            Write-Host "✓ Tunnel stopped" -ForegroundColor Green
        } else {
            Remove-Item $PidFile
            Write-Host "⚠ Tunnel was not running" -ForegroundColor Yellow
        }
    } else {
        Write-Host "⚠ No PID file found" -ForegroundColor Yellow
    }
}

function Get-TunnelStatus {
    param($Name, $Config)

    $PidFile = Join-Path $PidDir "$Name.pid"

    if (Test-Path $PidFile) {
        $Pid = Get-Content $PidFile
        if (Get-Process -Id $Pid -ErrorAction SilentlyContinue) {
            Write-Host "✓ $Name (port $($Config.LocalPort)) - RUNNING (PID: $Pid)" -ForegroundColor Green

            # Test port connectivity
            $PortTest = Test-NetConnection -ComputerName 127.0.0.1 -Port $Config.LocalPort -InformationLevel Quiet -WarningAction SilentlyContinue
            if ($PortTest) {
                Write-Host "  ✓ Port is accepting connections" -ForegroundColor Green
            } else {
                Write-Host "  ✗ Port is not responding" -ForegroundColor Red
            }
        } else {
            Write-Host "✗ $Name (port $($Config.LocalPort)) - STOPPED (stale PID)" -ForegroundColor Red
            Remove-Item $PidFile
        }
    } else {
        Write-Host "✗ $Name (port $($Config.LocalPort)) - STOPPED" -ForegroundColor Red
    }
    Write-Host ""
}

# Main logic
switch ($Action) {
    'start' {
        if ($TunnelName) {
            if ($Tunnels.ContainsKey($TunnelName)) {
                Start-Tunnel $TunnelName $Tunnels[$TunnelName]
            } else {
                Write-Host "Unknown tunnel: $TunnelName" -ForegroundColor Red
                exit 1
            }
        } else {
            Write-Host "Starting all tunnels..." -ForegroundColor Cyan
            foreach ($Tunnel in $Tunnels.GetEnumerator()) {
                Start-Tunnel $Tunnel.Key $Tunnel.Value
                Write-Host ""
            }
        }
    }
    'stop' {
        if ($TunnelName) {
            Stop-Tunnel $TunnelName
        } else {
            Write-Host "Stopping all tunnels..." -ForegroundColor Cyan
            foreach ($Tunnel in $Tunnels.GetEnumerator()) {
                Stop-Tunnel $Tunnel.Key
            }
        }
    }
    'restart' {
        if ($TunnelName) {
            Stop-Tunnel $TunnelName
            Start-Sleep -Seconds 1
            Start-Tunnel $TunnelName $Tunnels[$TunnelName]
        } else {
            Write-Host "Restarting all tunnels..." -ForegroundColor Cyan
            foreach ($Tunnel in $Tunnels.GetEnumerator()) {
                Stop-Tunnel $Tunnel.Key
            }
            Start-Sleep -Seconds 2
            foreach ($Tunnel in $Tunnels.GetEnumerator()) {
                Start-Tunnel $Tunnel.Key $Tunnel.Value
                Write-Host ""
            }
        }
    }
    'status' {
        Write-Host "Tunnel Status:" -ForegroundColor Cyan
        Write-Host "==============" -ForegroundColor Cyan
        foreach ($Tunnel in $Tunnels.GetEnumerator()) {
            Get-TunnelStatus $Tunnel.Key $Tunnel.Value
        }
    }
}
```

**Usage:**

```powershell
# Start all tunnels
.\scripts\manage-tunnels.ps1 start

# Start specific tunnel
.\scripts\manage-tunnels.ps1 start eilya

# Check status
.\scripts\manage-tunnels.ps1 status

# Stop all tunnels
.\scripts\manage-tunnels.ps1 stop

# Restart
.\scripts\manage-tunnels.ps1 restart
```

---

#### Solution 7.2: Cross-Platform SSH Config
**Document platform-specific escape requirements:**

```markdown
# Platform-Specific SSH Configuration

## Git Bash (Windows)

```bash
# Works with double backslash
ssh -N -L 1434:127.0.0.1:1433 laptop-4k8hhere\\user@10.18.26.18

# Or use SSH config
Host danish-db
    HostName 10.18.26.18
    User laptop-4k8hhere\user  # Single backslash in config file
```

## PowerShell (Windows)

```powershell
# Use quotes around username
ssh -N -L 1434:127.0.0.1:1433 'laptop-4k8hhere\user'@10.18.26.18

# Or escape with backtick
ssh -N -L 1434:127.0.0.1:1433 laptop-4k8hhere`\user@10.18.26.18
```

## CMD (Windows) - Not Recommended

Use PowerShell or Git Bash instead. CMD has poor SSH support.

## Linux / WSL / Mac

```bash
# Works with double backslash
ssh -N -L 1434:127.0.0.1:1433 laptop-4k8hhere\\user@10.18.26.18
```
```

---

#### Solution 7.3: Line Ending Configuration
**Add to `.gitattributes`:**

```gitattributes
# Ensure shell scripts have LF line endings
*.sh text eol=lf
*.bash text eol=lf

# PowerShell scripts should have CRLF on Windows
*.ps1 text eol=crlf

# Markdown files
*.md text eol=lf

# SSH config files
**/ssh/config text eol=lf
```

**Add `.editorconfig`:**

```editorconfig
root = true

[*]
charset = utf-8
end_of_line = lf
insert_final_newline = true
trim_trailing_whitespace = true

[*.sh]
end_of_line = lf

[*.ps1]
end_of_line = crlf

[*.bat]
end_of_line = crlf
```

---

### Solution Set 8: Resource Management

#### Solution 8.1: Cleanup Script
**Create `scripts/cleanup-tunnels.sh`:**

```bash
#!/bin/bash

echo "SSH Tunnel Cleanup Utility"
echo "==========================="
echo ""

# Kill all SSH tunnels
echo "Finding SSH tunnel processes..."
TUNNEL_PIDS=$(ps aux | grep "ssh.*-L.*127.0.0.1" | grep -v grep | awk '{print $2}')

if [ -z "$TUNNEL_PIDS" ]; then
    echo "No SSH tunnels found."
else
    echo "Found SSH tunnel processes:"
    ps aux | grep "ssh.*-L.*127.0.0.1" | grep -v grep | awk '{print "  PID " $2 ": " $11 " " $12 " " $13}'
    echo ""

    read -p "Kill all these processes? (y/n) " -n 1 -r
    echo

    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "$TUNNEL_PIDS" | xargs kill -9
        echo "✓ All tunnels killed"
    else
        echo "Aborted"
    fi
fi

# Clean up PID files
PID_DIR=".ssh-tunnels"
if [ -d "$PID_DIR" ]; then
    echo ""
    echo "Cleaning up PID files..."
    rm -rf "$PID_DIR"/*.pid
    echo "✓ PID files removed"
fi

# Check for port conflicts
echo ""
echo "Checking for remaining port bindings..."
for PORT in 3307 3308 3309 1434 5434; do
    BINDING=$(netstat -tuln 2>/dev/null | grep ":$PORT " || ss -tuln 2>/dev/null | grep ":$PORT ")
    if [ ! -z "$BINDING" ]; then
        echo "⚠ Port $PORT is still bound"
    else
        echo "✓ Port $PORT is free"
    fi
done

echo ""
echo "Cleanup complete!"
```

**PowerShell version (`scripts/cleanup-tunnels.ps1`):**

```powershell
Write-Host "SSH Tunnel Cleanup Utility" -ForegroundColor Cyan
Write-Host "===========================" -ForegroundColor Cyan
Write-Host ""

# Find SSH tunnel processes
Write-Host "Finding SSH tunnel processes..." -ForegroundColor Yellow
$TunnelProcesses = Get-Process | Where-Object { $_.ProcessName -eq 'ssh' -and $_.CommandLine -like '*-L*127.0.0.1*' }

if ($TunnelProcesses.Count -eq 0) {
    Write-Host "No SSH tunnels found." -ForegroundColor Green
} else {
    Write-Host "Found SSH tunnel processes:" -ForegroundColor Yellow
    $TunnelProcesses | ForEach-Object {
        Write-Host "  PID $($_.Id): $($_.ProcessName)" -ForegroundColor White
    }
    Write-Host ""

    $Confirm = Read-Host "Kill all these processes? (y/n)"

    if ($Confirm -eq 'y' -or $Confirm -eq 'Y') {
        $TunnelProcesses | Stop-Process -Force
        Write-Host "✓ All tunnels killed" -ForegroundColor Green
    } else {
        Write-Host "Aborted" -ForegroundColor Yellow
    }
}

# Clean up PID files
$PidDir = Join-Path $PSScriptRoot "..\.ssh-tunnels"
if (Test-Path $PidDir) {
    Write-Host ""
    Write-Host "Cleaning up PID files..." -ForegroundColor Yellow
    Remove-Item -Path (Join-Path $PidDir "*.pid") -Force -ErrorAction SilentlyContinue
    Write-Host "✓ PID files removed" -ForegroundColor Green
}

# Check for port conflicts
Write-Host ""
Write-Host "Checking for remaining port bindings..." -ForegroundColor Yellow
@(3307, 3308, 3309, 1434, 5434) | ForEach-Object {
    $Port = $_
    $Binding = Get-NetTCPConnection -LocalPort $Port -ErrorAction SilentlyContinue
    if ($Binding) {
        Write-Host "⚠ Port $Port is still bound" -ForegroundColor Yellow
    } else {
        Write-Host "✓ Port $Port is free" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "Cleanup complete!" -ForegroundColor Cyan
```

---

## Implementation Checklist

- [ ] **Security:** Implement SSH key authentication (Solution 2.1)
- [ ] **Security:** Remove passwords from repository (Solution 2.2)
- [ ] **Config:** Create SSH config file for each team member (Solution 1.3)
- [ ] **Config:** Fix naming inconsistency (shafiqah vs piqa) (Solution 1.1)
- [ ] **Config:** Document correct Windows path escaping (Solution 1.2)
- [ ] **Scripts:** Create tunnel management script (Solution 3.4)
- [ ] **Scripts:** Create connection validation script (Solution 3.2)
- [ ] **Scripts:** Create cleanup script (Solution 8.1)
- [ ] **Scripts:** Create platform-specific versions (PowerShell) (Solution 7.1)
- [ ] **Docs:** Create per-member tunnel guide (Solution 6.1)
- [ ] **Docs:** Create troubleshooting workflow (Solution 6.2)
- [ ] **Docs:** Create database client installation guide (Solution 5.2)
- [ ] **Docs:** Document database versions (Solution 5.3)
- [ ] **Laravel:** Create team status Artisan command (Solution 6.3)
- [ ] **Network:** Configure firewall rules (Solution 4.2)
- [ ] **Network:** Document VPN/network requirements (Solution 4.3)
- [ ] **Network:** Add SSH keep-alive configuration (Solution 4.4)
- [ ] **Testing:** Test all tunnels on each team member's machine
- [ ] **Testing:** Verify cross-platform compatibility
- [ ] **Training:** Team walkthrough of new scripts and documentation

---

## Priority Actions (Start Here)

1. **IMMEDIATE (Security Risk):**
   - [ ] Remove passwords from `ssh-tunnels.md`
   - [ ] Add `ssh-tunnels.md` to `.gitignore` or delete it
   - [ ] Implement SSH key authentication

2. **HIGH PRIORITY (Usability):**
   - [ ] Create tunnel management script (`manage-tunnels.sh`)
   - [ ] Create per-member tunnel guide
   - [ ] Fix naming inconsistency (piqa → shafiqah)

3. **MEDIUM PRIORITY (Reliability):**
   - [ ] Add SSH keep-alive configuration
   - [ ] Create connection validation script
   - [ ] Create troubleshooting workflow documentation

4. **LOW PRIORITY (Nice to Have):**
   - [ ] Create Laravel team status command
   - [ ] Create cleanup script
   - [ ] Document database versions
