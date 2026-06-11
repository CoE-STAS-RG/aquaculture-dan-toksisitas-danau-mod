# AquaSmart Monitor — Aquaculture & Water Toxicity Monitoring System

> Full-stack Laravel application for real-time water quality monitoring in aquaculture ponds and lakes. Built by **CoE STAS-RG**.

---

## Features

- **Real-time sensor monitoring** — pH, Temperature, DO, Turbidity, EC, TDS, TDS EC Mod, ORP
- **Risk assessment** — automatic classification (Optimal / Warning / Critical) per reading
- **Multi-device management** — register and manage multiple monitoring stations per user
- **Fish feeding tracker** — log feeding data and visualize fish growth over time
- **Trend charts** — historical water quality visualization per device
- **Bilingual UI** — full English and Bahasa Indonesia support (212 translation keys)
- **Role-based access** — Admin and User roles with separate dashboards
- **REST API** — Sanctum-authenticated endpoints for IoT device integration
- **MQTT integration** — receive sensor data from hardware buoys via MQTT broker
- **Real-time push** — Laravel Reverb WebSockets for live dashboard updates
- **Product marketplace** — cart and product listing for aquaculture supplies

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Blade, Alpine.js, Tailwind CSS, Chart.js |
| Auth | Laravel Sanctum (API tokens + session) |
| WebSockets | Laravel Reverb |
| IoT Protocol | MQTT (`php-mqtt/client`) |
| Testing | Pest |
| Database | MySQL / MariaDB |
| Queue | Laravel Queue (database driver) |

---

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18 & npm
- MySQL or MariaDB
- An MQTT broker (e.g. Mosquitto) for live sensor ingestion

---

## Installation

```bash
# 1. Clone the repository
git clone <repo-url>
cd aquaculture-dan-toksisitas-danau-mod

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Configure environment
cp .env.example .env
php artisan key:generate

# 5. Set database credentials in .env
# DB_DATABASE=aquaculture
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Run migrations and seeders
php artisan migrate
php artisan db:seed

# 7. Create storage symlink (for profile photos)
php artisan storage:link

# 8. Start development server
composer run dev
```

`composer run dev` starts all services concurrently: HTTP server (port 8000), queue worker, log watcher, and Vite dev server.

---

## Environment Variables

Key variables to configure in `.env`:

```env
APP_NAME="AquaSmart Monitor"
APP_LOCALE=en                  # Default locale: en or id
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_DATABASE=aquaculture

# MQTT broker
MQTT_HOST=localhost
MQTT_PORT=1883

# Reverb WebSockets
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=

# Storage (profile photos)
FILESYSTEM_DISK=local
```

---

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/           # Web controllers
│   │   │   └── Api/               # REST API controllers
│   │   └── Middleware/
│   │       └── SetLocale.php      # Applies session locale on every request
│   ├── Models/
│   │   ├── User.php
│   │   ├── Device.php
│   │   ├── SensorReading.php
│   │   ├── WaterQualityReading.php
│   │   ├── FishFeeding.php
│   │   ├── Product.php
│   │   └── Cart.php
│   └── Policies/                  # Authorization policies
├── resources/
│   ├── views/                     # Blade templates
│   │   ├── admin/                 # Admin dashboard & user management
│   │   ├── devices/               # Device index, create, show
│   │   ├── fish-feedings/         # Fish feeding tracker
│   │   ├── profile/               # Profile settings
│   │   ├── user/                  # User dashboard
│   │   ├── layouts/               # App, admin, guest, sidebar layouts
│   │   └── components/            # Reusable Blade components
│   ├── lang/
│   │   ├── en/
│   │   │   ├── navigation.php     # Navbar & account menu strings
│   │   │   ├── ui.php             # All page-level UI labels (212 keys)
│   │   │   └── messages.php       # Flash messages & status strings
│   │   └── id/
│   │       ├── navigation.php
│   │       ├── ui.php
│   │       └── messages.php
│   ├── js/                        # app.js, bootstrap.js, Echo setup
│   └── css/                       # app.css (Tailwind)
├── routes/
│   ├── web.php                    # Web routes
│   ├── api.php                    # REST API routes
│   └── auth.php                   # Breeze auth routes
├── database/
│   ├── migrations/
│   └── seeders/
└── tests/                         # Pest test suites
```

---

## API Endpoints

All API routes are prefixed with `/api` and require a Sanctum Bearer token unless noted.

### Authentication
```
POST   /api/login                              # Login, returns token
POST   /api/register                           # Register new user
```

### Devices
```
GET    /api/devices                            # List user's devices
POST   /api/devices                            # Create device
GET    /api/devices/{id}                       # Show device
PUT    /api/devices/{id}                       # Update device
DELETE /api/devices/{id}                       # Delete device
```

### Sensor Data
```
GET    /api/sensor-data                        # List sensor readings
POST   /api/sensor-data                        # Store reading (IoT device)
GET    /api/sensor-data/device/{deviceCode}    # Readings by device code
```

### Fish Feeding
```
GET    /api/fish-feedings                      # List feeding logs
POST   /api/fish-feedings                      # Log a feeding
```

**Sensor data payload example:**
```json
{
  "device_code": "BUOY_001",
  "temperature": 28.5,
  "ph": 7.2,
  "do": 6.1,
  "turbidity": 12.0,
  "ec": 450,
  "tds": 280,
  "tds_ec_mod": 1.6,
  "orp": 180
}
```

---

## Language / Localization

Switch language via the navbar dropdown or by visiting:

```
/language/en    # Switch to English
/language/id    # Switch to Bahasa Indonesia
```

The selected locale is stored in the user session and applied on every request by `SetLocale` middleware. Translation files:

| File | Purpose |
|------|---------|
| `navigation.php` | Navbar, dropdowns, account menu (31 keys) |
| `ui.php` | All page-level UI labels (212 keys) |
| `messages.php` | Flash messages and status strings |

---

## Sensor Parameters

| Parameter | Unit | Optimal Range |
|-----------|------|--------------|
| Temperature | °C | 25–30 |
| pH | — | 6.5–8.5 |
| DO (Dissolved Oxygen) | mg/L | > 5 |
| Turbidity | NTU | < 25 |
| EC (Electrical Conductivity) | μS/cm | 200–1500 |
| TDS (Total Dissolved Solids) | ppm | 100–500 |
| TDS EC Mod | — | derived ratio |
| ORP | mV | 200–400 |

---

## Roles

| Role | Access |
|------|--------|
| `admin` | Admin dashboard, user management, all devices |
| `user` | Personal dashboard, own devices, fish feeding, marketplace |

---

## Artisan Commands

```bash
# Cache management
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan optimize:clear

# Database
php artisan migrate
php artisan migrate:status
php artisan db:seed

# Testing
php artisan test
php artisan test --coverage
php artisan test --filter=DeviceControllerTest
```

---

## Security Checklist (Production)

- Set `APP_DEBUG=false` and `APP_ENV=production`
- Use strong, unique database passwords
- Enable HTTPS / SSL certificate
- Configure MQTT broker authentication
- Keep `.env` out of version control (already in `.gitignore`)
- Set file permissions: `755` for files, `775` for `storage/` and `bootstrap/cache/`

---

## Troubleshooting

```bash
# View logs
tail -f storage/logs/laravel.log

# Clear all caches
php artisan optimize:clear

# Check migration status
php artisan migrate:status

# Inspect routes
php artisan route:list
```

---

## License

MIT — see `LICENSE` for details.

---

**Maintained by:** CoE STAS-RG Project Team  
**Last Updated:** June 11, 2026
