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
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#0a5fb5" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
  <meta name="apple-mobile-web-app-title" content="Runway" />
  <meta name="description" content="A simple financial planning and budgeting app" />
  <link rel="manifest" href="manifest.json" />
  <link rel="apple-touch-icon" href="data:image/svg+xml,%3Csvg width='180' height='180' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Crect fill='%230a5fb5' width='40' height='40'/%3E%3Cpath d='M20 2C20 2 10 10 10 20C10 27.7 15 35 20 35C25 35 30 27.7 30 20C30 10 20 2 20 2Z' fill='white'/%3E%3Ccircle cx='20' cy='20' r='6' fill='%230a5fb5'/%3E%3Cpath d='M12 28C12 28 8 32 5 35M28 28C28 28 32 32 35 35' stroke='white' stroke-width='2' fill='none'/%3E%3C/svg%3E" />
  <title>Runway - Financial Planning</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    .unified-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: linear-gradient(135deg, #0a5fb5 0%, #0a246a 100%);
      color: white;
      padding: 20px 30px;
      border-radius: 0;
      margin: -20px -20px 30px -20px;
    }
    .header-left {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .header-logo {
      font-size: 2.5rem;
      font-weight: 700;
    }
    .header-title {
      margin: 0;
    }
    .header-right {
      display: flex;
      gap: 15px;
      align-items: center;
    }
    .user-greeting {
      color: white;
      font-size: 0.9rem;
    }
    .logout-btn {
      background: #cc3333;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.85rem;
      transition: all 0.2s ease;
    }
    .logout-btn:hover {
      background: #bb2222;
      transform: translateY(-1px);
    }
    #themeSelect {
      padding: 6px 12px;
      border-radius: 6px;
      border: none;
      background: rgba(255,255,255,0.95);
      cursor: pointer;
      font-weight: 600;
    }
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 20px;
    }
    @media (min-width: 1200px) {
      .dashboard-grid {
        grid-template-columns: repeat(4, 1fr);
      }
    }
    .tabs-container {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      border-bottom: 2px solid #ddd;
    }
    .tab-btn {
      background: none;
      border: none;
      padding: 12px 20px;
      cursor: pointer;
      font-weight: 600;
      color: #999;
      border-bottom: 3px solid transparent;
      transition: all 0.2s ease;
    }
    .tab-btn.active {
      color: var(--xp-accent);
      border-bottom-color: var(--xp-accent);
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
  </style>
</head>
<body>
  <div class="unified-header">
    <div class="header-left">
      <svg width="40" height="40" viewBox="0 0 40 40" style="fill: white;">
        <path d="M20 2C20 2 10 10 10 20C10 27.7 15 35 20 35C25 35 30 27.7 30 20C30 10 20 2 20 2Z" fill="white"/>
        <circle cx="20" cy="20" r="6" fill="#0a5fb5"/>
        <path d="M12 28C12 28 8 32 5 35M28 28C28 28 32 32 35 35" stroke="white" stroke-width="2" fill="none"/>
      </svg>
      <h1 class="header-title" style="margin: 0; color: white; font-size: 1.8rem;">Runway</h1>
    </div>
    <div class="header-right">
      <select id="themeSelect" onchange="changeTheme(this.value)">
        <option value="blue">Blue</option>
        <option value="sunset">Sunset</option>
        <option value="ocean">Ocean</option>
        <option value="purple">Purple</option>
        <option value="forest">Forest</option>
        <option value="rose">Rose</option>
        <option value="dark">Dark</option>
      </select>
      <span class="user-greeting">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
      <button class="logout-btn" onclick="logout()">Logout</button>
    </div>
  </div>

  <main class="container">
    <!-- FINANCIAL OVERVIEW DASHBOARD -->
    <section>
      <h2>Financial Overview <span class="help-icon" title="Complete summary of your income, expenses, and savings">?</span></h2>
      <div class="dashboard-grid">
        <article class="card">
          <h3>Monthly Income</h3>
          <p id="incomeTotal">$0.00</p>
          <small>All income sources</small>
        </article>
        <article class="card">
          <h3>Monthly Expenses</h3>
          <p id="expenseTotal">$0.00</p>
          <small>Total spending</small>
        </article>
        <article class="card highlight-card">
          <h3>Monthly Income After Expenses</h3>
          <p id="savingsTotal">$0.00</p>
          <small>Income minus expenses</small>
        </article>
        <article class="card highlight-card">
          <h3>Annual Income</h3>
          <p id="yearlyTotal">$0.00</p>
          <small>Yearly total</small>
        </article>
        <article class="card">
          <h3>Total Monthly Savings Goals</h3>
          <p id="overviewMonthlySavings">$0.00</p>
          <small>All savings goals</small>
        </article>
        <article class="card highlight-card">
          <h3>Annual Savings</h3>
          <p id="overviewAnnualSavings">$0.00</p>
          <small>Yearly savings with gains</small>
        </article>
      </div>

    </section>

    <!-- CURRENT SAVINGS & RUNWAY -->
    <section>
      <h2>Financial Runway <span class="help-icon" title="How long can you live off your savings?">?</span></h2>
      <p style="color: #666; margin: 0 0 1.5rem 0; font-size: 0.95rem;">Enter your current savings to see how many months you can survive on them.</p>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
        <div>
          <label>
            <small style="color: var(--xp-accent); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Current Savings ($)</small>
            <input type="number" id="currentSavings" step="0.01" placeholder="0.00" value="0" style="padding: 12px; border: 1px solid var(--xp-border); border-radius: 6px; font-size: 0.95rem; width: 100%;" onchange="calculateRunway()" oninput="calculateRunway()" />
          </label>
        </div>
        <div style="display: flex; align-items: flex-end;">
          <button class="btn" type="button" onclick="calculateRunway()" style="width: 100%; padding: 12px;">Calculate Runway</button>
        </div>
      </div>

      <div id="runwayResults"></div>
    </section>

    <!-- BUDGET TRACKING WITH TABS -->
    <section>
      <h2>Budget Tracking <span class="help-icon" title="Add and manage your income, expenses, and savings goals">?</span></h2>

      <div class="tabs-container">
        <button class="tab-btn active" onclick="switchTab('income-tab')">Income</button>
        <button class="tab-btn" onclick="switchTab('expense-tab')">Expenses</button>
        <button class="tab-btn" onclick="switchTab('savings-tab')">Monthly Savings Goal</button>
      </div>

      <!-- INCOME TAB -->
      <div id="income-tab" class="tab-content active">
        <div class="grid-2">
          <div>
            <h3 style="margin-top: 0; border-bottom: 2px solid var(--xp-accent); padding-bottom: 10px;">Add Income Source</h3>
            <form id="incomeForm" class="stack">
              <label>
                <small style="color: #666; text-transform: uppercase;">Source Name</small>
                <input name="source_name" placeholder="e.g., Salary, Freelance, Bonus" required />
              </label>
              <label>
                <small style="color: #666; text-transform: uppercase;">Amount</small>
                <input name="amount" type="number" step="0.01" placeholder="0.00" required />
              </label>
              <label>
                <small style="color: #666; text-transform: uppercase;">Frequency</small>
                <select name="frequency">
                  <option value="daily">Daily</option>
                  <option value="weekly">Weekly</option>
                  <option value="bi-weekly">Bi-Weekly</option>
                  <option value="monthly" selected>Monthly</option>
                  <option value="yearly">Yearly</option>
                </select>
              </label>
              <button class="btn" type="submit">Add Income</button>
            </form>
          </div>
          <div>
            <h3 style="margin-top: 0; border-bottom: 2px solid var(--xp-accent); padding-bottom: 10px;">Your Income Sources</h3>
            <div id="incomeList" style="min-height: 100px;"></div>
          </div>
        </div>
      </div>

      <!-- EXPENSE TAB -->
      <div id="expense-tab" class="tab-content">
        <div class="grid-2">
          <div>
            <h3 style="margin-top: 0; border-bottom: 2px solid var(--xp-accent); padding-bottom: 10px;">Add Expense</h3>
            <form id="expenseForm" class="stack">
              <label>
                <small style="color: #666; text-transform: uppercase;">Category</small>
                <input name="category" placeholder="e.g., Groceries, Rent, Transportation" required />
              </label>
              <label>
                <small style="color: #666; text-transform: uppercase;">Amount</small>
                <input name="amount" type="number" step="0.01" placeholder="0.00" required />
              </label>
              <label>
                <small style="color: #666; text-transform: uppercase;">Frequency</small>
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
                <small style="color: #666; text-transform: uppercase;">Date</small>
                <input id="expenseDate" name="date" type="date" required />
              </label>
              <button class="btn" type="submit">Add Expense</button>
            </form>
          </div>
          <div>
            <h3 style="margin-top: 0; border-bottom: 2px solid var(--xp-accent); padding-bottom: 10px;">Your Expenses</h3>
            <div id="expenseList" style="min-height: 100px;"></div>
          </div>
        </div>
      </div>

      <!-- SAVINGS GOALS TAB -->
      <div id="savings-tab" class="tab-content">
        <div class="grid-2">
          <div>
            <h3 style="margin-top: 0; border-bottom: 2px solid var(--xp-accent); padding-bottom: 10px;">Add Savings Goal</h3>
            <form id="savingsGoalForm" class="stack">
              <label>
                <small style="color: #666; text-transform: uppercase;">Goal Name</small>
                <input name="goal_name" placeholder="e.g., Emergency Fund, Vacation" required />
              </label>
              <label>
                <small style="color: #666; text-transform: uppercase;">Monthly Amount</small>
                <input name="amount" type="number" step="0.01" placeholder="0.00" required />
              </label>
              <label style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="hasGain" id="hasGainCheckbox" onchange="toggleGainInput()" />
                <small style="color: #666; text-transform: uppercase;">Include annual gain %</small>
              </label>
              <label id="gainLabel" style="display: none;">
                <small style="color: #666; text-transform: uppercase;">Annual Gain %</small>
                <input name="gain" type="number" step="0.01" placeholder="0.00" />
              </label>
              <button class="btn" type="submit">Add Goal</button>
            </form>
          </div>
          <div>
            <h3 style="margin-top: 0; border-bottom: 2px solid var(--xp-accent); padding-bottom: 10px;">Your Savings Goals</h3>
            <div id="savingsGoalsList" style="min-height: 100px;"></div>
          </div>
        </div>

        <div style="margin-top: 2rem;">
          <div class="cards">
            <article class="card">
              <h3>Total Monthly Savings</h3>
              <p id="totalMonthlySavings" style="color: var(--xp-accent); font-weight: bold; font-size: 1.8rem;">$0.00</p>
              <small>Sum of all goals</small>
            </article>
            <article class="card highlight-card">
              <h3>Annual Savings</h3>
              <p id="totalAnnualSavings" style="color: var(--xp-accent); font-weight: bold; font-size: 1.8rem;">$0.00</p>
              <small>Yearly total with gains</small>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- SAVED BUDGETS -->
    <section>
      <h2>Saved Budgets <span class="help-icon" title="Save and compare your budget snapshots">?</span></h2>
      <p style="color: #666; margin: 0 0 1.5rem 0; font-size: 0.95rem;">Save your current budget to compare different scenarios.</p>
      <button class="btn" style="margin-bottom: 1.5rem;" onclick="saveBudget()">Save Current Budget</button>
      <div class="history" id="budgetList"></div>
    </section>
  </main>

  <!-- BUDGET DETAIL MODAL -->
  <div id="budgetModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; overflow: auto;">
    <div style="background: white; margin: 50px auto; padding: 30px; border-radius: 12px; max-width: 600px; position: relative;">
      <button onclick="closeBudgetModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 1.5rem; cursor: pointer;">✕</button>
      <h2 id="budgetModalTitle">Budget Details</h2>
      <div id="budgetModalContent"></div>
    </div>
  </div>

  <!-- HELP TOOLTIP -->
  <div id="helpTooltip" class="tooltip" style="display: none;"></div>

  <!-- EDIT INCOME MODAL -->
  <div id="editIncomeModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; overflow: auto;">
    <div style="background: white; margin: 50px auto; padding: 30px; border-radius: 12px; max-width: 400px;">
      <h2>Edit Income</h2>
      <form id="editIncomeForm" class="stack">
        <input type="hidden" id="editIncomeId" />
        <label>
          <small style="color: #666; text-transform: uppercase;">Source Name</small>
          <input id="editIncomeName" placeholder="e.g., Salary" required />
        </label>
        <label>
          <small style="color: #666; text-transform: uppercase;">Amount</small>
          <input id="editIncomeAmount" type="number" step="0.01" placeholder="0.00" required />
        </label>
        <label>
          <small style="color: #666; text-transform: uppercase;">Frequency</small>
          <select id="editIncomeFrequency">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="bi-weekly">Bi-Weekly</option>
            <option value="monthly" selected>Monthly</option>
            <option value="yearly">Yearly</option>
          </select>
        </label>
        <button class="btn" type="submit">Save Changes</button>
        <button class="btn" type="button" onclick="closeEditIncomeModal()" style="background: #999;">Cancel</button>
      </form>
    </div>
  </div>

  <!-- EDIT EXPENSE MODAL -->
  <div id="editExpenseModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; overflow: auto;">
    <div style="background: white; margin: 50px auto; padding: 30px; border-radius: 12px; max-width: 400px;">
      <h2>Edit Expense</h2>
      <form id="editExpenseForm" class="stack">
        <input type="hidden" id="editExpenseId" />
        <label>
          <small style="color: #666; text-transform: uppercase;">Category</small>
          <input id="editExpenseCategory" placeholder="e.g., Groceries" required />
        </label>
        <label>
          <small style="color: #666; text-transform: uppercase;">Amount</small>
          <input id="editExpenseAmount" type="number" step="0.01" placeholder="0.00" required />
        </label>
        <label>
          <small style="color: #666; text-transform: uppercase;">Frequency</small>
          <select id="editExpenseFrequency">
            <option value="one-time" selected>One-Time</option>
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="bi-weekly">Bi-Weekly</option>
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly</option>
          </select>
        </label>
        <label>
          <small style="color: #666; text-transform: uppercase;">Date</small>
          <input id="editExpenseDate" type="date" required />
        </label>
        <button class="btn" type="submit">Save Changes</button>
        <button class="btn" type="button" onclick="closeEditExpenseModal()" style="background: #999;">Cancel</button>
      </form>
    </div>
  </div>

  <!-- EDIT SAVINGS GOAL MODAL -->
  <div id="editSavingsGoalModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; overflow: auto;">
    <div style="background: white; margin: 50px auto; padding: 30px; border-radius: 12px; max-width: 400px;">
      <h2>Edit Savings Goal</h2>
      <form id="editSavingsGoalForm" class="stack">
        <input type="hidden" id="editSavingsGoalId" />
        <label>
          <small style="color: #666; text-transform: uppercase;">Goal Name</small>
          <input id="editSavingsGoalName" placeholder="e.g., Emergency Fund" required />
        </label>
        <label>
          <small style="color: #666; text-transform: uppercase;">Monthly Amount</small>
          <input id="editSavingsGoalAmount" type="number" step="0.01" placeholder="0.00" required />
        </label>
        <label style="display: flex; align-items: center; gap: 8px;">
          <input type="checkbox" id="editSavingsGoalHasGain" onchange="toggleEditGainInput()" />
          <small style="color: #666; text-transform: uppercase;">Include annual gain %</small>
        </label>
        <label id="editGainLabel" style="display: none;">
          <small style="color: #666; text-transform: uppercase;">Annual Gain %</small>
          <input id="editSavingsGoalGain" type="number" step="0.01" placeholder="0.00" />
        </label>
        <button class="btn" type="submit">Save Changes</button>
        <button class="btn" type="button" onclick="closeEditSavingsGoalModal()" style="background: #999;">Cancel</button>
      </form>
    </div>
  </div>

  <script>
    function switchTab(tabId) {
      // Hide all tabs
      const tabs = document.querySelectorAll('.tab-content');
      tabs.forEach(tab => tab.classList.remove('active'));

      // Remove active from all buttons
      const btns = document.querySelectorAll('.tab-btn');
      btns.forEach(btn => btn.classList.remove('active'));

      // Show selected tab
      document.getElementById(tabId).classList.add('active');

      // Add active to button
      event.target.classList.add('active');
    }
  </script>
  <script src="app.js"></script>
  <script>
    // Register service worker for PWA functionality
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('sw.js')
        .then(registration => console.log('Service Worker registered'))
        .catch(err => console.log('Service Worker registration failed:', err));
    }
  </script>
</body>
</html>
