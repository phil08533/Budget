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
      <h2>📊 Financial Dashboard</h2>
      <div class="cards" id="dashboard">
        <article class="card">
          <h3>Total Income</h3>
          <p id="incomeTotal">$0.00</p>
          <small style="color: #999; font-size: 0.8rem;">All income sources</small>
        </article>
        <article class="card">
          <h3>Total Expenses</h3>
          <p id="expenseTotal">$0.00</p>
          <small style="color: #999; font-size: 0.8rem;">This period</small>
        </article>
        <article class="card">
          <h3>Monthly Savings</h3>
          <p id="savingsTotal">$0.00</p>
          <small style="color: #999; font-size: 0.8rem;">Income - Expenses</small>
        </article>
        <article class="card">
          <h3>Annual Projection</h3>
          <p id="netTotal">$0.00</p>
          <small style="color: #999; font-size: 0.8rem;">If you maintain this rate</small>
        </article>
      </div>
    </section>

    <!-- INCOME & EXPENSE TRACKING -->
    <section>
      <h2>💸 Budget Tracking</h2>
      <div class="grid-2">
        <!-- ADD INCOME -->
        <div>
          <h2 style="border-bottom: 2px dashed var(--accent); margin-top: 0;">Income Entry</h2>
          <form id="incomeForm" class="stack">
            <label>
              <small style="color: #666; text-transform: uppercase;">Source Name</small>
              <input name="source_name" placeholder="e.g., Salary, Freelance" required />
            </label>
            <label>
              <small style="color: #666; text-transform: uppercase;">Amount ($)</small>
              <input name="amount" type="number" step="0.01" placeholder="0.00" required />
            </label>
            <label>
              <small style="color: #666; text-transform: uppercase;">Frequency</small>
              <select name="frequency">
                <option value="monthly">Monthly</option>
                <option value="weekly">Weekly</option>
                <option value="bi-weekly">Bi-Weekly</option>
                <option value="yearly">Yearly</option>
                <option value="daily">Daily</option>
              </select>
            </label>
            <button class="btn" type="submit">+ Add Income</button>
          </form>
          <div id="incomeList" style="margin-top: 1rem;"></div>
        </div>

        <!-- ADD EXPENSE -->
        <div>
          <h2 style="border-bottom: 2px dashed var(--accent); margin-top: 0;">Expense Entry</h2>
          <form id="expenseForm" class="stack">
            <label>
              <small style="color: #666; text-transform: uppercase;">Category</small>
              <input name="category" placeholder="e.g., Groceries, Rent" required />
            </label>
            <label>
              <small style="color: #666; text-transform: uppercase;">Amount ($)</small>
              <input name="amount" type="number" step="0.01" placeholder="0.00" required />
            </label>
            <label>
              <small style="color: #666; text-transform: uppercase;">Date</small>
              <input name="date" type="date" required />
            </label>
            <button class="btn" type="submit">+ Add Expense</button>
          </form>
          <div id="expenseList" style="margin-top: 1rem;"></div>
        </div>
      </div>
    </section>

    <!-- WHAT-IF SIMULATOR -->
    <section id="simulator">
      <h2>🎯 What-If Simulator</h2>
      <p style="color: #666; margin: -1rem 0 1rem 0;">Test different savings and investment scenarios to see your potential wealth growth.</p>
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
      <h2>📋 Saved Scenarios</h2>
      <p style="color: #666; margin: -1rem 0 1rem 0;">Your financial projections and plans are saved here for future reference.</p>
      <div class="history" id="scenarioList"></div>
    </section>
  </main>

  <script src="app.js"></script>
</body>
</html>
