<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FutureWorth - Financial Planning</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    .header-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      background: linear-gradient(135deg, #0a5fb5 0%, #0a246a 100%);
      color: white;
      padding: 15px 20px;
      border-radius: 16px;
    }
    .user-info {
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .logout-btn {
      background: #cc3333;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.85rem;
      transition: all 0.2s ease;
    }
    .logout-btn:hover {
      background: #bb2222;
      transform: translateY(-1px);
    }
  </style>
</head>
<body>
  <div class="header-top">
    <div style="display: flex; align-items: center; gap: 15px;">
      <div style="width: 48px; height: 48px; font-size: 2.5rem; display: flex; align-items: center; justify-content: center;">$</div>
      <h2 style="margin: 0; color: white; font-size: 1.5rem; font-weight: 700;">Runway</h2>
    </div>
    <div style="display: flex; gap: 15px; align-items: center; margin-left: auto;">
      <select id="themeSelect" onchange="changeTheme(this.value)" style="padding: 6px 12px; border-radius: 6px; border: none; background: rgba(255,255,255,0.95); cursor: pointer; font-weight: 600;">
        <option value="blue">Blue</option>
        <option value="sunset">Sunset</option>
        <option value="ocean">Ocean</option>
        <option value="purple">Purple</option>
        <option value="forest">Forest</option>
        <option value="rose">Rose</option>
        <option value="dark">Dark</option>
      </select>
      <span style="color: white; font-size: 0.9rem;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
      <button class="logout-btn" onclick="logout()">Logout</button>
    </div>
  </div>

  <header class="hero">
    <div style="font-size: 3.5rem; margin-right: 20px;">$</div>
    <div>
      <h1>Runway</h1>
      <p>Financial planning and budgeting dashboard</p>
    </div>
  </header>

  <main class="container">
    <!-- FINANCIAL DASHBOARD -->
    <section>
      <h2>Financial Dashboard <span class="help-icon" title="View your complete financial summary at a glance">?</span></h2>
      <div class="cards" id="dashboard">
        <article class="card">
          <h3>Total Monthly Income</h3>
          <p id="incomeTotal">$0.00</p>
          <small style="color: #999; font-size: 0.8rem;">All recurring income sources</small>
        </article>
        <article class="card">
          <h3>Total Monthly Expenses</h3>
          <p id="expenseTotal">$0.00</p>
          <small style="color: #999; font-size: 0.8rem;">Average monthly spending</small>
        </article>
        <article class="card highlight-card">
          <h3>Monthly Savings</h3>
          <p id="savingsTotal">$0.00</p>
          <small style="color: #999; font-size: 0.8rem;">Money you're saving each month</small>
        </article>
        <article class="card highlight-card">
          <h3>Annual Savings</h3>
          <p id="yearlyTotal">$0.00</p>
          <small style="color: #999; font-size: 0.8rem;">Total savings per year</small>
        </article>
      </div>
    </section>

    <!-- SAVINGS BREAKDOWN -->
    <section>
      <h2>Savings Overview <span class="help-icon" title="Your monthly and yearly savings potential">?</span></h2>
      <div class="grid-2">
        <div class="breakdown-card">
          <h3>Monthly Savings</h3>
          <p id="monthlySavings" style="color: var(--xp-accent); font-weight: bold;">$0.00/month</p>
          <small id="monthlySavingsNote" style="color: #999;">Income minus expenses</small>
        </div>
        <div class="breakdown-card">
          <h3>Yearly Savings</h3>
          <p id="yearlySavings" style="color: var(--xp-accent); font-weight: bold;">$0.00/year</p>
          <small id="yearlySavingsNote" style="color: #999;">12 months of savings</small>
        </div>
      </div>
    </section>

    <!-- CURRENT SAVINGS & RUNWAY -->
    <section>
      <h2>Your Financial Runway <span class="help-icon" title="How long can you live off your savings?">?</span></h2>
      <p style="color: #666; margin: 0 0 1.5rem 0; font-size: 0.95rem;">Enter your current savings to see how many months you can survive on them if you stop earning money.</p>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
        <div>
          <label>
            <small style="color: var(--xp-accent); font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Current Savings Balance ($)</small>
            <input type="number" id="currentSavings" step="0.01" placeholder="0.00" value="0" style="padding: 12px; border: 1px solid var(--xp-border); border-radius: 6px; font-size: 0.95rem;" onchange="calculateRunway()" oninput="calculateRunway()" />
          </label>
        </div>
        <div style="display: flex; align-items: flex-end;">
          <button class="btn" type="button" onclick="calculateRunway()" style="width: 100%; padding: 12px;">Calculate Runway</button>
        </div>
      </div>

      <div id="runwayResults"></div>
    </section>
    <section>
      <h2>Budget Breakdown <span class="help-icon" title="See your income and expenses organized by frequency">?</span></h2>
      <div class="grid-2">
        <div class="breakdown-card">
          <h3>Daily Expenses</h3>
          <p id="dailyExpenses" style="color: var(--accent); font-weight: bold;">$0.00/day</p>
          <small id="dailyExpenseCount" style="color: #999;">0 items</small>
        </div>
        <div class="breakdown-card">
          <h3>Weekly Expenses</h3>
          <p id="weeklyExpenses" style="color: var(--accent); font-weight: bold;">$0.00/week</p>
          <small id="weeklyExpenseCount" style="color: #999;">0 items</small>
        </div>
        <div class="breakdown-card">
          <h3>Monthly Expenses</h3>
          <p id="monthlyExpenses" style="color: var(--accent); font-weight: bold;">$0.00/month</p>
          <small id="monthlyExpenseCount" style="color: #999;">0 items</small>
        </div>
        <div class="breakdown-card">
          <h3>Yearly Expenses</h3>
          <p id="yearlyExpenses" style="color: var(--accent); font-weight: bold;">$0.00/year</p>
          <small id="yearlyExpenseCount" style="color: #999;">0 items</small>
        </div>
      </div>
    </section>

    <!-- INCOME & EXPENSE TRACKING -->
    <section>
      <h2>Budget Tracking <span class="help-icon" title="Add your income sources and track your spending here">?</span></h2>
      <div class="grid-2">
        <!-- ADD INCOME -->
        <div>
          <h2 style="border-bottom: 2px dashed var(--accent); margin-top: 0;">Add Income Source</h2>
          <form id="incomeForm" class="stack">
            <label>
              <small style="color: #666; text-transform: uppercase;">Source Name <span class="help-icon" title="Name of your income source (e.g., Salary, Freelance, Bonuses)">?</span></small>
              <input name="source_name" placeholder="e.g., Salary, Freelance, Bonus" required />
            </label>
            <label>
              <small style="color: #666; text-transform: uppercase;">Amount <span class="help-icon" title="How much money per frequency period">?</span></small>
              <input name="amount" type="number" step="0.01" placeholder="0.00" required />
            </label>
            <label>
              <small style="color: #666; text-transform: uppercase;">How Often <span class="help-icon" title="Daily, Weekly, Bi-Weekly, Monthly, or Yearly">?</span></small>
              <select name="frequency">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="bi-weekly">Bi-Weekly</option>
                <option value="monthly" selected>Monthly</option>
                <option value="yearly">Yearly</option>
              </select>
            </label>
            <button class="btn" type="submit">+ Add Income</button>
          </form>
          <div id="incomeList" style="margin-top: 1rem;"></div>
        </div>

        <!-- ADD EXPENSE -->
        <div>
          <h2 style="border-bottom: 2px dashed var(--accent); margin-top: 0;">Add Expense</h2>
          <form id="expenseForm" class="stack">
            <label>
              <small style="color: #666; text-transform: uppercase;">Category <span class="help-icon" title="What type of expense? (e.g., Groceries, Rent, Entertainment)">?</span></small>
              <input name="category" placeholder="e.g., Groceries, Rent, Transportation" required />
            </label>
            <label>
              <small style="color: #666; text-transform: uppercase;">Amount <span class="help-icon" title="How much did you spend or will spend">?</span></small>
              <input name="amount" type="number" step="0.01" placeholder="0.00" required />
            </label>
            <label>
              <small style="color: #666; text-transform: uppercase;">Frequency <span class="help-icon" title="Is this a one-time expense or does it repeat?">?</span></small>
              <select name="frequency">
                <option value="one-time" selected>One-Time</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="bi-weekly">Bi-Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
              </select>
            </label>
            <label>
              <small style="color: #666; text-transform: uppercase;">Date <span class="help-icon" title="When did or will this expense occur">?</span></small>
              <input id="expenseDate" name="date" type="date" required />
            </label>
            <button class="btn" type="submit">+ Add Expense</button>
          </form>
          <div id="expenseList" style="margin-top: 1rem;"></div>
        </div>
      </div>
    </section>

    <!-- SAVED BUDGETS -->
    <section>
      <h2>Saved Budgets <span class="help-icon" title="Save and compare your budget snapshots (max 3)">?</span></h2>
      <p style="color: #666; margin: 0 0 1.5rem 0; font-size: 0.95rem;">Save your current budget to compare different scenarios. You can save up to 3 budgets.</p>
      <button class="btn" style="margin-bottom: 1.5rem;" onclick="saveBudget()">Save Current Budget</button>
      <div class="history" id="budgetList"></div>
    </section>
  </main>

  <!-- HELP TOOLTIP -->
  <div id="helpTooltip" class="tooltip" style="display: none;"></div>

  <script src="app.js"></script>
</body>
</html>
