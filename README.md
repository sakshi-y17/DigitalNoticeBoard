# College Digital Notice Board

A modern web-based digital notice board application for campus announcements and departmental communications.

The platform enables students to view announcements in real time and provides administrators with a dedicated management portal.

---

## 🚀 Features

### Student Portal
- **Pinned Announcements Ticker**: Real-time breaking news banner at the top of the portal.
- **Category Filtering**: Filter notices by category (`Exams`, `Events`, `Holidays`, `All Notices`).
- **Live Search**: Instant client-side search filtering across titles and contents.
- **Dynamic Age Badges**: Highlighting recent notices (`JUST NOW`, `NEW!`).
- **Single Notice Printing**: Print individual notices formatted for notice boards.
- **Dark Mode**: Persisted dark/light theme switch powered by `localStorage`.

### Admin Dashboard
- **Session Authentication**: Protected administration panel.
- **Department Analytics**: Real-time counts of Total Notices, Pinned Notices, and Notices Posted Today.
- **Notice Management (CRUD)**:
  - Add new notices with category tags and character limit counter.
  - Pin important notices to the top and headline ticker.
  - Edit existing notice details.
  - Delete expired announcements.
- **Searchable Management Table**: Search and filter notices within the management dashboard.

---

## 🛠️ Technology Stack

- **Backend**: PHP (Procedural & OOP MySQLi, Sessions)
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, CSS3 (CSS Variables, Flexbox, Responsive Design), Vanilla JavaScript
- **Web Server**: Apache (XAMPP) with `mod_rewrite` URL routing via `.htaccess`
- **Typography**: Google Fonts (*Montserrat*, *Lato*)

---

## 📋 Installation & Setup

### 1. Prerequisites
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL / MariaDB)
- Web browser (Chrome, Edge, Firefox)

### 2. Setup Project Files
Clone or place the project folder into your XAMPP `htdocs` directory:
```bash
c:\xampp\htdocs\digital_notice_board
```

### 3. Database Setup
1. Start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Open phpMyAdmin at `http://localhost/phpmyadmin/`.
3. Import the provided [`database.sql`](database.sql) file:
   - This creates the `digital_notice_board` database and `notices` table with seed data.

### 4. Configuration
Review [`config.php`](config.php) to configure your database connection parameters and define your custom administrator credentials:
```php
// Database Configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "digital_notice_board";
$port = 3307;

// Admin Portal Credentials
// Configure your custom administrator credentials here
define('ADMIN_USERNAME', 'your_username');
define('ADMIN_PASSWORD', 'your_password');
```

### 5. Accessing the Application
- **Student Notice Portal**: `http://localhost/digital_notice_board/`
- **Admin Login**: `http://localhost/digital_notice_board/admin_login.php` (or `/admin` if URL rewriting is enabled)

---

## 📁 Project Structure

```text
digital_notice_board/
├── index.php              # Public student announcement portal
├── admin_dashboard.php    # Admin management panel & analytics
├── admin_login.php        # Admin authentication form
├── edit_notice.php        # Notice editing interface
├── api.php                # Backend CRUD request dispatcher
├── search.php             # Search API endpoint
├── admin_search.php       # Admin table search endpoint
├── config.php             # Database & admin credentials configuration
├── logout.php             # Session destruction & sign-out
├── database.sql           # Database schema & sample seed data
├── style.css              # Main application stylesheet
├── script.js              # Live search and dynamic client logic
├── darkmode.js            # Dark mode state manager
├── .htaccess              # Apache URL rewriting rules
├── .gitignore             # Git ignored files & directories
└── docs/                  # Presentation slides and project documentation
```
