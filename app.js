const $ = (sel) => document.querySelector(sel);

let latestSimulationPayload = null;

function money(value) {
  return `$${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
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

  // Update summary cards
  $('#incomeTotal').textContent = money(data.summary.income_total);
  $('#expenseTotal').textContent = money(data.summary.expense_total);
  $('#savingsTotal').textContent = money(data.summary.savings_total);
  $('#netTotal').textContent = money(data.summary.net_monthly_balance);

  // Render income list
  const incomeList = $('#incomeList');
  incomeList.innerHTML = '';
  if (data.income.length) {
    data.income.forEach((item) => {
      const div = document.createElement('div');
      div.className = 'history-item';
      div.innerHTML = `
        <div>
          <h4 style="margin: 0;">${item.source_name}</h4>
          <p style="margin: 0.25rem 0 0 0; color: #666;">${item.frequency}</p>
        </div>
        <div style="text-align: right;">
          <div style="font-weight: bold; color: var(--accent);">${money(item.amount)}</div>
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
      const div = document.createElement('div');
      div.className = 'history-item';
      div.innerHTML = `
        <div>
          <h4 style="margin: 0;">${item.category}</h4>
          <p style="margin: 0.25rem 0 0 0; color: #666;">${item.date}</p>
        </div>
        <div style="text-align: right;">
          <div style="font-weight: bold; color: var(--accent);">${money(item.amount)}</div>
          <button class="btn" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; margin-top: 0.5rem;" data-delete-expense="${item.expense_id}">Delete</button>
        </div>
      `;
      expenseList.appendChild(div);
    });
  } else {
    expenseList.innerHTML = '<p style="color: #999; text-align: center;">No expenses yet</p>';
  }

  // Render scenarios
  const scenarioList = $('#scenarioList');
  scenarioList.innerHTML = '';
  if (!data.scenarios.length) {
    scenarioList.innerHTML = '<p style="color: #999; text-align: center; padding: 2rem;">No saved scenarios yet. Run a simulation and save it!</p>';
    return;
  }

  data.scenarios.forEach((scenario) => {
    const item = document.createElement('div');
    item.className = 'history-item';
    item.innerHTML = `
      <div>
        <h4 style="margin: 0;">${scenario.saved_name}</h4>
        <p style="margin: 0.25rem 0 0 0; color: #666;">${scenario.type} • ${scenario.duration_months} months • ${scenario.expected_return_rate}%</p>
      </div>
      <div style="display: flex; gap: 0.5rem;">
        <button class="btn secondary" style="font-size: 0.75rem; padding: 0.4rem 0.8rem;" data-edit="${scenario.scenario_id}">Edit</button>
        <button class="btn" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; background: #c84b4b; border-color: #c84b4b;" data-delete="${scenario.scenario_id}">Delete</button>
      </div>
    `;
    item.style.justifyContent = 'space-between';
    item.style.alignItems = 'center';
    scenarioList.appendChild(item);
  });
}

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
    // Set date to today if not provided
    if (!form.get('date')) {
      form.set('date', new Date().toISOString().split('T')[0]);
    }
    await api('expense', 'POST', Object.fromEntries(form));
    e.target.reset();
    await loadDashboard();
  } catch (err) {
    alert('Error adding expense: ' + err.message);
  }
});

$('#simulateForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  try {
    const payload = Object.fromEntries(new FormData(e.target));
    latestSimulationPayload = payload;
    const res = await api('simulate', 'POST', payload);

    const resultsDiv = $('#projectionResults');
    resultsDiv.innerHTML = `
      <div class="cards">
        <article class="card">
          <h3>Projected Savings</h3>
          <p>${money(res.projection.future_savings)}</p>
          <small style="color: #999;">After ${res.projection.duration_months} months @ ${res.projection.monthly_amount}/month</small>
        </article>
        <article class="card">
          <h3>Projected Investment</h3>
          <p>${money(res.projection.future_investment)}</p>
          <small style="color: #999;">With ${res.projection.expected_return_rate}% annual return</small>
        </article>
      </div>
    `;
  } catch (err) {
    alert('Error running simulation: ' + err.message);
  }
});

$('#saveScenario').addEventListener('click', async () => {
  try {
    const formPayload = Object.fromEntries(new FormData($('#simulateForm')));
    const payload = latestSimulationPayload || formPayload;
    await api('scenario', 'POST', payload);
    alert('Scenario saved!');
    await loadDashboard();
  } catch (err) {
    alert('Error saving scenario: ' + err.message);
  }
});

// Event delegation for income/expense/scenario deletions
document.addEventListener('click', async (e) => {
  const deleteIncomeId = e.target.dataset.deleteIncome;
  const deleteExpenseId = e.target.dataset.deleteExpense;
  const deleteScenarioId = e.target.dataset.delete;
  const editScenarioId = e.target.dataset.edit;

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

  if (deleteScenarioId && confirm('Delete this scenario?')) {
    try {
      await api('scenario', 'DELETE', { scenario_id: deleteScenarioId });
      await loadDashboard();
    } catch (err) {
      alert('Error: ' + err.message);
    }
  }

  if (editScenarioId) {
    const savedName = prompt('New scenario name:');
    if (!savedName) return;
    try {
      const payload = Object.fromEntries(new FormData($('#simulateForm')));
      await api('scenario', 'PUT', {
        scenario_id: editScenarioId,
        saved_name: savedName,
        ...payload,
      });
      await loadDashboard();
    } catch (err) {
      alert('Error: ' + err.message);
    }
  }
});

loadDashboard().catch((err) => {
  console.error(err);
  alert('Unable to load dashboard. Check that:\n1. PHP server is running\n2. MySQL database is set up\n3. Database credentials in db.php are correct');
});
