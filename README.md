# Budget💰 WebApp Topic: “FutureWorth” (Budgeting + Financial Projection App)
📌 Core Idea

A budgeting app that does more than track money. It shows users:

How much money they will have by the end of the year if they save consistently
“What if I saved this amount for X time?”
“What if I invested this monthly instead?”
Estimated growth based on average return rates

It turns budgeting into future financial simulation, not just tracking.

🧠 Main Features
1. 📊 Budget Tracking (CRUD system)

Users can:

Add income sources
Add expenses
Update entries
Delete entries

Each entry affects:

Monthly savings
Yearly projection
2. 📈 Future Savings Calculator

Users input:

Monthly savings goal

App outputs:

End-of-year total savings
Total saved over custom time period

Example:

“If you save $300/month, you will have $3,600 in 12 months.”

3. 📉 “What If I Saved?” Simulator

Users can test scenarios:

“What if I saved $100/week?”
“What if I stopped buying coffee?”
“What if I saved 20% of my income?”

Outputs:

Total savings over time
Visual growth projection
4. 📊 Investment Growth Simulator

Users input:

Monthly investment amount
Time period
Expected average return rate (default e.g. 7%)

Outputs:

Estimated portfolio value
Compound growth visualization

Example:

“Investing $200/month at 7% return = ~$34,000 in 10 years”

🗄️ DATABASE DESIGN (Required for your assignment)

You will have at least 4 tables + 1 junction table

1. Users Table
user_id (PK)
username
email
password_hash
2. Income Table
income_id (PK)
user_id (FK)
source_name
amount
frequency
3. Expenses Table
expense_id (PK)
user_id (FK)
category
amount
date
4. Scenarios Table
scenario_id (PK)
user_id (FK)
type (save / invest / hybrid)
monthly_amount
duration_months
expected_return_rate
5. 🔗 Budget_Scenarios (Junction Table)

This links users to saved financial simulations.

id (PK)
user_id (FK)
scenario_id (FK)
saved_name (e.g. “Vacation Plan”, “Retirement Test”)

👉 This satisfies your junction table requirement

🔄 CRUD OPERATIONS

You will implement:

CREATE
Add income
Add expenses
Create financial scenarios
READ
Dashboard summary
Scenario results
Monthly breakdown
UPDATE
Edit expenses/income
Modify scenario values
DELETE
Remove expenses/income
Delete scenarios
⚙️ BACKEND REQUIREMENTS (AMPPS/PHP)
PHP handles:
database queries
CRUD operations
API endpoints for Fetch
MySQL database stores:
user data
financial entries
scenarios
Must use:
prepared statements (SQL injection protection)
🌐 FRONTEND REQUIREMENTS
🧱 Layout Structure
1. Hero Section
Title: “See Your Financial Future”
Subtitle: “Budget smarter. Save faster. Predict your wealth.”
CTA Button: “Start Simulating”
2. Dashboard Section
Income summary
Expense breakdown
Savings total
Net monthly balance
3. Simulation Section
Input sliders:
monthly savings
investment amount
return rate
time period
Output cards:
projected savings
projected investment growth
4. Scenario History Section
Saved simulations
clickable results
edit/delete options
🎨 UI REQUIREMENTS
Flexbox layout required
Clean financial dashboard style
Cards for:
income
expenses
projections
Responsive design (desktop + mobile)
🔗 FETCH API USAGE

You will use Fetch API for:

Adding income/expenses without refresh
Loading dashboard data dynamically
Running simulations
Saving scenarios
Updating results live
📊 SECURITY REQUIREMENTS
Prepared SQL statements
Input validation
Sanitization of user inputs
No direct SQL string concatenation


You should show:

Adding income
Adding expenses
Running a “what if” simulation
Saving a scenario
Viewing projected results
