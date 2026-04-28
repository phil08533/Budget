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
  $('#incomeTotal').textContent = money(data.summary.income_total);
  $('#expenseTotal').textContent = money(data.summary.expense_total);
  $('#savingsTotal').textContent = money(data.summary.savings_total);
  $('#netTotal').textContent = money(data.summary.net_monthly_balance);

  const list = $('#scenarioList');
  list.innerHTML = '';
  if (!data.scenarios.length) {
    list.innerHTML = '<p>No saved scenarios yet.</p>';
    return;
  }

  data.scenarios.forEach((scenario) => {
    const item = document.createElement('div');
    item.className = 'history-item';
    item.innerHTML = `
      <div>
        <strong>${scenario.saved_name}</strong>
        <p>${scenario.type} • ${scenario.duration_months} months • ${scenario.expected_return_rate}%</p>
      </div>
      <div>
        <button data-edit="${scenario.scenario_id}">Edit</button>
        <button data-delete="${scenario.scenario_id}">Delete</button>
      </div>
    `;
    list.appendChild(item);
  });
}

$('#incomeForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = new FormData(e.target);
  await api('income', 'POST', Object.fromEntries(form));
  e.target.reset();
  await loadDashboard();
});

$('#expenseForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = new FormData(e.target);
  await api('expense', 'POST', Object.fromEntries(form));
  e.target.reset();
  await loadDashboard();
});

$('#simulateForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const payload = Object.fromEntries(new FormData(e.target));
  latestSimulationPayload = payload;
  const res = await api('simulate', 'POST', payload);
  $('#projectedSavings').textContent = money(res.projection.future_savings);
  $('#projectedInvestment').textContent = money(res.projection.future_investment);
});

$('#saveScenario').addEventListener('click', async () => {
  const formPayload = Object.fromEntries(new FormData($('#simulateForm')));
  const payload = latestSimulationPayload || formPayload;
  await api('scenario', 'POST', payload);
  await loadDashboard();
});

$('#scenarioList').addEventListener('click', async (e) => {
  const deleteId = e.target.dataset.delete;
  const editId = e.target.dataset.edit;

  if (deleteId) {
    await api('scenario', 'DELETE', { scenario_id: deleteId });
    await loadDashboard();
  }

  if (editId) {
    const savedName = prompt('New saved name:');
    if (!savedName) return;
    const payload = Object.fromEntries(new FormData($('#simulateForm')));
    await api('scenario', 'PUT', {
      scenario_id: editId,
      saved_name: savedName,
      ...payload,
    });
    await loadDashboard();
  }
});

loadDashboard().catch((err) => {
  console.error(err);
  alert('Unable to load dashboard. Check DB setup in README.');
});
