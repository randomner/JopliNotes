<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 获取输入
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// 数据库连接
$con = mysqli_connect("localhost", "root", "", "joplinotes");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// 预处理防注入
$stmt = $con->prepare("SELECT user_id, password_hash FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// 检查用户是否存在
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => '\'<i>error: User does not exist</i>\'']);
    exit;
}

$row = $result->fetch_assoc();

// 密码校验（以后用password_hash + password_verify）
if ($password === $row['password_hash']) {
    $_SESSION["username"] = $username;
    $_SESSION["user_id"] = $row['user_id'];

    echo json_encode(['success' => true, 'message' => '\'<i>error: Login successful</i>\'']);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => '\'<i>error: Incorrect Password</i>\'']);
    exit;
}
?>
