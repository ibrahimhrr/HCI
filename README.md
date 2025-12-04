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
     - once installation process is complete
     - you will have to first set up the ports you need.
       - This is a step by step guide on what to do  
       - ![alt text](https://github.com/ibrahimhrr/HCI/blob/3921960dd923eb76112b0ceb299315c195c976d6/img/Screenshot%202025-12-03%20160530.png)
       - ![alt text](https://github.com/ibrahimhrr/HCI/blob/3921960dd923eb76112b0ceb299315c195c976d6/img/Screenshot%202025-12-03%20160640.png)
       - ![alt text](https://github.com/ibrahimhrr/HCI/blob/3921960dd923eb76112b0ceb299315c195c976d6/img/Screenshot%202025-12-03%20160709.png)
       - Here you would put in the path to the githubr repo
         - ![alt text](https://github.com/ibrahimhrr/HCI/blob/3921960dd923eb76112b0ceb299315c195c976d6/img/Screenshot%202025-12-03%20160733.png)
       - You start the server now 
         - ![alt text](https://github.com/ibrahimhrr/HCI/blob/3921960dd923eb76112b0ceb299315c195c976d6/img/Screenshot%202025-12-03%20160819.png)
       - We start the Webpage server
       - ![alt text](https://github.com/ibrahimhrr/HCI/blob/3921960dd923eb76112b0ceb299315c195c976d6/img/Screenshot%202025-12-03%20160904.png)

4. Setup Database
   - You will then go over phpMyadmin and import the mydatabase.sql file to set up the database
     - ![alt text](https://github.com/ibrahimhrr/HCI/blob/3921960dd923eb76112b0ceb299315c195c976d6/img/Screenshot%202025-12-03%20160941.png)
   - Click on the Database button and create a database named mydatabase
     - ![alt text](https://github.com/ibrahimhrr/HCI/blob/28ffacd80655f8a4af7086ec6eefe44368204954/img/Screenshot%202025-12-03%20161048.png)
   - Once Created, You need you now click that database on the right hand side, then move onto the right and select the import section and then click the browse button
     - ![alt text](https://github.com/ibrahimhrr/HCI/blob/28ffacd80655f8a4af7086ec6eefe44368204954/img/Screenshot%202025-12-03%20161137.png)
   - Before you import the mydatabase.sql file, you need to first change some code in the file(for some reason this is what needs to be changed in order to work on Windows) and save the  file 
     - ![alt text](https://github.com/ibrahimhrr/HCI/blob/28ffacd80655f8a4af7086ec6eefe44368204954/img/Screenshot%202025-12-03%20161333.png)
   - You then click the browse button, locate the file and import it to the database.
   - Finally to get the application to show up make sure the port is set to the number below:
     - ![alt text](https://github.com/ibrahimhrr/HCI/blob/28ffacd80655f8a4af7086ec6eefe44368204954/img/Screenshot%202025-12-03%20161523.png)

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


*Version 1.0 - November 2025*
