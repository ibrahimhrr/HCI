# 📅 OnlyPlans - Smart Calendar Application

A modern, intelligent calendar system with Intelligent time slot suggestions and conflict detection.

## ✨ Features

- **Smart Event Scheduling**: Create, edit, and delete events with color coding
- **Intelligent Suggestions**:  algorithm finds optimal time slots based on:
  - Existing calendar conflicts
  - Preferred time windows (6 AM - 10 PM)
  - Quality scoring (excellent/good/fair/poor)
  - 6 date range options (today, tomorrow, this week, next week, this month, next month)
- **Instant Event Creation**: One-click event creation from suggested slots
- **Visual Calendar Interface**: FullCalendar integration with drag-and-drop
- **Conflict Detection**: Automatic overlap detection prevents double-booking
- **Modern UI**: Gradient design with responsive layout

---

## 🚀 Quick Start (3 Options)

### **Option 1: Automated Installer (Easiest)**
*Best for beginners - No database knowledge needed*

1. **Start your local PHP server** (MAMP/XAMPP/WAMP)
2. **Open your browser**: `http://localhost/HCI/install.php`
3. **Follow the wizard**: Enter database credentials (usually `root`/`root`)
4. **Done!** The installer creates everything automatically

**What it does:**
- ✅ Creates database automatically
- ✅ Sets up all tables
- ✅ Configures connection
- ✅ Ready in 2 minutes

---

### **Option 2: SQLite (Most Portable)**
*Perfect for moving between devices - Zero configuration*

**Why SQLite?**
- No MySQL server needed
- Database is just a file (`onlyplans.db`)
- Works on any device with PHP
- Perfect for demos and prototypes

**Setup:**

1. **Rename the connection file:**
```bash
mv connection.php connection_mysql.php.backup
mv connection_sqlite.php connection.php
```

2. **Update your PHP files to use PDO** (I can help with this)

3. **Open in browser** - Database auto-creates!

**To share with others:**
- Copy entire folder (includes `onlyplans.db` file)
- They open `index.php` in browser
- All your events travel with the file!

---

### **Option 3: Docker (Professional)**
*Industry-standard deployment - One command setup*

**Why Docker?**
- Same environment everywhere
- No "works on my machine" issues
- Includes MySQL + phpMyAdmin
- Perfect for team collaboration

**Setup:**

1. **Install Docker Desktop**: [docker.com](https://docker.com)

2. **Run one command:**
```bash
docker-compose up
```

3. **Access your apps:**
   - Calendar: `http://localhost:8080`
   - phpMyAdmin: `http://localhost:8081`

4. **Share with team:**
   - Give them this folder
   - They run `docker-compose up`
   - Identical setup for everyone!

**Docker Commands:**
```bash
# Start services
docker-compose up

# Start in background
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs

# Reset everything
docker-compose down -v
```

---

## 📋 Manual Installation (Traditional Method)

If you prefer manual setup:

### **Requirements:**
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- phpMyAdmin (optional, for database management)

### **Steps:**

1. **Clone/Download this repository**

2. **Import database:**
   - Open phpMyAdmin
   - Create database: `onlyplans`
   - Import: `database_setup.sql`

3. **Configure connection:**
   - Edit `connection.php`
   - Update credentials:
   ```php
   $hostname = 'localhost';
   $username = 'root';
   $password = 'your_password';
   $database = 'onlyplans';
   ```

4. **Start your server:**
   - MAMP: Place in `htdocs/HCI`
   - XAMPP: Place in `htdocs/HCI`
   - WAMP: Place in `www/HCI`

5. **Open browser:** `http://localhost/HCI/index.php`

---

## 📁 Project Structure

```
HCI/
├── index.php                    # Main calendar interface
├── createEventForm.php          # Event creation form
├── createEvent.php              # Event creation backend
├── updateEvent.php              # Event update backend
├── deleteEvent.php              # Event deletion backend
├── bulkDeleteEvents.php         # Bulk deletion
├── getEvents.php                # Fetch events API
├── smartSuggestionsForm.php     # Smart suggestions UI
├── smartSuggestions.php         # Smart suggestions algorithm
├── connection.php               # Database connection
├── database_setup.sql           # Database schema
├── styles.css                   # Global styles
├── install.php                  # Automated installer
├── docker-compose.yml           # Docker configuration
├── connection_sqlite.php        # SQLite alternative
├── README.md                    # This file
├── SMART_SUGGESTIONS_GUIDE.md   # Algorithm documentation
└── js/                          # JavaScript files
```

---

## 🎯 How to Use

### **Creating Events:**

1. **From Calendar:**
   - Click any date on calendar
   - Auto-redirects to pre-filled form
   - Choose color, set times
   - Click "Create Event"

2. **From Smart Suggestions:**
   - Click "Smart Suggestions" button
   - Select duration (0.5-4 hours)
   - Choose date range
   - Pick time window
   - Click "Find Smart Suggestions"
   - Select any suggested slot → Instant creation!

### **Editing Events:**
- Click existing event
- Modify details in modal
- Save changes

### **Deleting Events:**
- Click event → Delete button
- Or use bulk delete from menu

### **Smart Suggestions Features:**
- **Quality Scoring**: Excellent (100pts), Good (75pts), Fair (50pts)
- **Conflict Detection**: Prevents overlapping events
- **Time Windows**: Morning, afternoon, evening, or all day
- **Date Ranges**: Today to next month options
- **Uniqueness**: No duplicate slots shown

---

## 🔄 Migrating Between Devices

### **Method 1: Database Export/Import**
```bash
# On old device - Export data
mysqldump -u root -p onlyplans > backup.sql

# On new device - Import data
mysql -u root -p onlyplans < backup.sql
```

### **Method 2: SQLite (Recommended)**
- Switch to SQLite (see Option 2 above)
- Just copy the entire folder
- `onlyplans.db` file contains everything
- No database setup on new device!

### **Method 3: Docker**
- Docker volumes persist data
- Copy docker-compose.yml
- Run `docker-compose up` anywhere
- Data automatically migrates

---

## 🛠️ Configuration

### **Database Connection:**
Edit `connection.php`:
```php
$hostname = 'localhost';      // Database host
$username = 'root';           // Database username
$password = 'your_password';  // Database password
$database = 'onlyplans';      // Database name
```

### **Smart Suggestions Settings:**
Edit `smartSuggestions.php` constants:
```php
define('PREFERRED_START_HOUR', 6);   // 6 AM
define('PREFERRED_END_HOUR', 22);    // 10 PM
define('TIME_SLOT_INTERVAL', 15);    // 15-minute intervals
```

### **Time Zones:**
Add to `connection.php`:
```php
date_default_timezone_set('America/New_York');
```

---

## 🎨 Color Coding System

Events support 9 color categories:
- 🔵 Blue - Default
- 🟢 Green - Work
- 🔴 Red - Important
- 🟡 Yellow - Personal
- 🟣 Purple - Health
- 🟠 Orange - Social
- 🔵 Teal - Study
- 🩷 Pink - Family
- ⚫ Black - Other

---

## 🐛 Troubleshooting

### **"Database Connection Failed"**
- Check MySQL/MAMP is running
- Verify credentials in `connection.php`
- Run automated installer: `install.php`

### **"Error finding suggestions"**
- Check console for JavaScript errors
- Verify `smartSuggestions.php` exists and is not empty
- Check PHP error logs

### **Events not showing on calendar**
- Check browser console for errors
- Verify `getEvents.php` returns JSON
- Clear browser cache

### **Color not displaying**
- Check database `color` column has valid hex values
- Verify eventRender function in `index.php`

### **Trailing whitespace errors**
- Ensure no spaces after `?>` in PHP files
- Use editor's "trim trailing whitespace" feature

---

## 📦 Sharing Your Calendar

### **For Non-Technical Users:**

1. **Package as ZIP:**
```bash
# Create portable package
zip -r OnlyPlans-Portable.zip HCI/ -x "*.git*"
```

2. **Include instructions.txt:**
```
OnlyPlans Calendar - Installation
================================

1. Install MAMP (mamp.info) or XAMPP (apachefriends.org)
2. Extract this folder to:
   - MAMP: /Applications/MAMP/htdocs/
   - XAMPP: C:/xampp/htdocs/
3. Start MAMP/XAMPP
4. Open browser: http://localhost:8888/HCI/install.php
5. Follow the setup wizard
6. Done!
```

### **For Developers:**

1. **GitHub Repository:**
```bash
git clone https://github.com/yourusername/onlyplans.git
cd onlyplans
# Run install.php or docker-compose up
```

2. **Docker Hub:**
```bash
docker pull yourusername/onlyplans
docker run -p 8080:80 yourusername/onlyplans
```

---

## 🔒 Security Notes

**Before Production Deployment:**

1. **Delete installer:** `rm install.php`
2. **Use prepared statements** (already implemented)
3. **Set environment variables:**
```php
// In production
$hostname = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
```
4. **Enable HTTPS**
5. **Add authentication system**
6. **Sanitize all inputs** (already implemented)

---

## 🚀 Deployment Options

### **Shared Hosting:**
1. Upload files via FTP
2. Create MySQL database in cPanel
3. Import `database_setup.sql`
4. Update `connection.php`

### **Cloud Platforms:**

**Heroku:**
- Add ClearDB MySQL addon
- Set environment variables
- Deploy via Git

**AWS:**
- EC2 instance + RDS MySQL
- Or use Elastic Beanstalk

**DigitalOcean:**
- One-click LAMP droplet
- Upload via SFTP

---

## 📚 Additional Documentation

- **Smart Suggestions Algorithm**: See `SMART_SUGGESTIONS_GUIDE.md`
- **API Documentation**: See inline comments in PHP files
- **Database Schema**: See `database_setup.sql`

---

## 🤝 Contributing

Found a bug or want to add features? Contributions welcome!

1. Fork the repository
2. Create feature branch
3. Make your changes
4. Test thoroughly
5. Submit pull request

---

## 📄 License

MIT License - Free to use, modify, and distribute

---

## 💡 Tips & Best Practices

### **For Best Performance:**
- Keep events under 1000 for fast rendering
- Run Smart Suggestions during off-peak hours for large calendars
- Use color coding consistently

### **For Collaboration:**
- Use Docker for team consistency
- Export/import database backups regularly
- Document custom modifications

### **For Mobile Access:**
- Responsive design works on phones/tablets
- Consider adding PWA features
- Test on various screen sizes

---

## 🎓 Learning Resources

**Technologies Used:**
- PHP 7.4+ - [php.net](https://php.net)
- MySQL 8.0 - [mysql.com](https://mysql.com)
- FullCalendar 3.4 - [fullcalendar.io](https://fullcalendar.io)
- Bootstrap 3.3 - [getbootstrap.com](https://getbootstrap.com)
- jQuery 3.2 - [jquery.com](https://jquery.com)

---

## ❓ FAQ

**Q: Can I use this offline?**  
A: Yes! Use SQLite version or run local MAMP/XAMPP server

**Q: How do I backup my data?**  
A: Export database from phpMyAdmin or copy `onlyplans.db` (SQLite)

**Q: Can multiple users access it?**  
A: Currently single-user. For multi-user, add authentication system

**Q: Does it sync across devices?**  
A: Use cloud hosting (Heroku/AWS) for automatic sync

**Q: Can I customize the colors?**  
A: Yes! Edit color options in `createEventForm.php` and `index.php`

---

## 📞 Support

Need help? Check:
1. This README
2. `SMART_SUGGESTIONS_GUIDE.md`
3. Inline code comments
4. Browser console for errors
5. PHP error logs

---

**Made with ❤️ for better time management**

*Version 1.0 - November 2025*
