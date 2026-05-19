# Claude.md - Aquaculture Water Toxicity Monitoring System

**Project:** Aquaculture & Water Toxicity Monitoring System  
**Type:** Full-stack Laravel Application  
**Language:** PHP (Backend) + Blade (Frontend) + JavaScript  
**Key Technologies:** Laravel 12, MQTT, Reverb WebSockets, Sanctum API Auth

---

## 📋 Project Overview

This is a comprehensive Laravel-based monitoring system for aquaculture water quality management and toxicity detection. The system monitors real-time water parameters (pH, temperature, DO, turbidity, EC, TDS, ORP) and provides alerts for out-of-threshold conditions.

### Architecture
```
aquaculture-dan-toksisitas-danau-mod-main/
├── app/                          # Application logic
│   ├── Http/Controllers/         # Web & API controllers
│   ├── Models/                   # Eloquent models
│   ├── Policies/                 # Authorization policies
│   └── Providers/                # Service providers
├── resources/                    # Frontend assets
│   ├── views/                    # Blade templates (FRONTEND - READ ONLY)
│   ├── js/                       # JavaScript assets
│   ├── css/                      # CSS stylesheets
│   └── lang/                     # Multi-language support (EN/ID)
├── routes/                       # Route definitions
│   ├── web.php                   # Web routes
│   ├── api.php                   # REST API routes
│   └── auth.php                  # Authentication routes
├── database/                     # Migrations & seeders
├── config/                       # Configuration files
├── storage/                      # Logs & cache
└── tests/                        # Test suites (Pest)
```

---

## 🔑 Core Models & Relationships

### User
- **Roles:** Admin, User
- **Relations:** devices, fish_feedings, carts, water_quality_readings
- **Key Fields:** email, phone, photo, role

### Device
- **Purpose:** Represents sensor monitoring stations
- **Relations:** user, sensor_readings, water_quality_readings
- **Key Fields:** name, device_code (unique), location, type

### SensorReading / WaterQualityReading
- **Purpose:** Store sensor measurement data
- **Parameters:** pH, temperature, DO, turbidity, EC, TDS, ORP
- **Key Fields:** device_id, reading_value, reading_time, risk_level

### FishFeeding
- **Purpose:** Track feeding operations and fish growth
- **Key Fields:** device_id, user_id, feed_type, amount, fish_weight

### Product & Cart
- **Purpose:** Marketplace functionality
- **Relations:** User → Cart → Product
- **Key Fields:** category, price, description

---

## 🚀 Development Workflow

### Running the Application

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Start development (uses concurrently: server + queue + logs + vite)
composer run dev

# Alternative: Run separately
php artisan serve                    # Port 8000
npm run dev                          # Vite dev server
php artisan queue:listen            # Queue worker
```

### Available Artisan Commands

```bash
# Cache & Performance
php artisan optimize                # Optimize framework
php artisan optimize:clear          # Clear optimizations
php artisan config:cache            # Cache configuration
php artisan route:cache             # Cache routes

# Database
php artisan migrate                 # Run pending migrations
php artisan migrate:rollback        # Rollback last batch
php artisan migrate:status          # Show migration status
php artisan db:seed                 # Run seeders

# Testing
php artisan test                    # Run all tests
php artisan test --filter=TestName  # Run specific test
```

---

## 📝 Code Standards & Guidelines

### Backend (PHP/Laravel)

**File Organization:**
- Controllers: `app/Http/Controllers/` (Web) & `app/Http/Controllers/Api/` (API)
- Models: `app/Models/` (use singular names)
- Requests: `app/Http/Requests/` (form validation)
- Policies: `app/Policies/` (authorization logic)

**Naming Conventions:**
```php
// Controllers
class DeviceController              // Web controller
class ApiDeviceController           // API controller
class SensorDataController          // API resource controller

// Methods
public function index()             // List resources
public function show($id)           // Show single resource
public function create()            // Show create form
public function store(Request $r)   // Save new resource
public function edit($id)           // Show edit form
public function update(Request $r)  // Update resource
public function destroy($id)        // Delete resource

// Models
class Device extends Model          // Singular
class SensorReading extends Model    // Clear purpose
```

**Laravel Best Practices:**
- ✅ Use Eloquent relationships instead of manual queries
- ✅ Use Query Scopes for common filters
- ✅ Use Form Requests for validation
- ✅ Use Policies for authorization
- ✅ Use Events for domain actions
- ✅ Use Collections for data transformation
- ✅ Type hint arguments and return types
- ✅ Write tests for critical logic

**Example: Creating a New Feature**

```php
// 1. Migration (database schema)
php artisan make:migration create_alerts_table

// 2. Model
php artisan make:model Alert -m

// 3. Controller (Web & API)
php artisan make:controller AlertController -r
php artisan make:controller Api/ApiAlertController -r

// 4. Requests (validation)
php artisan make:request StoreAlertRequest
php artisan make:request UpdateAlertRequest

// 5. Policy (authorization)
php artisan make:policy AlertPolicy --model=Alert

// 6. Tests
php artisan make:test Feature/AlertTest
```

### Frontend (Blade Templates)

**⚠️ FRONTEND IS READ-ONLY**
- Claude Code CANNOT modify Blade files unless explicitly requested
- Vue/Alpine.js components should be reviewed but not modified without permission
- CSS in `resources/css/` should not be changed by Claude Code
- Any frontend improvements require user approval

**If Frontend Changes Are Requested:**
- User must explicitly request the change
- Provide detailed description of what will be modified
- Explain the impact on existing functionality
- Ensure backwards compatibility

**Frontend File Locations (View Only):**
- Templates: `resources/views/` (*.blade.php)
- Components: `resources/views/components/`
- Layouts: `resources/views/layouts/`
- CSS: `resources/css/app.css`
- JavaScript: `resources/js/` (app.js, bootstrap.js)

**Language Files:**
- English: `resources/lang/en/`
- Indonesian: `resources/lang/id/`

### API Development

**REST Endpoint Patterns:**

```
GET    /api/devices              # List all devices
POST   /api/devices              # Create device
GET    /api/devices/{id}         # Show device
PUT    /api/devices/{id}         # Update device
DELETE /api/devices/{id}         # Delete device

GET    /api/sensor-data                      # List sensor readings
POST   /api/sensor-data                      # Store reading
GET    /api/sensor-data/device/{deviceCode}  # Get readings by device
```

**API Authentication:**
```php
// Routes requiring auth (using Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/devices', [ApiDeviceController::class, 'index']);
    // ... protected routes
});
```

**Request/Response Format:**

```json
// Request
{
  "name": "Sensor A",
  "device_code": "SENSOR_A_001",
  "location": "Lake Zone 1"
}

// Success Response (200)
{
  "success": true,
  "data": { ... },
  "message": "Operation successful"
}

// Error Response (400/500)
{
  "success": false,
  "message": "Error description",
  "errors": { "field": "validation message" }
}
```

---

## ⚙️ CLAUDE CODE RESTRICTIONS

### ✅ ALLOWED Operations

1. **Backend Development**
   - Create/modify controllers (Web & API)
   - Create/modify models and relationships
   - Create/modify migrations
   - Create/modify service classes
   - Create/modify API endpoints
   - Write tests (Pest)
   - Create/modify seeders
   - Add new features

2. **Database**
   - Create new migrations
   - Modify database schema (via migrations)
   - Create seeders for test data
   - Database queries and optimization

3. **Configuration**
   - Modify `config/` files
   - Modify `.env` settings (documentation only)
   - Modify `routes/` files (backend routes)

4. **Code Analysis**
   - Suggest improvements to existing code
   - Identify performance bottlenecks
   - Find potential security issues
   - Recommend refactoring opportunities

### ❌ PROHIBITED Operations

1. **Git/GitHub Operations**
   - NO git commits
   - NO git pushes
   - NO creating/deleting branches
   - NO interacting with GitHub API
   - NO pulling from remote
   - NO merging code
   - **Reason:** User handles all version control manually

2. **Frontend Modifications**
   - ❌ Modify Blade templates (resources/views/)
   - ❌ Modify CSS (resources/css/)
   - ❌ Modify JavaScript (resources/js/)
   - ❌ Modify language files (resources/lang/)
   - **Exception:** Only if user explicitly requests with detailed instructions
   - **Reason:** Frontend control remains with user

3. **Environment Changes**
   - ❌ Modify `.env` file directly
   - ❌ Run migrations automatically (always ask first)
   - ❌ Change database without user approval
   - ❌ Run destructive commands (like migrate:reset)

4. **Deployment Operations**
   - ❌ Deploy to production
   - ❌ Run deployment scripts
   - ❌ Modify server files
   - ❌ Change hosting configuration

---

## 💡 Improvement Suggestions & Best Practices

### 1. **MQTT Integration Improvements**

**Current State:** Using php-mqtt/client for sensor data

**Suggested Enhancements:**
```php
// Create MQTT service class
app/Services/MqttService.php

class MqttService {
    public function subscribeToDeviceTopic(Device $device)
    public function publishAlert(Alert $alert)
    public function handleDisconnection()
}

// Use Laravel queues for MQTT operations
php artisan make:job ProcessMqttMessage
php artisan make:job SubscribeToMqttTopics

// Create event for sensor data received
php artisan make:event SensorDataReceived
// Listeners can trigger notifications, store data, check thresholds
```

### 2. **Real-time Updates with Reverb**

**Current State:** Reverb installed but underutilized

**Suggested Enhancements:**
```php
// Create broadcasting channels
app/Broadcasting/DeviceChannel.php

// Use Echo.js in frontend for real-time dashboard
// Broadcast sensor updates as they arrive
// Create real-time alerts system

// Example event:
event(new SensorReadingReceived($device, $reading));

// Configure in config/broadcasting.php for production
```

### 3. **Automated Alert System**

**Suggestion:** Enhance threshold-based alerts
```php
// Create alert rules system
app/Models/AlertRule.php    // Define when to alert
app/Services/AlertService.php // Check conditions
app/Jobs/EvaluateAlerts.php  // Run periodically

// Support multiple alert types:
- Email notifications
- SMS notifications (Twilio)
- In-app notifications
- Webhook callbacks
```

### 4. **Data Visualization & Reports**

**Suggestion:** Add charting library
```bash
npm install chart.js
npm install laravel-echo

// Create reports feature:
- Daily/Weekly/Monthly water quality reports
- Trend analysis charts
- PDF export functionality
- Real-time dashboard widgets
```

### 5. **API Documentation**

**Suggestion:** Generate API docs with Scribe
```bash
composer require --dev knuckleswtf/scribe

// Auto-generate documentation from routes
// Create interactive API testing interface
```

### 6. **Caching Strategy**

**Suggestion:** Implement intelligent caching
```php
// Cache frequently accessed data
Cache::remember('device.readings.' . $id, 60 * 5, fn() => 
    SensorReading::where('device_id', $id)
        ->latest()
        ->take(50)
        ->get()
);

// Cache device list
Cache::remember('user.devices.' . auth()->id(), 60, fn() =>
    auth()->user()->devices()->get()
);
```

### 7. **Logging & Monitoring**

**Suggestion:** Enhanced logging setup
```php
// Log all sensor readings for audit
Log::channel('sensors')->info('Reading received', [
    'device_id' => $device->id,
    'values' => $reading->values,
    'timestamp' => now()
]);

// Monitor API performance
// Track sensor data ingestion rates
// Alert on data gaps
```

### 8. **Role-Based Features**

**Current:** Admin/User roles exist

**Suggested Enhancement:**
```php
// Add more granular roles:
- SuperAdmin
- DataManager
- Observer (read-only)
- Maintenance

// Create role-based views and permissions
- Different dashboard for each role
- Feature access control
- Data visibility filters
```

### 9. **Testing Coverage**

**Current:** Tests exist in tests/ directory

**Suggestions:**
```bash
# Add feature tests
php artisan make:test Feature/SensorDataControllerTest

# Add unit tests
php artisan make:test Unit/SensorReadingTest

# Add API tests
php artisan make:test Feature/Api/ApiDeviceControllerTest

# Aim for 80%+ code coverage
php artisan test --coverage
```

### 10. **Performance Optimization**

**Suggestions:**
- Add database indexes on frequently queried columns
- Use query eager loading (`with()` to prevent N+1 queries)
- Implement pagination for large datasets
- Cache sensor readings at regular intervals
- Use Laravel Horizon for queue monitoring

---

## 🔒 Security Considerations

### Critical Security Points

1. **Authentication & Authorization**
   - ✅ Sanctum for API auth (configured)
   - ✅ Role middleware for web routes (configured)
   - ✅ Policies for model authorization
   - ⚠️ Always validate user ownership of resources

2. **Data Validation**
   - ✅ Use Form Requests for all inputs
   - ✅ Validate sensor data types and ranges
   - ✅ Implement rate limiting on API endpoints
   - ✅ Whitelist valid sensor parameters

3. **SQL Injection Prevention**
   - ✅ Always use Eloquent or parameterized queries
   - ❌ NEVER use raw string concatenation in queries

4. **MQTT Security**
   - Configure authentication for MQTT broker
   - Use TLS/SSL for MQTT connections
   - Validate device codes before processing sensor data

5. **API Security**
   - Rate limit API endpoints: `throttle:60,1`
   - Validate API token origin
   - Use CORS appropriately
   - Implement request signing for device communications

6. **Environment Variables**
   - Keep `.env` file out of version control (already in .gitignore)
   - Use strong database passwords
   - Rotate API keys regularly
   - Document required environment variables

---

## 📊 Recommended Project Structure for Future Features

```
✅ Current Implementation:
- User management
- Device management
- Sensor data collection
- Fish feeding tracking
- Product marketplace
- Multi-language support (EN/ID)

🎯 Recommended Next Steps (in priority order):

1. Enhanced Alerting System
   - Customizable alert rules per user
   - Multiple notification channels
   - Alert history & statistics

2. Data Export & Reporting
   - CSV/PDF export functionality
   - Scheduled reports
   - Data visualization dashboard

3. Advanced Analytics
   - Water quality trend analysis
   - Predictive alerts
   - Historical comparisons

4. Integration Extensions
   - Webhook support for external systems
   - SMS notifications (Twilio/Nexmo)
   - Email notifications with templates

5. Mobile App Support
   - Dedicated mobile API endpoints
   - Push notifications
   - Offline-first features

6. System Monitoring
   - Device health checks
   - Network status monitoring
   - Data synchronization tracking
```

---

## 📞 Common Development Tasks

### Adding a New API Endpoint

```bash
# 1. Create controller
php artisan make:controller Api/ApiNewFeatureController -r

# 2. Create request validation
php artisan make:request StoreNewFeatureRequest

# 3. Create model (if needed)
php artisan make:model NewFeature -m

# 4. Add routes in routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('new-features', ApiNewFeatureController::class);
});

# 5. Create test
php artisan make:test Feature/Api/ApiNewFeatureControllerTest

# 6. Run migration
php artisan migrate
```

### Debugging Tips

```bash
# View recent logs
tail -f storage/logs/laravel.log

# Check database migrations status
php artisan migrate:status

# List all routes
php artisan route:list

# Test an endpoint
curl -X GET http://localhost:8000/api/devices \
  -H "Authorization: Bearer YOUR_TOKEN"

# Use Tinker for quick testing
php artisan tinker
> $device = App\Models\Device::first();
> $device->readings()->count();
```

---

## 🎯 Summary of Claude Code Boundaries

| Operation | Allowed | Notes |
|-----------|---------|-------|
| Backend Code | ✅ Yes | Controllers, Models, Services, Jobs |
| API Development | ✅ Yes | REST endpoints with Sanctum auth |
| Database | ✅ Yes | Via migrations only |
| Tests | ✅ Yes | Unit & Feature tests (Pest) |
| Configuration | ✅ Yes | config/ files, routes/web.php & routes/api.php |
| Suggestions | ✅ Yes | Actively suggest improvements & optimizations |
| **Frontend** | ❌ No | Read-only unless explicitly requested |
| **GitHub** | ❌ No | No commits, pushes, or git operations |
| **Deployment** | ❌ No | No production deployments |
| **Environment** | ⚠️ Ask | Only with explicit user permission |

---

## 📚 Useful Resources

- **Laravel Documentation:** https://laravel.com/docs
- **Pest Testing:** https://pestphp.com
- **Reverb WebSockets:** https://reverb.laravel.com
- **Laravel Sanctum:** https://laravel.com/docs/sanctum
- **MQTT Protocol:** https://mqtt.org

---

**Last Updated:** May 5, 2026  
**Version:** 1.0  
**Maintained by:** Project Team