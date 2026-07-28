# DrupalJira

Internal task tracker built on Drupal 11.

---

## Prerequisites

The following must be installed on your machine:
- **Docker** / Docker Desktop
- **DDEV** (version 1.23.0 or higher)

---

## Setup

Run the following commands in your terminal to fully set up the project:

```bash
# 1. Clone the repository
git clone <repository-url> drupaljira
cd drupaljira

# 2. Start DDEV environment
ddev start

# 3. Install dependencies via Composer
ddev composer install

# 4. Install a clean Drupal site with the minimal profile
ddev drush site-install minimal --site-name="DrupalJira" --account-name=admin --account-pass=admin -y

# 5. Open the site
ddev launch
```

---

## Xdebug and PHPStorm Setup

### Enabling Debugging

Xdebug is **disabled** by default to preserve performance. To enable it:

```bash
ddev xdebug
```

To disable it:

```bash
ddev xdebug off
```

> After disabling Xdebug, site requests and Drush commands run without any delays — no connection to a debugger is attempted.

---

### PHPStorm Configuration

#### 1. Create a Debug Configuration

1. **Run** → **Edit Configurations...**
2. Click **+** → **PHP Remote Debug**
3. Fill in the fields:
   - **Name**: `DDEV DrupalJira`
   - **Server**: create a new server (click `...`):
     - **Name**: `drupaljira`
     - **Host**: `drupaljira.ddev.site`
     - **Port**: `443`
     - **Debugger**: `Xdebug`
     - **IDE key**: `PHPSTORM`
   - **Path mapping** (critical):
     - **Local path**: `/home/<your-user>/drupaljira` (project root on host)
     - **Remote path**: `/var/www/html`
4. Click **OK**

#### 2. Start Listening for Debug Connections

1. **Run** → **Start Listening for PHP Debug Connections** (phone icon in the toolbar)
2. Verify the Xdebug port is **9003** (**PHP** → **Debug** → **Xdebug**, port `9003`)

#### 3. Verify PHP Settings

1. **File** → **Settings** → **PHP** → **Debug**
2. Ensure:
   - **Xdebug** → port: `9003`
   - **Can accept external connections**: enabled

---

### Verification

#### Web Requests

1. Open any Drupal file in PHPStorm, e.g. `web/core/lib/Drupal/Core/Controller/ControllerBase.php`
2. Set a breakpoint on a line inside a method
3. Enable debugging: `ddev xdebug`
4. Open `https://drupaljira.ddev.site` in your browser
5. Execution will stop at the breakpoint. In PHPStorm you have:
   - Call stack
   - Scope variable values
   - Watch/Evaluate to modify variables and affect execution after Resume

#### Drush (CLI)

```bash
ddev xdebug
ddev drush status
```

Execution will stop at breakpoints in Drush/Drupal code.

---

### Troubleshooting

| Problem | Solution |
|---------|----------|
| Breakpoint not hit | Check path mapping: Local path = project root, Remote path = `/var/www/html` |
| PHPStorm not accepting connections | Ensure **Start Listening** is active (phone icon is green) |
| `Cannot find local file` | Path mapping must match the actual project location on your host |
| Slow requests when not debugging | Disable Xdebug: `ddev xdebug off` |

---

### Onboarding a New Developer

1. Install **Docker** and **DDEV**
2. Clone the repository and run `ddev start`
3. Verify the file `.ddev/php/xdebug.ini` exists (already in the repo)
4. In PHPStorm:
   - **File** → **Settings** → **PHP** → **Debug** → **Xdebug**: port `9003`
   - **Run** → **Edit Configurations** → **PHP Remote Debug**:
     - Server: `drupaljira.ddev.site:443`, debugger `Xdebug`, IDE key `PHPSTORM`
     - Path mapping: `<project-root>` → `/var/www/html`
   - **Run** → **Start Listening for PHP Debug Connections**
5. Enable debugging: `ddev xdebug`
6. Open the site in a browser — the breakpoint should be hit
