# Aquaculture & Water Toxicity Monitoring System

> A comprehensive Laravel-based monitoring system for aquaculture water quality management and toxicity detection.

## 🌊 Features

- **Real-time Water Quality Monitoring**
  - pH levels
  - Water temperature
  - Dissolved oxygen (DO)
  - Turbidity (NTU)
  - Electrical conductivity (EC)
  - Total Dissolved Solids (TDS)
  - Oxidation-Reduction Potential (ORP)

- **Device Management**
  - Register multiple monitoring devices
  - Track device locations
  - View device history and readings

- **Fish Feeding Management**
  - Track feeding schedules
  - Monitor fish weight growth
  - Record feed types and amounts

- **Product Marketplace**
  - Browse aquaculture products
  - Shopping cart functionality
  - Product management for admin

- **User Management**
  - Role-based access (Admin/User)
  - User authentication and authorization
  - Profile management

- **Alerts & Notifications**
  - Threshold-based warnings
  - Water quality alerts
  - Automatic risk assessment

## 🚀 Quick Start

### Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL >= 8.0
- Web Server (Nginx/Apache)

### Local Development

```bash
# Clone repository
git clone [repository-url]
cd aquaculture-dan-toksisitas-danau-mod

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
# DB_DATABASE=your_database
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Build assets
npm run dev

# Start development server
php artisan serve
```

Visit `http://localhost:8000`

## 📦 Deployment to VPS

**IMPORTANT:** Before deploying to production, read the comprehensive deployment guide:

📚 **[DEPLOYMENT.md](DEPLOYMENT.md)** - Complete step-by-step deployment guide

📋 **[PRE-DEPLOYMENT-CHECKLIST.md](PRE-DEPLOYMENT-CHECKLIST.md)** - Checklist before going live

### Quick Deploy (Linux VPS)

```bash
# 1. Upload files to VPS
# 2. Copy production environment
cp .env.production .env

# 3. Edit .env with your credentials
nano .env

# 4. Run deployment script
chmod +x deploy.sh
./deploy.sh

# 5. Setup web server (see DEPLOYMENT.md)
```

### Quick Deploy (Windows)

```powershell
# 1. Copy production environment
Copy-Item .env.production .env

# 2. Edit .env with your credentials

# 3. Run deployment script
.\deploy.ps1
```

## 🛠️ Available Scripts

### Deployment
- `deploy.sh` / `deploy.ps1` - Full deployment script
- `backup.sh` - Database and files backup
- `troubleshoot.sh` / `troubleshoot.ps1` - Diagnostic tool

### Artisan Commands

```bash
# Cache management
php artisan cache:clear       # Clear application cache
php artisan config:clear      # Clear config cache
php artisan route:clear       # Clear route cache
php artisan view:clear        # Clear compiled views

# Optimization
php artisan config:cache      # Cache config
php artisan route:cache       # Cache routes
php artisan view:cache        # Cache views
php artisan optimize          # Optimize framework

# Database
php artisan migrate           # Run migrations
php artisan migrate:rollback  # Rollback last migration
php artisan migrate:status    # Show migration status
```

## 🔒 Security

### Production Checklist

- ✅ Set `APP_DEBUG=false`
- ✅ Set `APP_ENV=production`
- ✅ Use strong database passwords
- ✅ Enable HTTPS/SSL
- ✅ Set proper file permissions (755/775)
- ✅ Keep dependencies updated
- ✅ Regular backups

### File Permissions (Linux)

```bash
# Application files
chmod -R 755 /var/www/your-app

# Writable directories
chmod -R 775 storage bootstrap/cache

# Owner
chown -R www-data:www-data /var/www/your-app
```

## 📊 Monitoring & Logs

### View Logs

```bash
# Linux/Mac
tail -f storage/logs/laravel.log

# Windows PowerShell
Get-Content storage/logs/laravel.log -Wait
```

### Error Tracking

All errors are logged to `storage/logs/laravel.log` with context information.

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter TestName
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support & Troubleshooting

### Common Issues

**Error 500 - Internal Server Error**
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan optimize:clear

# Check permissions
chmod -R 775 storage bootstrap/cache
```

**Database Connection Failed**
```bash
# Check .env database credentials
# Test connection
php artisan migrate:status
```

**Missing Dependencies**
```bash
# Reinstall
composer install
npm install
```

### Getting Help

1. Check logs: `storage/logs/laravel.log`
2. Run troubleshooting: `./troubleshoot.sh`
3. Review documentation: `DEPLOYMENT.md`
4. Check Laravel documentation: https://laravel.com/docs

## 📞 Contact

For issues and questions, please open an issue on GitHub.

---

Built with ❤️ using Laravel

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
