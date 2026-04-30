<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    jsonResponse(['error' => 'Not authenticated'], 401);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? '';

$userId = (int) $_SESSION['user_id'];

function calculateMonthlyValue(float $amount, string $frequency): float
{
    return match ($frequency) {
        'daily' => $amount * 30.44,
        'weekly' => $amount * 4.33,
        'bi-weekly' => $amount * 2.167,
        'monthly' => $amount,
        'yearly' => $amount / 12,
        'one-time' => 0,
        default => 0,
    };
}

switch ($action) {
    case 'dashboard':
        if ($method !== 'GET') {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
        getDashboard($userId);
        break;

    case 'income':
        match ($method) {
            'POST' => createIncome($userId),
            'PUT' => updateIncome($userId),
            'DELETE' => deleteIncome($userId),
            default => jsonResponse(['error' => 'Method not allowed'], 405),
        };
        break;

    case 'expense':
        match ($method) {
            'POST' => createExpense($userId),
            'PUT' => updateExpense($userId),
            'DELETE' => deleteExpense($userId),
            default => jsonResponse(['error' => 'Method not allowed'], 405),
        };
        break;

    case 'simulate':
        if ($method !== 'POST') {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
        simulateScenario();
        break;

    case 'scenario':
        match ($method) {
            'POST' => createScenario($userId),
            'PUT' => updateScenario($userId),
            'DELETE' => deleteScenario($userId),
            default => jsonResponse(['error' => 'Method not allowed'], 405),
        };
        break;

    case 'budget':
        match ($method) {
            'POST' => saveBudget($userId),
            'DELETE' => deleteBudget($userId),
            default => jsonResponse(['error' => 'Method not allowed'], 405),
        };
        break;

    case 'budgets':
        if ($method !== 'GET') {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
        getBudgets($userId);
        break;

    default:
        jsonResponse(['error' => 'Unknown action'], 404);
}

function getDashboard(int $userId): void
{
    $pdo = db();

    $incomeStmt = $pdo->prepare('SELECT income_id, source_name, amount, frequency FROM income WHERE user_id = ? ORDER BY income_id DESC');
    $incomeStmt->execute([$userId]);
    $income = $incomeStmt->fetchAll();

    $expenseStmt = $pdo->prepare('SELECT expense_id, category, amount, frequency, date FROM expenses WHERE user_id = ? ORDER BY date DESC, expense_id DESC');
    $expenseStmt->execute([$userId]);
    $expenses = $expenseStmt->fetchAll();

    $scenarioStmt = $pdo->prepare(
        'SELECT s.scenario_id, s.type, s.monthly_amount, s.duration_months, s.expected_return_rate, bs.saved_name
         FROM scenarios s
         INNER JOIN budget_scenarios bs ON bs.scenario_id = s.scenario_id
         WHERE bs.user_id = ?
         ORDER BY bs.id DESC'
    );
    $scenarioStmt->execute([$userId]);
    $scenarios = $scenarioStmt->fetchAll();

    $incomeTotal = 0;
    foreach ($income as $row) {
        $incomeTotal += calculateMonthlyValue((float) $row['amount'], $row['frequency']);
    }

    $expenseTotal = 0;
    foreach ($expenses as $row) {
        $expenseTotal += calculateMonthlyValue((float) $row['amount'], $row['frequency']);
    }

    jsonResponse([
        'income' => $income,
        'expenses' => $expenses,
        'scenarios' => $scenarios,
        'summary' => [
            'income_total' => round($incomeTotal, 2),
            'expense_total' => round($expenseTotal, 2),
            'savings_total' => round($incomeTotal - $expenseTotal, 2),
            'net_monthly_balance' => round($incomeTotal - $expenseTotal, 2),
        ],
    ]);
}

function createIncome(int $userId): void
{
    $body = readJsonBody();
    $source = trim((string) ($body['source_name'] ?? ''));
    $amount = toMoney($body['amount'] ?? null, 'amount');
    $frequency = trim((string) ($body['frequency'] ?? 'monthly'));

    if ($source === '') {
        jsonResponse(['error' => 'source_name is required'], 422);
    }

    $pdo = db();
    $stmt = $pdo->prepare('INSERT INTO income (user_id, source_name, amount, frequency) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $source, $amount, $frequency]);

    jsonResponse(['message' => 'Income added'], 201);
}

function updateIncome(int $userId): void
{
    $body = readJsonBody();
    $incomeId = toInt($body['income_id'] ?? null, 'income_id');
    $source = trim((string) ($body['source_name'] ?? ''));
    $amount = toMoney($body['amount'] ?? null, 'amount');
    $frequency = trim((string) ($body['frequency'] ?? 'monthly'));

    $pdo = db();
    $stmt = $pdo->prepare('UPDATE income SET source_name = ?, amount = ?, frequency = ? WHERE income_id = ? AND user_id = ?');
    $stmt->execute([$source, $amount, $frequency, $incomeId, $userId]);

    jsonResponse(['message' => 'Income updated']);
}

function deleteIncome(int $userId): void
{
    $body = readJsonBody();
    $incomeId = toInt($body['income_id'] ?? null, 'income_id');

    $pdo = db();
    $stmt = $pdo->prepare('DELETE FROM income WHERE income_id = ? AND user_id = ?');
    $stmt->execute([$incomeId, $userId]);

    jsonResponse(['message' => 'Income deleted']);
}

function createExpense(int $userId): void
{
    $body = readJsonBody();
    $category = trim((string) ($body['category'] ?? ''));
    $amount = toMoney($body['amount'] ?? null, 'amount');
    $frequency = trim((string) ($body['frequency'] ?? 'one-time'));
    $date = trim((string) ($body['date'] ?? date('Y-m-d')));

    if ($category === '') {
        jsonResponse(['error' => 'category is required'], 422);
    }

    $pdo = db();
    $stmt = $pdo->prepare('INSERT INTO expenses (user_id, category, amount, frequency, date) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$userId, $category, $amount, $frequency, $date]);

    jsonResponse(['message' => 'Expense added'], 201);
}

function updateExpense(int $userId): void
{
    $body = readJsonBody();
    $expenseId = toInt($body['expense_id'] ?? null, 'expense_id');
    $category = trim((string) ($body['category'] ?? ''));
    $amount = toMoney($body['amount'] ?? null, 'amount');
    $frequency = trim((string) ($body['frequency'] ?? 'one-time'));
    $date = trim((string) ($body['date'] ?? date('Y-m-d')));

    $pdo = db();
    $stmt = $pdo->prepare('UPDATE expenses SET category = ?, amount = ?, frequency = ?, date = ? WHERE expense_id = ? AND user_id = ?');
    $stmt->execute([$category, $amount, $frequency, $date, $expenseId, $userId]);

    jsonResponse(['message' => 'Expense updated']);
}

function deleteExpense(int $userId): void
{
    $body = readJsonBody();
    $expenseId = toInt($body['expense_id'] ?? null, 'expense_id');

    $pdo = db();
    $stmt = $pdo->prepare('DELETE FROM expenses WHERE expense_id = ? AND user_id = ?');
    $stmt->execute([$expenseId, $userId]);

    jsonResponse(['message' => 'Expense deleted']);
}

function simulateScenario(): void
{
    $body = readJsonBody();
    $monthlyAmount = toMoney($body['monthly_amount'] ?? 0, 'monthly_amount');
    $durationMonths = max(1, toInt($body['duration_months'] ?? 12, 'duration_months'));
    $returnRate = (float) ($body['expected_return_rate'] ?? 7);

    $futureSavings = $monthlyAmount * $durationMonths;
    $monthlyRate = $returnRate / 100 / 12;

    if ($monthlyRate <= 0) {
        $futureInvestment = $futureSavings;
    } else {
        $futureInvestment = $monthlyAmount * ((pow(1 + $monthlyRate, $durationMonths) - 1) / $monthlyRate);
    }

    jsonResponse([
        'projection' => [
            'future_savings' => round($futureSavings, 2),
            'future_investment' => round($futureInvestment, 2),
            'duration_months' => $durationMonths,
            'monthly_amount' => $monthlyAmount,
            'expected_return_rate' => $returnRate,
        ],
    ]);
}

function createScenario(int $userId): void
{
    $body = readJsonBody();
    $savedName = trim((string) ($body['saved_name'] ?? 'My Scenario'));
    $type = trim((string) ($body['type'] ?? 'save'));
    $monthlyAmount = toMoney($body['monthly_amount'] ?? 0, 'monthly_amount');
    $durationMonths = toInt($body['duration_months'] ?? 12, 'duration_months');
    $returnRate = (float) ($body['expected_return_rate'] ?? 7);

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $scenarioStmt = $pdo->prepare(
            'INSERT INTO scenarios (user_id, type, monthly_amount, duration_months, expected_return_rate) VALUES (?, ?, ?, ?, ?)'
        );
        $scenarioStmt->execute([$userId, $type, $monthlyAmount, $durationMonths, $returnRate]);

        $scenarioId = (int) $pdo->lastInsertId();

        $junctionStmt = $pdo->prepare('INSERT INTO budget_scenarios (user_id, scenario_id, saved_name) VALUES (?, ?, ?)');
        $junctionStmt->execute([$userId, $scenarioId, $savedName]);

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        jsonResponse(['error' => 'Unable to save scenario'], 500);
    }

    jsonResponse(['message' => 'Scenario saved'], 201);
}

function updateScenario(int $userId): void
{
    $body = readJsonBody();
    $scenarioId = toInt($body['scenario_id'] ?? null, 'scenario_id');
    $savedName = trim((string) ($body['saved_name'] ?? 'Scenario'));
    $type = trim((string) ($body['type'] ?? 'save'));
    $monthlyAmount = toMoney($body['monthly_amount'] ?? 0, 'monthly_amount');
    $durationMonths = toInt($body['duration_months'] ?? 12, 'duration_months');
    $returnRate = (float) ($body['expected_return_rate'] ?? 7);

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $scenarioStmt = $pdo->prepare(
            'UPDATE scenarios SET type = ?, monthly_amount = ?, duration_months = ?, expected_return_rate = ? WHERE scenario_id = ? AND user_id = ?'
        );
        $scenarioStmt->execute([$type, $monthlyAmount, $durationMonths, $returnRate, $scenarioId, $userId]);

        $junctionStmt = $pdo->prepare('UPDATE budget_scenarios SET saved_name = ? WHERE scenario_id = ? AND user_id = ?');
        $junctionStmt->execute([$savedName, $scenarioId, $userId]);

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        jsonResponse(['error' => 'Unable to update scenario'], 500);
    }

    jsonResponse(['message' => 'Scenario updated']);
}

function deleteScenario(int $userId): void
{
    $body = readJsonBody();
    $scenarioId = toInt($body['scenario_id'] ?? null, 'scenario_id');

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $junctionStmt = $pdo->prepare('DELETE FROM budget_scenarios WHERE scenario_id = ? AND user_id = ?');
        $junctionStmt->execute([$scenarioId, $userId]);

        $scenarioStmt = $pdo->prepare('DELETE FROM scenarios WHERE scenario_id = ? AND user_id = ?');
        $scenarioStmt->execute([$scenarioId, $userId]);

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        jsonResponse(['error' => 'Unable to delete scenario'], 500);
    }

    jsonResponse(['message' => 'Scenario deleted']);
}

function saveBudget(int $userId): void
{
    $body = readJsonBody();
    $name = trim((string) ($body['name'] ?? 'Budget'));
    $income = $body['income'] ?? [];
    $expenses = $body['expenses'] ?? [];
    $summary = $body['summary'] ?? [];

    if (!is_array($income) || !is_array($expenses)) {
        jsonResponse(['error' => 'Invalid budget data'], 422);
    }

    $pdo = db();

    // Calculate totals
    $totalIncome = 0;
    foreach ($income as $item) {
        $totalIncome += calculateMonthlyValue((float) ($item['amount'] ?? 0), $item['frequency'] ?? 'monthly');
    }

    $totalExpenses = 0;
    foreach ($expenses as $item) {
        $totalExpenses += calculateMonthlyValue((float) ($item['amount'] ?? 0), $item['frequency'] ?? 'monthly');
    }

    $stmt = $pdo->prepare(
        'INSERT INTO budgets (user_id, budget_name, total_income, total_expenses, monthly_savings, budget_data) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $userId,
        $name,
        round($totalIncome, 2),
        round($totalExpenses, 2),
        round($totalIncome - $totalExpenses, 2),
        json_encode(['income' => $income, 'expenses' => $expenses])
    ]);

    jsonResponse(['message' => 'Budget saved'], 201);
}

function getBudgets(int $userId): void
{
    $pdo = db();
    $stmt = $pdo->prepare('SELECT budget_id, budget_name, total_income, total_expenses, monthly_savings, created_at FROM budgets WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    $budgets = $stmt->fetchAll();

    jsonResponse(['budgets' => $budgets]);
}

function deleteBudget(int $userId): void
{
    $body = readJsonBody();
    $budgetId = toInt($body['budget_id'] ?? null, 'budget_id');

    $pdo = db();
    $stmt = $pdo->prepare('DELETE FROM budgets WHERE budget_id = ? AND user_id = ?');
    $stmt->execute([$budgetId, $userId]);

    jsonResponse(['message' => 'Budget deleted']);
}
