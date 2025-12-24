<?php
require_once __DIR__ . '/../db/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare('SELECT full_name FROM employees WHERE id = ?');
$stmt->execute([$id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('DELETE FROM employees WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить врача</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Удалить врача</h1>
        <div class="mb-4">
            Вы уверены, что хотите удалить врача <strong><?= htmlspecialchars($employee['full_name']) ?></strong>?
        </div>
        <form method="POST">
            <button type="submit" class="btn btn-danger">Удалить</button>
            <a href="index.php" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</body>
</html>
