# FutureWorth 💰

A PHP + MySQL budgeting and financial projection app based on your assignment brief.

## What is implemented

- Budget tracking CRUD for income and expenses.
- Scenario simulation endpoint (`save / invest / hybrid`) using monthly contribution + compound growth.
- Scenario save/edit/delete with a junction table (`budget_scenarios`).
- Dashboard summary with totals for income, expenses, savings, and net monthly balance.
- Fetch API frontend for dynamic updates (no page refresh).
- Prepared statements and server-side validation helpers.

## Project structure

- `index.php` – main UI page.
- `app.js` – Fetch API calls + dashboard interactions.
- `styles.css` – responsive Flexbox dashboard styling.
- `api.php` – backend CRUD and simulation endpoints.
- `db.php` – shared DB connection and validation utilities.
- `schema.sql` – database schema (4 tables + 1 junction table) and demo user.

## Setup

1. Create the database and tables:

```bash
mysql -u root < schema.sql
```

2. Update DB credentials in `db.php` if needed:

```php
const DB_HOST = '127.0.0.1';
const DB_NAME = 'futureworth';
const DB_USER = 'root';
const DB_PASS = '';
```

3. Run locally with PHP built-in server:

```bash
php -S localhost:8000
```

4. Open: `http://localhost:8000/index.php`

## API endpoints

- `GET api.php?action=dashboard`
- `POST|PUT|DELETE api.php?action=income`
- `POST|PUT|DELETE api.php?action=expense`
- `POST api.php?action=simulate`
- `POST|PUT|DELETE api.php?action=scenario`

## Demo flow covered

- Add income.
- Add expense.
- Run “what if” simulation.
- Save a scenario.
- View scenario history and projected results.
