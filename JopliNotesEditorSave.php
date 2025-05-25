<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 获取输入
$noteContent = $_POST['noteContent'] ?? '';
$user_id = $_SESSION["user_id"];

// 数据库连接
$con = mysqli_connect("localhost", "root", "", "joplinotes");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// 预处理防注入
$stmt = $con->prepare("UPDATE notes SET content_markdown = ? WHERE user_id = ?");
$stmt->bind_param("si", $noteContent, $user_id); // 两个字符串，一个 int
$stmt->execute();

echo json_encode(['success' => true, 'message' => 'Saved']);

?>
