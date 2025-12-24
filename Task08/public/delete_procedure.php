<?php
require_once __DIR__ . '/../db/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare('
    SELECT cp.employee_id, s.name as service_name, cp.completed_at
    FROM completed_procedures cp
    JOIN services s ON cp.service_id = s.id
    WHERE cp.id = ?
');
$stmt->execute([$id]);
$procedure = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$procedure) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('DELETE FROM completed_procedures WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: services.php?id=' . $procedure['employee_id']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить процедуру</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Удалить процедуру</h1>
        <div class="mb-4">
            Вы уверены, что хотите удалить процедуру <strong><?= htmlspecialchars($procedure['service_name']) ?></strong> 
            от <strong><?= htmlspecialchars($procedure['completed_at']) ?></strong>?
        </div>
        <form method="POST">
            <button type="submit" class="btn btn-danger">Удалить</button>
            <a href="services.php?id=<?= $procedure['employee_id'] ?>" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</body>
</html>
