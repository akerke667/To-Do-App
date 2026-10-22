<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';

// Тапсырмаларды іздеу бойынша алу
if ($search) {
    $stmt = $pdo->prepare("SELECT tasks.*, categories.name as category_name FROM tasks 
                           JOIN categories ON tasks.category_id = categories.id 
                           WHERE tasks.user_id = ? AND tasks.title LIKE ? ORDER BY tasks.id DESC");
    $stmt->execute([$user_id, "%$search%"]);
} else {
    $stmt = $pdo->prepare("SELECT tasks.*, categories.name as category_name FROM tasks 
                           JOIN categories ON tasks.category_id = categories.id 
                           WHERE tasks.user_id = ? ORDER BY tasks.id DESC");
    $stmt->execute([$user_id]);
}
$tasks = $stmt->fetchAll();

// Категорияларды алу
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <title>Тапсырмалар менеджері</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">To-Do Manager</a>
            <div class="text-white">
                <?= htmlspecialchars($_SESSION['username']) ?> | 
                <a href="logout.php" class="btn btn-outline-light btn-sm">Шығу</a>
            </div>
        </div>
    </nav>

    <div class="container" style="max-width: 700px;">
        <!-- Жаңа тапсырма қосу формасы -->
        <div class="card p-3 mb-4 shadow-sm">
            <h5>Жаңа тапсырма қосу</h5>
            <form action="add_task.php" method="POST" class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="title" class="form-control" placeholder="Тапсырма атауы..." required>
                </div>
                <div class="col-md-4">
                    <select name="category_id" class="form-select" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Қосу</button>
                </div>
            </form>
        </div>

        <!-- Іздеу жолағы -->
        <form method="GET" class="input-group mb-3">
            <input type="text" name="search" class="form-control" placeholder="Тапсырманы іздеу..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-secondary" type="submit">Іздеу</button>
        </form>

        <!-- Тапсырмалар тізімі -->
        <h4>Тапсырмаларым</h4>
        <ul class="list-group shadow-sm">
            <?php if (count($tasks) > 0): ?>
                <?php foreach ($tasks as $task): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <?php if ($task['status'] == 'Орындалды'): ?>
                                <del class="text-muted"><?= htmlspecialchars($task['title']) ?></del>
                            <?php else: ?>
                                <strong><?= htmlspecialchars($task['title']) ?></strong>
                            <?php endif; ?>
                            <span class="badge bg-info text-dark ms-2"><?= $task['category_name'] ?></span>
                        </div>
                        <div>
                            <?php if ($task['status'] == 'Орындалуда'): ?>
                                <a href="toggle_task.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-success">✓ Орындалды</a>
                            <?php endif; ?>
                            <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-danger">Жою</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item text-muted">Тапсырмалар табылмады.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
