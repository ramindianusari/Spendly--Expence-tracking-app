# Expense Tracker 💰

A personal finance web app — track income, expenses, and view your balance.
Built with **PHP 8**, **MySQL 8**, **HTML/CSS/JS**, and **Chart.js**.

---

## 🛠️ Tech Stack

| Layer        | Technology               |
|--------------|--------------------------|
| Frontend     | HTML5, CSS3, JavaScript (ES6+) |
| Backend      | PHP 8.2 + Apache         |
| Database     | MySQL 8.0 (Docker)       |
| Charts       | Chart.js                 |
| Dev env      | Docker + Docker Compose  |
| DB GUI       | DBeaver                  |
| Version ctrl | Git + GitHub             |

---

## 📁 Project Structure

```
expense-tracker/
├── index.php              # Login page
├── home.php               # Dashboard / Home page
├── register.php           # New user registration
├── logout.php             # Session logout handler
├── config/
│   └── db.php             # PDO connection (reads Docker env vars)
├── includes/
│   └── auth.php           # Session helpers & utilities
├── css/
│   └── style.css          # Global dark-theme styles
├── js/
│   └── main.js            # Frontend JS
├── database.sql           # MySQL schema + seed data (auto-imported)
├── Dockerfile             # PHP 8.2 + Apache image
├── docker-compose.yml     # App + DB services
├── .env.example           # Environment variable template
└── README.md
```

---

## 🚀 Getting Started (Docker + DBeaver)

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running
- [DBeaver Community](https://dbeaver.io/download/) installed

---

### 1. Clone the Repository

```bash
git clone https://github.com/<your-username>/expense-tracker.git
cd expense-tracker
```

### 2. Create your `.env` file

```bash
# Windows PowerShell
Copy-Item .env.example .env
```

Edit `.env` if you want custom passwords (defaults work fine for local dev):

```env
MYSQL_ROOT_PASSWORD=rootpassword
MYSQL_DATABASE=expense_tracker
MYSQL_USER=expense_user
MYSQL_PASSWORD=expense_pass
```

### 3. Start the containers

```bash
docker compose up -d
```

This spins up two containers:

| Container | What it runs | Port |
|-----------|-------------|------|
| `expense_tracker_app` | PHP 8.2 + Apache | `localhost:8080` |
| `expense_tracker_db`  | MySQL 8.0 | `localhost:3306` |

> The `database.sql` schema + seed data is **automatically imported** on first run.

### 4. Open the App

👉 [http://localhost:8080](http://localhost:8080)

**Demo credentials:**

| Field    | Value               |
|----------|---------------------|
| Email    | rahul@example.com   |
| Password | password123         |

---

## 🗄️ Connect DBeaver to MySQL

1. Open DBeaver → **New Database Connection** → choose **MySQL**
2. Fill in:

| Field    | Value          |
|----------|----------------|
| Host     | `localhost`    |
| Port     | `3306`         |
| Database | `expense_tracker` |
| Username | `expense_user` |
| Password | `expense_pass` |

3. Click **Test Connection** → **Finish**

> You can now browse tables, run queries, and view your data visually in DBeaver.

---

## 🐳 Useful Docker Commands

```bash
# Start containers (detached)
docker compose up -d

# Stop containers
docker compose down

# View live logs
docker compose logs -f

# Rebuild after code changes to Dockerfile
docker compose up -d --build

# Remove containers AND wipe the database volume (fresh start)
docker compose down -v
```

---

## ✨ Features (v1.0)

- [x] User registration & login (bcrypt passwords)
- [x] Session-based authentication
- [x] Dashboard — balance, income & expense totals
- [x] Recent transactions list with category icons
- [x] Responsive mobile-first UI (dark theme, green accent)
- [x] Time-aware greeting (Good morning / afternoon / evening)
- [x] Balance counter animation on page load
- [x] Docker Compose setup (MySQL + PHP/Apache)
- [x] DBeaver-ready MySQL on `localhost:3306`

## 🗺️ Roadmap

- [ ] Add income / expense pages
- [ ] Reports page with Chart.js pie & bar charts
- [ ] Filter transactions by date / category
- [ ] Export to CSV

---

## 📄 License

MIT © 2026
