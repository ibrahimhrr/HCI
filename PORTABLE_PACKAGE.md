# 📦 OnlyPlans Portable Package Guide

## 🎯 How to Share Your Calendar Application

This guide explains different ways to package and share OnlyPlans so it works on **any device** without complex setup.

---

## Method 1: SQLite Package (Recommended for Beginners)

**Best for:** Sharing with non-technical users, demos, prototypes

### ✅ Advantages:
- No database server needed
- Works instantly on any device with PHP
- All data in one file (`onlyplans.db`)
- Perfect for USB drives or cloud storage
- Zero configuration

### 📦 How to Package:

1. **Switch to SQLite:**
```bash
cp connection_sqlite.php connection.php
```

2. **Test it works:**
- Open in browser
- Create a test event
- Verify `onlyplans.db` file is created

3. **Create portable package:**
```bash
# On Mac/Linux
zip -r OnlyPlans-Portable.zip . -x "*.git*" "connection_mysql*" "*.DS_Store"

# On Windows
# Right-click folder → Send to → Compressed folder
```

### 📋 Include these instructions:

```
=================================
OnlyPlans Calendar - Portable Edition
=================================

QUICK START (3 minutes):

1. Install XAMPP (Windows/Mac/Linux):
   Download from: https://www.apachefriends.org/

2. Extract this folder to:
   Windows: C:\xampp\htdocs\OnlyPlans
   Mac: /Applications/XAMPP/htdocs/OnlyPlans
   Linux: /opt/lampp/htdocs/OnlyPlans

3. Start XAMPP Control Panel:
   - Start Apache service

4. Open browser:
   http://localhost/OnlyPlans/index.php

5. Done! Start creating events.

DATABASE INFO:
- Uses SQLite (file-based, no setup needed)
- Your data is in: onlyplans.db
- To backup: just copy onlyplans.db file

TROUBLESHOOTING:
- If Apache won't start: Change port to 8080 in XAMPP settings
- If you see errors: Make sure PHP is enabled in XAMPP
- Need help? See README.md

=================================
```

---

## Method 2: Docker Package (Best for Developers)

**Best for:** Teams, consistent environments, professional deployment

### ✅ Advantages:
- Identical setup on every machine
- Includes MySQL + phpMyAdmin
- One-command installation
- Industry standard
- No "works on my machine" issues

### 📦 How to Package:

1. **Ensure docker-compose.yml is configured**
2. **Create package:**
```bash
zip -r OnlyPlans-Docker.zip . -x "*.git*" "onlyplans.db"
```

### 📋 Include these instructions:

```
=================================
OnlyPlans Calendar - Docker Edition
=================================

PREREQUISITES:
- Install Docker Desktop: https://www.docker.com/products/docker-desktop

INSTALLATION (2 minutes):

1. Extract this folder anywhere

2. Open Terminal/Command Prompt in this folder

3. Run ONE command:
   docker-compose up

4. Access your apps:
   📅 Calendar:    http://localhost:8080
   🗄️  phpMyAdmin: http://localhost:8081
      Username: onlyplans_user
      Password: onlyplans_pass

5. To stop:
   Press Ctrl+C or run: docker-compose down

FEATURES:
✅ Full MySQL database
✅ phpMyAdmin included
✅ Data persists between restarts
✅ Clean uninstall: docker-compose down -v

SHARING WITH TEAM:
- Everyone just needs Docker
- Same environment guaranteed
- Data syncs via Git or cloud

=================================
```

---

## Method 3: All-in-One Installer Package

**Best for:** Business users, installation on multiple machines

### 📦 What to Include:

1. **Your application files**
2. **install.php** (automated wizard)
3. **Quickstart scripts:**
   - `quickstart.sh` (Mac/Linux)
   - `quickstart.bat` (Windows)
4. **Pre-configured XAMPP** (optional, legal to redistribute)

### 📋 Setup:

```bash
# Create comprehensive package
mkdir OnlyPlans-Installer
cp -r . OnlyPlans-Installer/
cd OnlyPlans-Installer

# Create installer README
cat > START_HERE.txt << 'EOF'
=================================
OnlyPlans Calendar - Easy Installer
=================================

AUTOMATIC SETUP:

Mac/Linux Users:
1. Open Terminal in this folder
2. Run: ./quickstart.sh
3. Choose option 1 (Automated Installer)
4. Follow the wizard

Windows Users:
1. Double-click: quickstart.bat
2. Choose option 1 (Automated Installer)
3. Follow the wizard

MANUAL SETUP:

1. Install XAMPP/MAMP
2. Copy this folder to htdocs/
3. Open: http://localhost/install.php
4. Complete the wizard

That's it!

=================================
EOF

# Package it
cd ..
zip -r OnlyPlans-Installer.zip OnlyPlans-Installer/
```

---

## Method 4: Cloud-Ready Package

**Best for:** Remote access, team collaboration, production use

### ☁️ Platforms:

#### **Heroku (Free Tier Available):**

1. Create `Procfile`:
```
web: vendor/bin/heroku-php-apache2
```

2. Create `.htaccess`:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L]
```

3. Deploy:
```bash
git init
heroku create onlyplans-calendar
heroku addons:create cleardb:ignite
git push heroku main
```

#### **DigitalOcean App Platform:**

1. Create `app.yaml`:
```yaml
name: onlyplans
services:
  - name: web
    github:
      repo: your-username/onlyplans
      branch: main
    build_command: echo "No build needed"
    run_command: php -S 0.0.0.0:8080
databases:
  - name: db
    engine: MYSQL
```

2. Connect repository and deploy

---

## 📊 Comparison Table

| Method | Setup Time | Portability | Tech Level | Best For |
|--------|-----------|-------------|------------|----------|
| SQLite | 5 min | ⭐⭐⭐⭐⭐ | Beginner | Demos, prototypes |
| Docker | 10 min | ⭐⭐⭐⭐ | Intermediate | Teams, production |
| Installer | 15 min | ⭐⭐⭐ | Beginner | Business users |
| Cloud | 30 min | ⭐⭐⭐⭐⭐ | Advanced | Remote access |

---

## 🔄 Migration Between Devices

### **From MySQL to SQLite:**
```php
// Export MySQL data
$events = mysqli_query($connection, "SELECT * FROM table_event");

// Connect to SQLite
$sqlite = new PDO('sqlite:onlyplans.db');

// Import data
while ($event = mysqli_fetch_assoc($events)) {
    $sqlite->exec("INSERT INTO table_event VALUES (...)");
}
```

### **From SQLite to MySQL:**
```bash
# Export SQLite
sqlite3 onlyplans.db .dump > backup.sql

# Import to MySQL
mysql -u root -p onlyplans < backup.sql
```

### **Between Different Servers:**
```bash
# Export from old server
mysqldump -u username -p onlyplans > backup.sql

# Import to new server
mysql -u username -p onlyplans < backup.sql
```

---

## 🎁 Distribution Checklist

Before sharing your package:

- [ ] Remove sensitive data (test events, personal info)
- [ ] Test on clean machine/VM
- [ ] Include README.md with instructions
- [ ] Add LICENSE file if open source
- [ ] Remove `.git` folder if not needed
- [ ] Test all features work after extraction
- [ ] Include database schema (database_setup.sql)
- [ ] Add contact/support information
- [ ] Version number in README
- [ ] Clear installation instructions

---

## 💡 Pro Tips

### **For USB Drive Distribution:**
1. Use SQLite version
2. Include portable XAMPP
3. Add autorun script (Windows)
4. Include video tutorial link

### **For GitHub Distribution:**
1. Good README with screenshots
2. Include demo GIF/video
3. Tag releases (v1.0, v1.1, etc.)
4. Add GitHub Actions for CI/CD

### **For Commercial Distribution:**
1. Add license validation
2. Include support documentation
3. Provide update mechanism
4. Add analytics (optional)

---

## 🔒 Security Before Distribution

1. **Remove debugging:**
```php
// Remove or comment out
error_reporting(0);
ini_set('display_errors', 0);
```

2. **Change default passwords:**
```php
// In connection.php
$password = 'CHANGE_THIS_PASSWORD';
```

3. **Add .htaccess protection:**
```apache
# Protect sensitive files
<FilesMatch "(connection\.php|database_setup\.sql)">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

4. **Use environment variables:**
```php
$hostname = getenv('DB_HOST') ?: 'localhost';
```

---

## 📞 Support Documentation Template

Create `SUPPORT.md`:

```markdown
# OnlyPlans Support

## Getting Help

1. Check README.md for setup instructions
2. Review TROUBLESHOOTING section
3. Check browser console for errors
4. Verify server is running

## Common Issues

**Calendar not loading:**
- Check Apache/XAMPP is running
- Verify path: http://localhost/[folder-name]/index.php
- Clear browser cache

**Database errors:**
- Run install.php wizard
- Check connection.php credentials
- Ensure database exists

**Events not saving:**
- Check PHP error logs
- Verify file permissions
- Test with simple event first

## Contact

- Email: [your-email]
- GitHub Issues: [repo-url]
- Documentation: README.md
```

---

## ✅ Final Package Structure

```
OnlyPlans/
├── START_HERE.txt          # Quick start guide
├── README.md               # Full documentation
├── PORTABLE_PACKAGE.md     # This file
├── SUPPORT.md              # Support information
├── quickstart.sh           # Mac/Linux installer
├── quickstart.bat          # Windows installer
├── install.php             # Web-based wizard
├── docker-compose.yml      # Docker configuration
├── database_setup.sql      # Database schema
├── connection_sqlite.php   # SQLite option
├── connection_docker.php   # Docker option
├── [all PHP files]         # Your application
└── js/                     # JavaScript files
```

---

**Package it and go! Your calendar is now portable and ready to work anywhere.** 🚀
