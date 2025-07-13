<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_set_cookie_params(86400);
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "cmo153", "NonsoMarcel12@", "cmo153");
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Database connection failed"]));
}

$action = $_GET['action'] ?? '';
$logged_in = isset($_SESSION['user_id']);

if (!$logged_in && !isset($_SESSION['trial_count'])) {
    $_SESSION['trial_count'] = 0;
}


if (
    !$logged_in &&
    ($_SESSION['trial_count'] ?? 0) > 3 &&
    !in_array($action, ['signup', 'login', 'session_check'])
) {
    echo json_encode(["error" => "Trial limit reached. Please log in or sign up."]);
    exit;
}


switch ($action) {

case 'signup':
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $conn->real_escape_string($data['username']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['trial_count'] = 0;
        unset($_SESSION['trial_transactions']);
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Signup failed: " . $stmt->error]);
    }
    exit;

case 'login':
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $conn->real_escape_string($data['username']);
    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $hash);
    if ($stmt->fetch() && password_verify($data['password'], $hash)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['trial_count'] = 0;
        unset($_SESSION['trial_transactions']);
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Invalid login"]);
    }
    exit;

case 'session_check':
    if (!$logged_in) {
        echo json_encode(["error" => "Unauthorized"]);
    } else {
        echo json_encode(["success" => true]);
    }
    exit;

case 'logout':
    session_unset();
    session_destroy();
    echo json_encode(["success" => true]);
    exit;

case 'add':
    $d = json_decode(file_get_contents("php://input"), true);
    if ($logged_in) {
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, description, amount, type, category) VALUES (?,?,?,?,?)");
        $stmt->bind_param("isdss", $_SESSION['user_id'], $d['description'], $d['amount'], $d['type'], $d['category']);
        $stmt->execute();
    } else {
        $_SESSION['trial_transactions'][] = [
            'description' => $d['description'],
            'amount' => $d['amount'],
            'type' => $d['type'],
            'category' => $d['category'],
            'date' => date('Y-m-d H:i:s')
        ];
    }
    echo json_encode(["success" => true]);
    exit;

case 'clear':
    if ($logged_in) {
        $conn->query("DELETE FROM transactions WHERE user_id={$_SESSION['user_id']}");
    } else {
        unset($_SESSION['trial_transactions']);
    }
    echo json_encode(["success" => true]);
    exit;

case 'history':
    if ($logged_in) {
        $stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id=? ORDER BY date DESC");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $q = $stmt->get_result();
        $out = [];
        while ($row = $q->fetch_assoc()) $out[] = $row;
        echo json_encode($out);
    } else {
        $trial = $_SESSION['trial_transactions'] ?? [];
        usort($trial, fn($a, $b) => strcmp($b['date'], $a['date']));
        echo json_encode($trial);
    }
    exit;

case 'summary':
    if ($logged_in) {
        $stmt = $conn->prepare("
            SELECT
                IFNULL(SUM(CASE WHEN type='income' THEN amount ELSE 0 END),0) AS total_income,
                IFNULL(SUM(CASE WHEN type='expense' THEN amount ELSE 0 END),0) AS total_expenses,
                IFNULL(SUM(CASE WHEN type='income' THEN amount ELSE -amount END),0) AS balance
            FROM transactions WHERE user_id=?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        echo json_encode([
            'total_income' => (float)$result['total_income'],
            'total_expenses' => (float)$result['total_expenses'],
            'balance' => (float)$result['balance']
        ]);
    } else {
        $trial = $_SESSION['trial_transactions'] ?? [];
        $income = 0; $expense = 0;
        foreach ($trial as $t) {
            if ($t['type'] === 'income') $income += $t['amount'];
            if ($t['type'] === 'expense') $expense += $t['amount'];
        }
        echo json_encode([
            'total_income' => $income,
            'total_expenses' => $expense,
            'balance' => $income - $expense
        ]);
    }
    exit;

case 'prediction':

    if (!$logged_in && $_SESSION['trial_count'] >= 3) {
        echo json_encode(["error" => "Trial limit reached. Please log in or sign up."]);
        exit;
    }

    $stable = 0;
    $variable = [];

    if ($logged_in) {
        $stmt = $conn->prepare("
            SELECT IFNULL(SUM(amount),0) FROM transactions WHERE user_id=? AND type='expense' AND category IN ('Rent', 'Utilities')");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($stable);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("
            SELECT amount FROM transactions WHERE user_id=? AND type='expense' AND (category NOT IN ('Rent','Utilities') OR category IS NULL)");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) $variable[] = $r['amount'];
    } else {
        $trial = $_SESSION['trial_transactions'] ?? [];
        foreach ($trial as $t) {
            if ($t['type'] === 'expense' && in_array($t['category'], ['Rent','Utilities'])) {
                $stable += $t['amount'];
            } elseif ($t['type'] === 'expense') {
                $variable[] = $t['amount'];
            }
        }
    }

    $var_total = array_sum($variable);
    $var_count = count($variable);

    $lower = $upper = 0;
    if ($var_count > 0) {
        $avg = $var_total / $var_count;
        $lower = max(0, $avg * $var_count * 0.85);
        $upper = $avg * $var_count * 1.05;
    }

    echo json_encode([
        'lower_bound' => round($stable + $lower, 2),
        'upper_bound' => round($stable + $upper, 2),
        'note' => 'Stable + variable expenses'
    ]);

    if (!$logged_in) {
        $_SESSION['trial_count']++;
    }

    exit;

default:
    echo json_encode(["error" => "Invalid action"]);
}
