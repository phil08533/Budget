const $ = (sel) => document.querySelector(sel);

function logout() {
  if (confirm('Are you sure you want to logout?')) {
    fetch('auth.php?action=logout', { method: 'POST' })
      .then(() => window.location.href = 'login.php')
      .catch(err => alert('Logout error: ' + err.message));
  }
}

function changeTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('futureworth-theme', theme);
  const themeSelect = document.getElementById('themeSelect');
  if (themeSelect) {
    themeSelect.value = theme;
  }
}

function calculateRunway() {
  const savingsInput = document.getElementById('currentSavings');
  const savings = parseFloat(savingsInput.value) || 0;
  const expenseText = document.getElementById('expenseTotal').textContent;
  const monthlyExpenses = parseFloat(expenseText.replace('$', '').replace(/,/g, '')) || 0;
  const resultsDiv = document.getElementById('runwayResults');

  if (savings <= 0) {
    resultsDiv.innerHTML = '<p style="color: #999; text-align: center; padding: 20px;">Enter your current savings to calculate your financial runway</p>';
    return;
  }

  if (monthlyExpenses <= 0) {
    resultsDiv.innerHTML = '<p style="color: #999; text-align: center; padding: 20px;">Add some expenses first to calculate your runway</p>';
    return;
  }

  const months = Math.floor(savings / monthlyExpenses);
  const years = Math.floor(months / 12);
  const remainingMonths = months % 12;
  const weeks = Math.floor((savings % monthlyExpenses) / (monthlyExpenses / 4.33));

  let timeText = '';
  if (years > 0) {
    timeText = `${years} year${years > 1 ? 's' : ''} and ${remainingMonths} month${remainingMonths !== 1 ? 's' : ''}`;
  } else if (months > 0) {
    timeText = `${months} month${months !== 1 ? 's' : ''} and ${weeks} week${weeks !== 1 ? 's' : ''}`;
  } else {
    const days = Math.floor((savings / monthlyExpenses) * 30);
    timeText = `${days} day${days !== 1 ? 's' : ''}`;
  }

  resultsDiv.innerHTML = `
    <div class="cards">
      <article class="card highlight-card">
        <h3>Financial Runway</h3>
        <p style="font-size: 1.8rem;">${timeText}</p>
        <small style="color: #999;">How long you can live off your current savings</small>
      </article>
      <article class="card highlight-card">
        <h3>Monthly Burn Rate</h3>
        <p style="font-size: 1.8rem;">$${monthlyExpenses.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
        <small style="color: #999;">Your current monthly expenses</small>
      </article>
      <article class="card">
        <h3>Savings Left</h3>
        <p style="font-size: 1.8rem;">$${savings.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
        <small style="color: #999;">Your current savings balance</small>
      </article>
    </div>
  `;
}

function money(value) {
  return `$${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

function calculateMonthlyValue(amount, frequency) {
  switch (frequency) {
    case 'daily': return amount * 30.44;
    case 'weekly': return amount * 4.33;
    case 'bi-weekly': return amount * 2.167;
    case 'monthly': return amount;
    case 'yearly': return amount / 12;
    case 'one-time': return 0;
    default: return 0;
  }
}

async function api(action, method = 'GET', body = null) {
  const options = { method, headers: { 'Content-Type': 'application/json' } };
  if (body) options.body = JSON.stringify(body);
  const res = await fetch(`api.php?action=${encodeURIComponent(action)}`, options);
  if (!res.ok) throw new Error((await res.json()).error || 'Request failed');
  return res.json();
}

async function loadDashboard() {
  const data = await api('dashboard');

  // Update summary cards with monthly values
  $('#incomeTotal').textContent = money(data.summary.income_total);
  $('#expenseTotal').textContent = money(data.summary.expense_total);
  $('#savingsTotal').textContent = money(data.summary.savings_total);
  $('#netTotal').textContent = money(data.summary.savings_total);

  // Calculate yearly savings
  const monthlySavings = data.summary.savings_total;
  const yearlySavings = monthlySavings * 12;
  $('#yearlyTotal').textContent = money(yearlySavings);

  // Update savings breakdown
  $('#monthlySavings').textContent = money(monthlySavings) + '/month';
  $('#yearlySavings').textContent = money(yearlySavings) + '/year';

  // Update savings goal display
  updateSavingsGoalDisplay();

  // Calculate and display breakdown by frequency
  const breakdown = { daily: 0, weekly: 0, monthly: 0, yearly: 0 };
  const counts = { daily: 0, weekly: 0, monthly: 0, yearly: 0 };

  data.expenses.forEach((expense) => {
    const monthlyVal = calculateMonthlyValue(expense.amount, expense.frequency);

    if (expense.frequency === 'daily') {
      breakdown.daily += expense.amount;
      counts.daily++;
    } else if (expense.frequency === 'weekly') {
      breakdown.weekly += expense.amount;
      counts.weekly++;
    } else if (expense.frequency === 'monthly' || expense.frequency === 'bi-weekly') {
      breakdown.monthly += monthlyVal;
      counts.monthly++;
    } else if (expense.frequency === 'yearly') {
      breakdown.yearly += expense.amount;
      counts.yearly++;
    }
  });

  $('#dailyExpenses').textContent = money(breakdown.daily) + '/day';
  $('#dailyExpenseCount').textContent = counts.daily + (counts.daily === 1 ? ' item' : ' items');
  $('#weeklyExpenses').textContent = money(breakdown.weekly) + '/week';
  $('#weeklyExpenseCount').textContent = counts.weekly + (counts.weekly === 1 ? ' item' : ' items');
  $('#monthlyExpenses').textContent = money(breakdown.monthly) + '/month';
  $('#monthlyExpenseCount').textContent = counts.monthly + (counts.monthly === 1 ? ' item' : ' items');
  $('#yearlyExpenses').textContent = money(breakdown.yearly) + '/year';
  $('#yearlyExpenseCount').textContent = counts.yearly + (counts.yearly === 1 ? ' item' : ' items');

  // Render income list
  const incomeList = $('#incomeList');
  incomeList.innerHTML = '';
  if (data.income.length) {
    data.income.forEach((item) => {
      const monthlyVal = calculateMonthlyValue(item.amount, item.frequency);
      const div = document.createElement('div');
      div.className = 'history-item';
      div.innerHTML = `
        <div>
          <h4 style="margin: 0;">${item.source_name}</h4>
          <p style="margin: 0.25rem 0 0 0; color: #666;">${item.frequency}</p>
        </div>
        <div style="text-align: right;">
          <div style="font-weight: bold; color: var(--xp-accent);">${money(item.amount)} (${money(monthlyVal)}/mo)</div>
          <button class="btn" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; margin-top: 0.5rem;" data-delete-income="${item.income_id}">Delete</button>
        </div>
      `;
      incomeList.appendChild(div);
    });
  } else {
    incomeList.innerHTML = '<p style="color: #999; text-align: center;">No income sources yet</p>';
  }

  // Render expense list
  const expenseList = $('#expenseList');
  expenseList.innerHTML = '';
  if (data.expenses.length) {
    data.expenses.forEach((item) => {
      const monthlyVal = calculateMonthlyValue(item.amount, item.frequency);
      const div = document.createElement('div');
      div.className = 'history-item';
      div.innerHTML = `
        <div>
          <h4 style="margin: 0;">${item.category}</h4>
          <p style="margin: 0.25rem 0 0 0; color: #666;">${item.frequency} • ${item.date}</p>
        </div>
        <div style="text-align: right;">
          <div style="font-weight: bold; color: var(--xp-accent);">${money(item.amount)} (${money(monthlyVal)}/mo)</div>
          <button class="btn" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; margin-top: 0.5rem;" data-delete-expense="${item.expense_id}">Delete</button>
        </div>
      `;
      expenseList.appendChild(div);
    });
  } else {
    expenseList.innerHTML = '<p style="color: #999; text-align: center;">No expenses yet</p>';
  }

  // Update budget list
  loadBudgets();
}

// Help tooltip system
document.addEventListener('mouseover', (e) => {
  const helpIcon = e.target.closest('.help-icon');
  if (!helpIcon) return;
  const tooltip = $('#helpTooltip');
  tooltip.textContent = helpIcon.getAttribute('title');
  tooltip.style.display = 'block';
  const rect = helpIcon.getBoundingClientRect();
  tooltip.style.left = (rect.left + rect.width / 2 - 100) + 'px';
  tooltip.style.top = (rect.top - 40) + 'px';
});

document.addEventListener('mouseout', (e) => {
  if (e.target.closest('.help-icon')) {
    $('#helpTooltip').style.display = 'none';
  }
});

// Initialize theme and setup
document.addEventListener('DOMContentLoaded', () => {
  const savedTheme = localStorage.getItem('futureworth-theme') || 'blue';
  document.documentElement.setAttribute('data-theme', savedTheme);
  const themeSelect = document.getElementById('themeSelect');
  if (themeSelect) {
    themeSelect.value = savedTheme;
  }

  const expenseDateInput = document.getElementById('expenseDate');
  if (expenseDateInput) {
    expenseDateInput.valueAsDate = new Date();
  }
});

$('#incomeForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const form = new FormData(e.target);
    await api('income', 'POST', Object.fromEntries(form));
    e.target.reset();
    await loadDashboard();
  } catch (err) {
    alert('Error adding income: ' + err.message);
  }
});

$('#expenseForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const form = new FormData(e.target);
    if (!form.get('date')) {
      form.set('date', new Date().toISOString().split('T')[0]);
    }
    await api('expense', 'POST', Object.fromEntries(form));
    e.target.reset();
    e.target.querySelector('input[name="date"]').valueAsDate = new Date();
    await loadDashboard();
  } catch (err) {
    alert('Error adding expense: ' + err.message);
  }
});

// Budgets (save/load budget snapshots)
async function saveBudget() {
  const name = prompt('Budget name:');
  if (!name) return;

  const data = await api('dashboard');
  const budgetData = {
    name: name,
    income: data.income,
    expenses: data.expenses,
    summary: data.summary,
    date: new Date().toISOString().split('T')[0]
  };

  try {
    await api('budget', 'POST', budgetData);
    alert('Budget saved!');
    await loadBudgets();
  } catch (err) {
    alert('Error saving budget: ' + err.message);
  }
}

async function loadBudgets() {
  try {
    const data = await api('budgets', 'GET');
    const list = $('#budgetList');
    list.innerHTML = '';

    if (!data.budgets || data.budgets.length === 0) {
      list.innerHTML = '<p style="color: #999; text-align: center;">No saved budgets. Create one to compare.</p>';
      return;
    }

    data.budgets.forEach((budget) => {
      const item = document.createElement('div');
      item.className = 'history-item';
      item.innerHTML = `
        <div>
          <h4 style="margin: 0;">${budget.budget_name}</h4>
          <p style="margin: 0.25rem 0 0 0; color: #666;">Income: ${money(budget.total_income)} | Expenses: ${money(budget.total_expenses)} | Savings: ${money(budget.monthly_savings)} (${budget.created_at})</p>
        </div>
        <button class="btn" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; background: #c84b4b; border-color: #c84b4b;" data-delete-budget="${budget.budget_id}">Delete</button>
      `;
      list.appendChild(item);
    });
  } catch (err) {
    console.error('Error loading budgets:', err);
  }
}

// Event delegation for deletions
document.addEventListener('click', async (e) => {
  const deleteIncomeId = e.target.dataset.deleteIncome;
  const deleteExpenseId = e.target.dataset.deleteExpense;
  const deleteBudgetId = e.target.dataset.deleteBudget;

  if (deleteIncomeId && confirm('Delete this income source?')) {
    try {
      await api('income', 'DELETE', { income_id: deleteIncomeId });
      await loadDashboard();
    } catch (err) {
      alert('Error: ' + err.message);
    }
  }

  if (deleteExpenseId && confirm('Delete this expense?')) {
    try {
      await api('expense', 'DELETE', { expense_id: deleteExpenseId });
      await loadDashboard();
    } catch (err) {
      alert('Error: ' + err.message);
    }
  }

  if (deleteBudgetId && confirm('Delete this budget?')) {
    try {
      await api('budget', 'DELETE', { budget_id: deleteBudgetId });
      await loadBudgets();
    } catch (err) {
      alert('Error: ' + err.message);
    }
  }
});

// Savings goal functions
function saveSavingsGoal() {
  const goal = parseFloat(document.getElementById('savingsGoal').value) || 0;
  localStorage.setItem('runway-savings-goal', goal);
  updateSavingsGoalDisplay();
}

function updateSavingsGoalDisplay() {
  const goal = parseFloat(localStorage.getItem('runway-savings-goal')) || 0;
  const actual = parseFloat($('#savingsTotal').textContent.replace('$', '').replace(/,/g, '')) || 0;
  const progress = goal > 0 ? ((actual / goal) * 100).toFixed(1) : 0;

  $('#savingsGoalAmount').textContent = money(goal);
  $('#savingsActualAmount').textContent = money(actual);
  $('#savingsProgress').textContent = Math.min(progress, 100) + '%';
}

loadDashboard().catch((err) => {
  console.error(err);
  alert('Unable to load dashboard. Check that:\n1. PHP server is running\n2. MySQL database is set up\n3. Database credentials in db.php are correct');
});

// Update savings goal display on dashboard load
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(updateSavingsGoalDisplay, 500);
});
