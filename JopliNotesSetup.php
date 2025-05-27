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

$stmt = $con->prepare("SELECT MAX(user_id) FROM users");
$stmt->execute();
$stmt->bind_result($maxUserID);
$stmt->fetch();
$stmt->close();

// 预处理防注入
$stmt = $con->prepare("SELECT username FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// 检查用户是否存在
if ($result->num_rows !== 0) {
   echo json_encode(['success' => false, 'message' => '\'<i>error: Username has been used.</i>\'']);
   exit;
}

$maxUserID = $maxUserID + 1;
$newUser = $con->prepare("INSERT INTO users (user_id, username, password_hash) VALUES (?, ?, ?)");
$newUser->bind_param("iss", $maxUserID, $username, $password);
$newUser->execute();
echo json_encode(['success' => true, 'message' => '\'Your account has been set up\'']);
exit;
?>
