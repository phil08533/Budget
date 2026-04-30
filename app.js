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

  // Calculate yearly savings
  const monthlySavings = data.summary.savings_total;
  const yearlySavings = monthlySavings * 12;
  $('#yearlyTotal').textContent = money(yearlySavings);

  // Load savings goals
  loadSavingsGoals();


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
          <div style="display: flex; gap: 0.3rem;">
            <button class="btn" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; margin-top: 0.5rem;" data-edit-income="${item.income_id}">Edit</button>
            <button class="btn" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; margin-top: 0.5rem; background: #c84b4b; border-color: #c84b4b;" data-delete-income="${item.income_id}">Delete</button>
          </div>
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
          <div style="display: flex; gap: 0.3rem;">
            <button class="btn" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; margin-top: 0.5rem;" data-edit-expense="${item.expense_id}">Edit</button>
            <button class="btn" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; margin-top: 0.5rem; background: #c84b4b; border-color: #c84b4b;" data-delete-expense="${item.expense_id}">Delete</button>
          </div>
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
        <div style="display: flex; gap: 0.5rem;">
          <button class="btn" style="font-size: 0.75rem; padding: 0.4rem 0.8rem;" data-view-budget="${budget.budget_id}">View</button>
          <button class="btn" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; background: #c84b4b; border-color: #c84b4b;" data-delete-budget="${budget.budget_id}">Delete</button>
        </div>
      `;
      list.appendChild(item);
    });
  } catch (err) {
    console.error('Error loading budgets:', err);
  }
}

// Event delegation for deletions and budget viewing
document.addEventListener('click', async (e) => {
  const deleteIncomeId = e.target.dataset.deleteIncome;
  const deleteExpenseId = e.target.dataset.deleteExpense;
  const deleteBudgetId = e.target.dataset.deleteBudget;
  const viewBudgetId = e.target.dataset.viewBudget;

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

  if (viewBudgetId) {
    viewBudgetDetails(viewBudgetId);
  }
});

// Savings goals functions
function toggleGainInput() {
  const checkbox = document.getElementById('hasGainCheckbox');
  const gainLabel = document.getElementById('gainLabel');
  gainLabel.style.display = checkbox.checked ? 'flex' : 'none';
}

$('#savingsGoalForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const form = new FormData(e.target);
    const data = Object.fromEntries(form);
    const goal = {
      id: Date.now(),
      name: data.goal_name,
      amount: parseFloat(data.amount) || 0,
      gain: data.hasGain === 'on' ? (parseFloat(data.gain) || 0) : 0
    };

    let goals = JSON.parse(localStorage.getItem('savings-goals') || '[]');
    goals.push(goal);
    localStorage.setItem('savings-goals', JSON.stringify(goals));

    e.target.reset();
    document.getElementById('hasGainCheckbox').checked = false;
    document.getElementById('gainLabel').style.display = 'none';
    loadSavingsGoals();
  } catch (err) {
    alert('Error adding savings goal: ' + err.message);
  }
});

function loadSavingsGoals() {
  const goals = JSON.parse(localStorage.getItem('savings-goals') || '[]');
  const list = $('#savingsGoalsList');
  list.innerHTML = '';

  if (goals.length === 0) {
    list.innerHTML = '<p style="color: #999; text-align: center;">No savings goals yet</p>';
  } else {
    goals.forEach((goal) => {
      const yearlyBase = goal.amount * 12;
      const interest = yearlyBase * (goal.gain / 100);
      const yearlyTotal = yearlyBase + interest;

      const div = document.createElement('div');
      div.className = 'history-item';
      div.innerHTML = `
        <div>
          <h4 style="margin: 0;">${goal.name}</h4>
          <p style="margin: 0.25rem 0 0 0; color: var(--xp-accent); font-weight: bold;">${money(goal.amount)}/month</p>
          <p style="margin: 0.25rem 0 0 0; color: #666; font-size: 0.85rem;">
            Yearly: ${money(yearlyBase)}${goal.gain > 0 ? ` + ${money(interest)} interest (${goal.gain}%)` : ''} = ${money(yearlyTotal)}
          </p>
        </div>
        <div style="text-align: right;">
          <button class="btn" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; margin-bottom: 0.5rem;" data-edit-goal="${goal.id}">Edit</button>
          <button class="btn" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; background: #c84b4b; border-color: #c84b4b;" data-delete-goal="${goal.id}">Delete</button>
        </div>
      `;
      list.appendChild(div);
    });
  }

  updateSavingsGoalsSummary();
}

function updateSavingsGoalsSummary() {
  const goals = JSON.parse(localStorage.getItem('savings-goals') || '[]');

  let totalMonthly = 0;
  let totalAnnual = 0;

  goals.forEach((goal) => {
    totalMonthly += goal.amount;
    const yearlyBase = goal.amount * 12;
    const gainAmount = yearlyBase * (goal.gain / 100);
    totalAnnual += yearlyBase + gainAmount;
  });

  $('#totalMonthlySavings').textContent = money(totalMonthly);
  $('#totalAnnualSavings').textContent = money(totalAnnual);

  // Update overview cards
  if ($('#overviewMonthlySavings')) {
    $('#overviewMonthlySavings').textContent = money(totalMonthly);
    $('#overviewAnnualSavings').textContent = money(totalAnnual);
  }
}

// Edit/Delete event delegation
document.addEventListener('click', async (e) => {
  const deleteGoalId = e.target.dataset.deleteGoal;
  const editGoalId = e.target.dataset.editGoal;
  const editIncomeId = e.target.dataset.editIncome;
  const editExpenseId = e.target.dataset.editExpense;

  if (deleteGoalId && confirm('Delete this savings goal?')) {
    let goals = JSON.parse(localStorage.getItem('savings-goals') || '[]');
    goals = goals.filter(g => g.id != deleteGoalId);
    localStorage.setItem('savings-goals', JSON.stringify(goals));
    loadSavingsGoals();
  }

  if (editGoalId) {
    let goals = JSON.parse(localStorage.getItem('savings-goals') || '[]');
    const goal = goals.find(g => g.id == editGoalId);
    if (goal) {
      document.getElementById('editSavingsGoalId').value = goal.id;
      document.getElementById('editSavingsGoalName').value = goal.name;
      document.getElementById('editSavingsGoalAmount').value = goal.amount;
      document.getElementById('editSavingsGoalHasGain').checked = goal.gain > 0;
      document.getElementById('editSavingsGoalGain').value = goal.gain;
      document.getElementById('editGainLabel').style.display = goal.gain > 0 ? 'flex' : 'none';
      document.getElementById('editSavingsGoalModal').style.display = 'block';
    }
  }

  if (editIncomeId) {
    const data = await api('dashboard');
    const income = data.income.find(i => i.income_id == editIncomeId);
    if (income) {
      document.getElementById('editIncomeId').value = income.income_id;
      document.getElementById('editIncomeName').value = income.source_name;
      document.getElementById('editIncomeAmount').value = income.amount;
      document.getElementById('editIncomeFrequency').value = income.frequency;
      document.getElementById('editIncomeModal').style.display = 'block';
    }
  }

  if (editExpenseId) {
    const data = await api('dashboard');
    const expense = data.expenses.find(e => e.expense_id == editExpenseId);
    if (expense) {
      document.getElementById('editExpenseId').value = expense.expense_id;
      document.getElementById('editExpenseCategory').value = expense.category;
      document.getElementById('editExpenseAmount').value = expense.amount;
      document.getElementById('editExpenseFrequency').value = expense.frequency;
      document.getElementById('editExpenseDate').value = expense.date;
      document.getElementById('editExpenseModal').style.display = 'block';
    }
  }
});

// Edit modal functions
function closeEditIncomeModal() {
  document.getElementById('editIncomeModal').style.display = 'none';
}

function closeEditExpenseModal() {
  document.getElementById('editExpenseModal').style.display = 'none';
}

function closeEditSavingsGoalModal() {
  document.getElementById('editSavingsGoalModal').style.display = 'none';
}

function toggleEditGainInput() {
  const checkbox = document.getElementById('editSavingsGoalHasGain');
  const gainLabel = document.getElementById('editGainLabel');
  gainLabel.style.display = checkbox.checked ? 'flex' : 'none';
}

// Edit form submissions
document.getElementById('editIncomeForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const incomeId = parseInt(document.getElementById('editIncomeId').value);
    const sourceName = document.getElementById('editIncomeName').value;
    const amount = parseFloat(document.getElementById('editIncomeAmount').value);
    const frequency = document.getElementById('editIncomeFrequency').value;

    await api('income', 'PUT', { income_id: incomeId, source_name: sourceName, amount, frequency });
    closeEditIncomeModal();
    await loadDashboard();
  } catch (err) {
    alert('Error saving income: ' + err.message);
  }
});

document.getElementById('editExpenseForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const expenseId = parseInt(document.getElementById('editExpenseId').value);
    const category = document.getElementById('editExpenseCategory').value;
    const amount = parseFloat(document.getElementById('editExpenseAmount').value);
    const frequency = document.getElementById('editExpenseFrequency').value;
    const date = document.getElementById('editExpenseDate').value;

    await api('expense', 'PUT', { expense_id: expenseId, category, amount, frequency, date });
    closeEditExpenseModal();
    await loadDashboard();
  } catch (err) {
    alert('Error saving expense: ' + err.message);
  }
});

document.getElementById('editSavingsGoalForm').addEventListener('submit', (e) => {
  e.preventDefault();
  try {
    const goalId = parseInt(document.getElementById('editSavingsGoalId').value);
    const name = document.getElementById('editSavingsGoalName').value;
    const amount = parseFloat(document.getElementById('editSavingsGoalAmount').value);
    const hasGain = document.getElementById('editSavingsGoalHasGain').checked;
    const gain = hasGain ? parseFloat(document.getElementById('editSavingsGoalGain').value) || 0 : 0;

    let goals = JSON.parse(localStorage.getItem('savings-goals') || '[]');
    const goal = goals.find(g => g.id == goalId);
    if (goal) {
      goal.name = name;
      goal.amount = amount;
      goal.gain = gain;
      localStorage.setItem('savings-goals', JSON.stringify(goals));
      closeEditSavingsGoalModal();
      loadSavingsGoals();
    }
  } catch (err) {
    alert('Error saving goal: ' + err.message);
  }
});

// Budget detail modal functions
async function viewBudgetDetails(budgetId) {
  try {
    const data = await api('budgets', 'GET');
    const budget = data.budgets.find(b => b.budget_id == budgetId);
    if (!budget) {
      alert('Budget not found');
      return;
    }

    const budgetData = JSON.parse(budget.budget_data || '{"income":[],"expenses":[]}');
    const modal = $('#budgetModal');
    const title = $('#budgetModalTitle');
    const content = $('#budgetModalContent');

    title.textContent = budget.budget_name;

    let html = `<p style="color: #666; margin-bottom: 1.5rem;">Created: ${budget.created_at}</p>`;
    html += `<div class="cards" style="margin-bottom: 1.5rem;">
      <div class="card">
        <h4 style="margin: 0 0 0.5rem 0;">Total Income</h4>
        <p style="font-weight: bold; color: var(--xp-accent);">${money(budget.total_income)}</p>
      </div>
      <div class="card">
        <h4 style="margin: 0 0 0.5rem 0;">Total Expenses</h4>
        <p style="font-weight: bold; color: var(--xp-accent);">${money(budget.total_expenses)}</p>
      </div>
      <div class="card highlight-card">
        <h4 style="margin: 0 0 0.5rem 0;">Monthly Savings</h4>
        <p style="font-weight: bold; color: var(--xp-accent);">${money(budget.monthly_savings)}</p>
      </div>
    </div>`;

    if (budgetData.income && budgetData.income.length > 0) {
      html += `<h4 style="margin-top: 1.5rem; margin-bottom: 0.75rem;">Income Sources</h4>`;
      budgetData.income.forEach((item) => {
        html += `<div style="padding: 0.75rem; background: #f5f5f5; border-radius: 6px; margin-bottom: 0.5rem;">
          <strong>${item.source_name}</strong> - ${money(item.amount)} (${item.frequency})
        </div>`;
      });
    }

    if (budgetData.expenses && budgetData.expenses.length > 0) {
      html += `<h4 style="margin-top: 1.5rem; margin-bottom: 0.75rem;">Expenses</h4>`;
      budgetData.expenses.forEach((item) => {
        html += `<div style="padding: 0.75rem; background: #f5f5f5; border-radius: 6px; margin-bottom: 0.5rem;">
          <strong>${item.category}</strong> - ${money(item.amount)} (${item.frequency})
        </div>`;
      });
    }

    content.innerHTML = html + `<div style="margin-top: 1.5rem; display: flex; gap: 0.5rem;">
      <button class="btn" style="flex: 1;" onclick="loadBudgetForEditing(${budgetId})">Load & Edit</button>
      <button class="btn" style="flex: 1; background: #999;" onclick="closeBudgetModal()">Close</button>
    </div>`;
    modal.style.display = 'block';
  } catch (err) {
    alert('Error loading budget: ' + err.message);
  }
}

function closeBudgetModal() {
  $('#budgetModal').style.display = 'none';
}

async function loadBudgetForEditing(budgetId) {
  try {
    const data = await api('budgets', 'GET');
    const budget = data.budgets.find(b => b.budget_id == budgetId);
    if (!budget) {
      alert('Budget not found');
      return;
    }

    const budgetData = JSON.parse(budget.budget_data || '{"income":[],"expenses":[]}');

    // Add income items
    for (const income of budgetData.income) {
      await api('income', 'POST', {
        source_name: income.source_name,
        amount: income.amount,
        frequency: income.frequency
      });
    }

    // Add expense items
    for (const expense of budgetData.expenses) {
      await api('expense', 'POST', {
        category: expense.category,
        amount: expense.amount,
        frequency: expense.frequency,
        date: expense.date
      });
    }

    closeBudgetModal();
    await loadDashboard();
    alert('Budget loaded! You can now edit these items.');
  } catch (err) {
    alert('Error loading budget: ' + err.message);
  }
}

loadDashboard().catch((err) => {
  console.error(err);
  alert('Unable to load dashboard. Check that:\n1. PHP server is running\n2. MySQL database is set up\n3. Database credentials in db.php are correct');
});
