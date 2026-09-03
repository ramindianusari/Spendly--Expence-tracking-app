# Expense Tracker 💰

A personal finance web app built with **PHP**, **MySQL**, **HTML/CSS/JS**, and **Chart.js**.
Track your income and expenses, view your balance, and analyse spending trends.

---

## 🛠️ Tech Stack

| Layer       | Technology        |
|-------------|-------------------|
| Frontend    | HTML5, CSS3, JavaScript (ES6+) |
| Backend     | PHP 8.x           |
| Database    | MySQL 8.x         |
| Charts      | Chart.js          |
| Local dev   | XAMPP             |
| Version control | Git + GitHub  |

---

## 📁 Project Structure

```
expense-tracker/
├── index.php           # Login page
├── home.php            # Dashboard / Home page
├── register.php        # New user registration
├── logout.php          # Session logout handler
├── config/
│   └── db.php          # PDO database connection
├── includes/
│   └── auth.php        # Session helpers & utilities
├── css/
│   └── style.css       # Global dark-theme styles
├── js/
│   └── main.js         # Frontend JS (validation, animations)
├── database.sql        # DB schema + seed data
└── README.md
```

---

## 🚀 Getting Started (XAMPP)

### 1. Clone the Repository

```bash
git clone https://github.com/<your-username>/expense-tracker.git
```

### 2. Place in XAMPP htdocs

```
C:\xampp\htdocs\expense-tracker\
```

### 3. Create the Database

1. Start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
3. Click **New** → name it `expense_tracker` → **Create**.
4. Select the database, click **Import**, and upload `database.sql`.

### 4. Configure DB Credentials (if needed)

Edit `config/db.php`:

```php
define('DB_USER', 'root');   // your MySQL username
define('DB_PASS', '');        // your MySQL password (blank by default in XAMPP)
```

### 5. Open the App

Visit [http://localhost/expense-tracker](http://localhost/expense-tracker)

**Demo credentials (from seed data):**

| Field    | Value                  |
|----------|------------------------|
| Email    | rahul@example.com      |
| Password | password123            |

---

## ✨ Features (v1.0)

- [x] User registration & login (bcrypt passwords)
- [x] Session-based authentication
- [x] Dashboard with total balance, income & expense summary
- [x] Recent transactions list with category icons
- [x] Responsive mobile-first UI (dark theme)
- [x] Time-aware greeting (Good morning / afternoon / evening)
- [x] Client-side form validation + server-side validation
- [x] Balance counter animation on page load

## 🗺️ Roadmap

- [ ] Add income / expense pages
- [ ] Reports page with Chart.js pie & bar charts
- [ ] Filter transactions by date / category
- [ ] Export to CSV

---

## 📸 Screenshots

| Login | Dashboard |
|-------|-----------|
| ![Login](assets/screenshots/login.png) | ![Home](assets/screenshots/home.png) |

---

## 📄 License

MIT © 2026
