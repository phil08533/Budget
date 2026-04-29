# FutureWorth - Setup & Installation Guide

## 📋 Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Web server (Apache, Nginx, or PHP built-in server)
- Modern web browser

## 🚀 Quick Start

### 1. Clone & Navigate
```bash
git clone <repository-url>
cd Budget
```

### 2. Database Setup
```bash
# Create the database
mysql -u root -p -e "CREATE DATABASE future_worth;"

# Import the schema
mysql -u root -p future_worth < database.sql
```

### 3. Update Database Config
Edit `config.php` and update your database credentials:
```php
$db_host = 'localhost';
$db_user = 'root';      // your MySQL username
$db_pass = '';          // your MySQL password
$db_name = 'future_worth';
```

### 4. Run the App
Option A - PHP Built-in Server:
```bash
php -S localhost:8000
```

Option B - Using Apache/Nginx:
- Place files in your web root directory
- Access via `http://localhost/Budget/` (or your configured path)

### 5. Access the App
Open your browser and go to:
```
http://localhost:8000/index.html
```

## 🔑 Key Features

- **Budget Tracking**: Add income sources and track expenses
- **Real-time Dashboard**: See your monthly savings and yearly projections instantly
- **What-If Simulator**: Test different savings and investment scenarios
- **Investment Growth**: Calculate compound interest on monthly investments
- **Scenario History**: Save and revisit financial projections

## 📊 Database Tables

| Table | Purpose |
|-------|---------|
| users | User accounts (with future auth integration) |
| income | Income sources and amounts |
| expenses | Expense entries with dates |
| scenarios | Saved financial simulations |
| budget_scenarios | Junction table linking users to scenarios |

## 🔐 Security Features

✅ Prepared SQL statements (prevents SQL injection)  
✅ Input sanitization on all user inputs  
✅ XSS protection with HTML escaping  
✅ Data validation before processing  

## 🛠️ Development Notes

### Current Implementation
- **Backend**: PHP with MySQLi (prepared statements)
- **Frontend**: Vanilla JavaScript with Fetch API
- **Styling**: Modern CSS with Flexbox responsive design
- **User Session**: Currently hardcoded user_id = 1 (ready for auth integration)

### Future Enhancements
- User authentication system
- Password hashing (bcrypt)
- Session management
- Data export (PDF/CSV)
- Mobile app version
- Advanced charting with Chart.js
- Budget alerts and notifications

## 📝 API Endpoints

All endpoints use `api.php?action=<action>` format:

| Action | Method | Purpose |
|--------|--------|---------|
| add-income | POST | Add new income source |
| add-expense | POST | Add new expense |
| get-dashboard | GET | Fetch dashboard data |
| delete-income | DELETE | Remove income entry |
| delete-expense | DELETE | Remove expense entry |
| run-simulation | POST | Calculate financial projection |
| save-scenario | POST | Save scenario to database |
| get-scenarios | GET | Fetch all saved scenarios |

## 🧪 Testing the App

1. **Add Income**: Enter a source (e.g., "Salary"), amount ($2000), frequency (Monthly)
2. **Add Expense**: Enter category (e.g., "Rent"), amount ($1000), date
3. **Check Dashboard**: Watch real-time calculations update
4. **Run Simulation**: Try "What if I save $300/month for 12 months?"
5. **Save Scenario**: Save your simulation with a custom name (e.g., "Vacation Fund")

## 🐛 Troubleshooting

**"Connection failed" error:**
- Check MySQL is running
- Verify database credentials in config.php
- Ensure future_worth database exists

**CORS errors:**
- config.php includes CORS headers, but ensure your server supports them
- If using a different origin, update the Access-Control-Allow-Origin header

**No data loading:**
- Check browser console for JavaScript errors
- Verify PHP error logs
- Ensure api.php is accessible

## 📞 Support
For issues or questions, check the code comments or create a GitHub issue.

---
**Happy budgeting! 💰**
