<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FutureWorth</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <header class="hero">
    <h1>See Your Financial Future</h1>
    <p>Budget smarter. Save faster. Predict your wealth.</p>
    <a href="#simulator" class="btn">Start Simulating</a>
  </header>

  <main class="container">
    <section class="cards" id="dashboard">
      <article class="card"><h3>Income</h3><p id="incomeTotal">$0.00</p></article>
      <article class="card"><h3>Expenses</h3><p id="expenseTotal">$0.00</p></article>
      <article class="card"><h3>Savings</h3><p id="savingsTotal">$0.00</p></article>
      <article class="card"><h3>Net Monthly</h3><p id="netTotal">$0.00</p></article>
    </section>

    <section class="grid-2">
      <section>
        <h2>Add Income</h2>
        <form id="incomeForm" class="stack">
          <input name="source_name" placeholder="Source name" required />
          <input name="amount" type="number" step="0.01" placeholder="Amount" required />
          <select name="frequency">
            <option value="monthly">Monthly</option>
            <option value="weekly">Weekly</option>
            <option value="yearly">Yearly</option>
          </select>
          <button class="btn" type="submit">Add Income</button>
        </form>
      </section>

      <section>
        <h2>Add Expense</h2>
        <form id="expenseForm" class="stack">
          <input name="category" placeholder="Category" required />
          <input name="amount" type="number" step="0.01" placeholder="Amount" required />
          <input name="date" type="date" required />
          <button class="btn" type="submit">Add Expense</button>
        </form>
      </section>
    </section>

    <section id="simulator">
      <h2>What If Simulator</h2>
      <form id="simulateForm" class="grid-4">
        <input name="saved_name" placeholder="Scenario name" value="Quick Simulation" />
        <input name="monthly_amount" type="number" step="0.01" placeholder="Monthly amount" required />
        <input name="duration_months" type="number" min="1" placeholder="Duration months" value="12" required />
        <input name="expected_return_rate" type="number" step="0.01" placeholder="Return rate %" value="7" required />
        <select name="type">
          <option value="save">Save</option>
          <option value="invest">Invest</option>
          <option value="hybrid">Hybrid</option>
        </select>
        <button class="btn" type="submit">Run Simulation</button>
        <button class="btn secondary" id="saveScenario" type="button">Save Scenario</button>
      </form>

      <div class="cards projection">
        <article class="card"><h3>Projected Savings</h3><p id="projectedSavings">$0.00</p></article>
        <article class="card"><h3>Projected Investment</h3><p id="projectedInvestment">$0.00</p></article>
      </div>
    </section>

    <section>
      <h2>History</h2>
      <div class="history" id="scenarioList"></div>
    </section>
  </main>

  <script src="app.js"></script>
</body>
</html>
