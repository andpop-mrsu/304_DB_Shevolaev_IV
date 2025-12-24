<?php
require_once __DIR__ . '/../db/db.php';

$employee_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare('SELECT full_name FROM employees WHERE id = ?');
$stmt->execute([$employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('
    SELECT cp.id, s.name as service_name, s.price, cp.completed_at
    FROM completed_procedures cp
    JOIN services s ON cp.service_id = s.id
    WHERE cp.employee_id = ?
    ORDER BY cp.completed_at DESC
');
$stmt->execute([$employee_id]);
$procedures = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оказанные услуги</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h1 class="mb-4">Оказанные услуги: <?= htmlspecialchars($employee['full_name']) ?></h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Услуга</th>
                    <th>Дата выполнения</th>
                    <th>Стоимость</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($procedures as $procedure): ?>
                    <tr>
                        <td><?= htmlspecialchars($procedure['service_name']) ?></td>
                        <td><?= htmlspecialchars((new DateTime($procedure['completed_at']))->format('H:i d.m.Y')) ?></td>
                        <td><?= number_format($procedure['price'], 2) ?> руб.</td>
                        <td>
                            <a href="edit_procedure.php?id=<?= $procedure['id'] ?>" class="btn btn-sm btn-warning">Редактировать</a>
                            <a href="delete_procedure.php?id=<?= $procedure['id'] ?>" class="btn btn-sm btn-danger">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="add_procedure.php?employee_id=<?= $employee_id ?>" class="btn btn-primary">Добавить процедуру</a>
        <a href="index.php" class="btn btn-secondary">Назад</a>
    </div>
</body>

</html>