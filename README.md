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


## 📋 Manual Installation (Traditional Method)

If you prefer manual setup:

### **Requirements:**
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- phpMyAdmin (optional, for database management)

### **Steps:**

1. **Clone/Download this repository**
   
2. install MMAMP (https://www.mamp.info/en/windows/)
   
3. Setup MMAMP
   -
4. Setup Database
5. 
6. **Import database:**
   - Open phpMyAdmin
   - Create database: `onlyplans`
   - Import: `database_setup.sql`

7. **Configure connection:**
   - Edit `connection.php`
   - Update credentials:
   ```php
   $hostname = 'localhost';
   $username = 'root';
   $password = 'your_password';
   $database = 'onlyplans';
   ```

8. **Start your server:**
   - MAMP: Place in `htdocs/HCI`
   - XAMPP: Place in `htdocs/HCI`
   - WAMP: Place in `www/HCI`

9. **Open browser:** `http://localhost:8888`

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



## 🎓 Learning Resources

**Technologies Used:**
- PHP 7.4+ - [php.net](https://php.net)
- MySQL 8.0 - [mysql.com](https://mysql.com)
- FullCalendar 3.4 - [fullcalendar.io](https://fullcalendar.io)
- Bootstrap 3.3 - [getbootstrap.com](https://getbootstrap.com)
- jQuery 3.2 - [jquery.com](https://jquery.com)

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
