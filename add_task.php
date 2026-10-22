<?php
session_start();
require 'db.php';

if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];

    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, category_id, title) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $category_id, $title]);
    }
}
header('Location: index.php');
exit;
?>
