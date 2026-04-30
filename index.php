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
      <svg class="logo" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <style>.logo-fill { fill: white; }</style>
        </defs>
        <circle cx="24" cy="28" r="12" class="logo-fill"/>
        <path d="M24 8c-4 0-7.2 2.4-8 5.6v2.4c0 2.4 3.6 4 8 4s8-1.6 8-4v-2.4C31.2 10.4 28 8 24 8zm0 6c-2.2 0-4-0.9-4-2s1.8-2 4-2s4 0.9 4 2S26.2 14 24 14z" class="logo-fill"/>
        <path d="M20 22c-2 1-3.2 2.8-3.2 4.8v4.8c0 2.4 3.6 4 8 4s8-1.6 8-4v-4.8c0-2-1.2-3.8-3.2-4.8" stroke="white" stroke-width="1.5" fill="none"/>
      </svg>
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
    <svg class="logo" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" style="width: 64px; height: 64px;">
      <defs>
        <style>.logo-fill { fill: white; }</style>
      </defs>
      <circle cx="24" cy="28" r="12" class="logo-fill"/>
      <path d="M24 8c-4 0-7.2 2.4-8 5.6v2.4c0 2.4 3.6 4 8 4s8-1.6 8-4v-2.4C31.2 10.4 28 8 24 8zm0 6c-2.2 0-4-0.9-4-2s1.8-2 4-2s4 0.9 4 2S26.2 14 24 14z" class="logo-fill"/>
      <path d="M20 22c-2 1-3.2 2.8-3.2 4.8v4.8c0 2.4 3.6 4 8 4s8-1.6 8-4v-4.8c0-2-1.2-3.8-3.2-4.8" stroke="white" stroke-width="1.5" fill="none"/>
    </svg>
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
          <p id="netTotal">$0.00</p>
          <small style="color: #999; font-size: 0.8rem;">Total savings if you maintain this rate</small>
        </article>
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
              <small style="color: #666; text-transform: uppercase;">💰 Amount <span class="help-icon" title="How much did you spend or will spend">?</span></small>
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

    <!-- BUDGET HISTORY -->
    <section>
      <h2>Budget History <span class="help-icon" title="View your past budgets and financial snapshots">?</span></h2>
      <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
        <label style="flex: 1; min-width: 200px;">
          <small style="color: #666; text-transform: uppercase;">View History From</small>
          <input type="month" id="historyMonth" value="" />
        </label>
        <button class="btn" id="loadHistoryBtn" style="align-self: flex-end;">Load History</button>
      </div>
      <div id="historyList" style="min-height: 100px;"></div>
    </section>

    <!-- SAVINGS & INVESTMENT ESTIMATOR -->
    <section id="simulator">
      <h2>Savings & Investment Estimator <span class="help-icon" title="Plan how much you'll have by setting savings goals">?</span></h2>
      <p style="color: #666; margin: 0 0 1.5rem 0; font-size: 0.95rem;">See how much you could save or earn through investments based on your monthly contributions.</p>

      <form id="simulateForm" class="estimator-form">
        <div class="estimator-section">
          <h3>📋 Your Plan</h3>

          <label>
            <small>Plan Name <span class="help-icon" title="Give your plan a memorable name">?</span></small>
            <input name="saved_name" placeholder="e.g., Vacation Fund, Emergency Fund" value="My Savings Plan" />
          </label>

          <label>
            <small>How much will you save/invest per month? <span class="help-icon" title="Enter the amount you can save each month">?</span></small>
            <input name="monthly_amount" type="number" step="0.01" placeholder="500" required />
          </label>

          <label>
            <small>How long will you save? <span class="help-icon" title="Enter the number of months">?</span></small>
            <div style="display: flex; gap: 10px;">
              <input name="duration_months" type="number" min="1" value="12" required style="flex: 1;" />
              <select style="flex: 0 0 auto;" onchange="document.querySelector('input[name=duration_months]').value = this.value === '12' ? 12 : this.value === '24' ? 24 : this.value === '60' ? 60 : this.value === '120' ? 120 : this.value">
                <option value="">Select...</option>
                <option value="12">1 Year</option>
                <option value="24">2 Years</option>
                <option value="60">5 Years</option>
                <option value="120">10 Years</option>
              </select>
            </div>
          </label>
        </div>

        <div class="estimator-section">
          <h3>📊 Choose Your Strategy</h3>

          <div class="strategy-selector">
            <label class="strategy-option">
              <input type="radio" name="type" value="save" checked onchange="toggleReturnRate()" />
              <div class="strategy-content">
                <strong>💾 Just Save</strong>
                <small>Regular savings in a savings account</small>
              </div>
            </label>

            <label class="strategy-option">
              <input type="radio" name="type" value="invest" onchange="toggleReturnRate()" />
              <div class="strategy-content">
                <strong>📈 Invest It</strong>
                <small>Invest in stocks/index funds with growth potential</small>
              </div>
            </label>
          </div>

          <label id="returnRateLabel" style="display: none;">
            <small>Expected Annual Return Rate (%) <span class="help-icon" title="Average annual return. Stock market average is ~7-10%">?</span></small>
            <input name="expected_return_rate" type="number" step="0.1" value="7" required />
          </label>
        </div>

        <div class="estimator-actions">
          <button class="btn" type="submit">Calculate Projection</button>
          <button class="btn secondary" id="saveScenario" type="button">Save This Plan</button>
        </div>
      </form>

      <div id="projectionResults" style="margin-top: 1.5rem;"></div>
    </section>

    <!-- SAVED SCENARIOS -->
    <section>
      <h2>Saved Scenarios <span class="help-icon" title="View and manage all your saved financial plans">?</span></h2>
      <p style="color: #666; margin: -1rem 0 1rem 0;">Your financial projections and plans are saved here for future reference and comparison.</p>
      <div class="history" id="scenarioList"></div>
    </section>
  </main>

  <!-- HELP TOOLTIP -->
  <div id="helpTooltip" class="tooltip" style="display: none;"></div>

  <script src="app.js"></script>
</body>
</html>
