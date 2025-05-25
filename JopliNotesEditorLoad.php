<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 获取输入
$username = $_SESSION["username"];
$user_id = $_SESSION["user_id"];
 //echo json_encode(['success' => true, 'message' => $user_id ]);

// 数据库连接
$con = mysqli_connect("localhost", "root", "", "joplinotes");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// 预处理防注入
$stmt = $con->prepare("SELECT title, content_markdown FROM notes WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// 检查note是否存在
if ($result->num_rows === 0) {
    $newNote = $con->prepare("INSERT INTO notes (user_id, title, content_markdown) VALUES (?, ?, ?)");
    $title = "title";
    $blank = "\u{200B}"; // 使用 PHP 的 Unicode 转义语法
    $newNote->bind_param("iss", $user_id, $title, $blank);
    $newNote->execute();

    // 再次获取笔记
    $stmt = $con->prepare("SELECT title, content_markdown FROM notes WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
}


// 读取笔记
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'content' => $row['content_markdown'] ]);
    exit;
} else {
}
?>
