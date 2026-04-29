<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FutureWorth - Financial Planning</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <header class="hero">
    <h1>💰 FutureWorth</h1>
    <p>See Your Financial Future. Budget smarter. Save faster.</p>
  </header>

  <main class="container">
    <!-- FINANCIAL DASHBOARD -->
    <section>
      <h2>📊 Financial Dashboard <span class="help-icon" title="View your complete financial summary at a glance">?</span></h2>
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
        <article class="card">
          <h3>Monthly Surplus</h3>
          <p id="savingsTotal">$0.00</p>
          <small style="color: #999; font-size: 0.8rem;">Income minus expenses</small>
        </article>
        <article class="card">
          <h3>Annual Projection</h3>
          <p id="netTotal">$0.00</p>
          <small style="color: #999; font-size: 0.8rem;">If you save this amount yearly</small>
        </article>
      </div>
    </section>

    <!-- BUDGET BREAKDOWN -->
    <section>
      <h2>📈 Budget Breakdown <span class="help-icon" title="See your income and expenses organized by frequency">?</span></h2>
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
      <h2>💸 Budget Tracking <span class="help-icon" title="Add your income sources and track your spending here">?</span></h2>
      <div class="grid-2">
        <!-- ADD INCOME -->
        <div>
          <h2 style="border-bottom: 2px dashed var(--accent); margin-top: 0;">Add Income Source</h2>
          <form id="incomeForm" class="stack">
            <label>
              <small style="color: #666; text-transform: uppercase;">💼 Source Name <span class="help-icon" title="Name of your income source (e.g., Salary, Freelance, Bonuses)">?</span></small>
              <input name="source_name" placeholder="e.g., Salary, Freelance, Bonus" required />
            </label>
            <label>
              <small style="color: #666; text-transform: uppercase;">💵 Amount <span class="help-icon" title="How much money per frequency period">?</span></small>
              <input name="amount" type="number" step="0.01" placeholder="0.00" required />
            </label>
            <label>
              <small style="color: #666; text-transform: uppercase;">📅 How Often <span class="help-icon" title="Daily, Weekly, Bi-Weekly, Monthly, or Yearly">?</span></small>
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
              <small style="color: #666; text-transform: uppercase;">🏷️ Category <span class="help-icon" title="What type of expense? (e.g., Groceries, Rent, Entertainment)">?</span></small>
              <input name="category" placeholder="e.g., Groceries, Rent, Transportation" required />
            </label>
            <label>
              <small style="color: #666; text-transform: uppercase;">💰 Amount <span class="help-icon" title="How much did you spend or will spend">?</span></small>
              <input name="amount" type="number" step="0.01" placeholder="0.00" required />
            </label>
            <label>
              <small style="color: #666; text-transform: uppercase;">🔄 Frequency <span class="help-icon" title="Is this a one-time expense or does it repeat?">?</span></small>
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
              <small style="color: #666; text-transform: uppercase;">📆 Date <span class="help-icon" title="When did or will this expense occur">?</span></small>
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
      <h2>📚 Budget History <span class="help-icon" title="View your past budgets and financial snapshots">?</span></h2>
      <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
        <label style="flex: 1; min-width: 200px;">
          <small style="color: #666; text-transform: uppercase;">View History From</small>
          <input type="month" id="historyMonth" value="" />
        </label>
        <button class="btn" id="loadHistoryBtn" style="align-self: flex-end;">Load History</button>
      </div>
      <div id="historyList" style="min-height: 100px;"></div>
    </section>

    <!-- WHAT-IF SIMULATOR -->
    <section id="simulator">
      <h2>🎯 What-If Simulator <span class="help-icon" title="Test different savings and investment scenarios">?</span></h2>
      <p style="color: #666; margin: -1rem 0 1rem 0;">Plan your financial future by testing different scenarios and seeing potential outcomes.</p>
      <form id="simulateForm" class="grid-4">
        <label>
          <small style="color: #666; text-transform: uppercase;">Scenario Name</small>
          <input name="saved_name" placeholder="e.g., Vacation Fund" value="Quick Simulation" />
        </label>
        <label>
          <small style="color: #666; text-transform: uppercase;">Monthly Amount ($)</small>
          <input name="monthly_amount" type="number" step="0.01" placeholder="0.00" required />
        </label>
        <label>
          <small style="color: #666; text-transform: uppercase;">Duration (months)</small>
          <input name="duration_months" type="number" min="1" value="12" required />
        </label>
        <label>
          <small style="color: #666; text-transform: uppercase;">Return Rate (%)</small>
          <input name="expected_return_rate" type="number" step="0.1" value="7" required />
        </label>
        <label>
          <small style="color: #666; text-transform: uppercase;">Simulation Type</small>
          <select name="type">
            <option value="save">💾 Savings Goal</option>
            <option value="invest">📈 Investment Growth</option>
            <option value="hybrid">🔄 Hybrid (Both)</option>
          </select>
        </label>
        <div style="display: flex; gap: 0.5rem;">
          <button class="btn" type="submit" style="flex: 1;">Run Simulation</button>
          <button class="btn secondary" id="saveScenario" type="button" style="flex: 1;">Save</button>
        </div>
      </form>

      <div id="projectionResults" style="margin-top: 1.5rem;"></div>
    </section>

    <!-- SAVED SCENARIOS -->
    <section>
      <h2>📋 Saved Scenarios <span class="help-icon" title="View and manage all your saved financial plans">?</span></h2>
      <p style="color: #666; margin: -1rem 0 1rem 0;">Your financial projections and plans are saved here for future reference and comparison.</p>
      <div class="history" id="scenarioList"></div>
    </section>
  </main>

  <!-- HELP TOOLTIP -->
  <div id="helpTooltip" class="tooltip" style="display: none;"></div>

  <script src="app.js"></script>
</body>
</html>
