<?php
require_once 'config.php';

$request_method = $_SERVER['REQUEST_METHOD'];
$request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$endpoint = basename($request_path);
$action = $_GET['action'] ?? null;

$user_id = 1; // TODO: Replace with actual session management

try {
    if ($action === 'add-income' && $request_method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        $stmt = $conn->prepare("INSERT INTO income (user_id, source_name, amount, frequency) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isds", $user_id, $data['source_name'], $data['amount'], $data['frequency']);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            throw new Exception("Failed to add income");
        }
    }
    elseif ($action === 'add-expense' && $request_method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        $stmt = $conn->prepare("INSERT INTO expenses (user_id, category, amount, date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isds", $user_id, $data['category'], $data['amount'], $data['date']);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            throw new Exception("Failed to add expense");
        }
    }
    elseif ($action === 'get-dashboard' && $request_method === 'GET') {
        // Get income summary
        $income_query = "SELECT SUM(amount) as total_income FROM income WHERE user_id = ?";
        $stmt = $conn->prepare($income_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $income_result = $stmt->get_result()->fetch_assoc();

        // Get expense summary
        $expense_query = "SELECT SUM(amount) as total_expenses FROM expenses WHERE user_id = ? AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
        $stmt = $conn->prepare($expense_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $expense_result = $stmt->get_result()->fetch_assoc();

        // Get all income entries
        $income_entries_query = "SELECT income_id, source_name, amount, frequency FROM income WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $conn->prepare($income_entries_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $income_entries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get all expense entries
        $expense_entries_query = "SELECT expense_id, category, amount, date FROM expenses WHERE user_id = ? ORDER BY date DESC";
        $stmt = $conn->prepare($expense_entries_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $expense_entries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $monthly_savings = ($income_result['total_income'] ?? 0) - ($expense_result['total_expenses'] ?? 0);

        echo json_encode([
            'success' => true,
            'total_income' => $income_result['total_income'] ?? 0,
            'total_expenses' => $expense_result['total_expenses'] ?? 0,
            'monthly_savings' => $monthly_savings,
            'yearly_projection' => $monthly_savings * 12,
            'income_entries' => $income_entries,
            'expense_entries' => $expense_entries
        ]);
    }
    elseif ($action === 'delete-income' && $request_method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);

        $stmt = $conn->prepare("DELETE FROM income WHERE income_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $data['income_id'], $user_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Failed to delete income");
        }
    }
    elseif ($action === 'delete-expense' && $request_method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);

        $stmt = $conn->prepare("DELETE FROM expenses WHERE expense_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $data['expense_id'], $user_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Failed to delete expense");
        }
    }
    elseif ($action === 'run-simulation' && $request_method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        $monthly_amount = $data['monthly_amount'];
        $duration_months = $data['duration_months'];
        $type = $data['type']; // 'save' or 'invest'
        $return_rate = $data['return_rate'] ?? 7;

        if ($type === 'save') {
            $total_savings = $monthly_amount * $duration_months;
            echo json_encode([
                'success' => true,
                'type' => 'save',
                'monthly_amount' => $monthly_amount,
                'duration_months' => $duration_months,
                'total_savings' => $total_savings
            ]);
        } elseif ($type === 'invest') {
            // Compound interest formula: FV = PMT * [((1 + r)^n - 1) / r]
            $monthly_rate = ($return_rate / 100) / 12;
            $total_value = $monthly_amount * ((pow(1 + $monthly_rate, $duration_months) - 1) / $monthly_rate);

            echo json_encode([
                'success' => true,
                'type' => 'invest',
                'monthly_amount' => $monthly_amount,
                'duration_months' => $duration_months,
                'return_rate' => $return_rate,
                'total_value' => $total_value
            ]);
        }
    }
    elseif ($action === 'save-scenario' && $request_method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        $stmt = $conn->prepare("INSERT INTO scenarios (user_id, type, monthly_amount, duration_months, expected_return_rate) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdid", $user_id, $data['type'], $data['monthly_amount'], $data['duration_months'], $data['return_rate']);

        if ($stmt->execute()) {
            $scenario_id = $conn->insert_id;

            $stmt = $conn->prepare("INSERT INTO budget_scenarios (user_id, scenario_id, saved_name) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $user_id, $scenario_id, $data['saved_name']);
            $stmt->execute();

            echo json_encode(['success' => true, 'id' => $scenario_id]);
        } else {
            throw new Exception("Failed to save scenario");
        }
    }
    elseif ($action === 'get-scenarios' && $request_method === 'GET') {
        $stmt = $conn->prepare("SELECT s.scenario_id, s.type, s.monthly_amount, s.duration_months, s.expected_return_rate, bs.saved_name FROM scenarios s JOIN budget_scenarios bs ON s.scenario_id = bs.scenario_id WHERE s.user_id = ? ORDER BY s.created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $scenarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode(['success' => true, 'scenarios' => $scenarios]);
    }
    else {
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>
