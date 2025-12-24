<?php
require_once __DIR__ . '/../db/db.php';

$stmt = $pdo->query('
    SELECT e.id, e.full_name, e.is_active, s.name as specialization
    FROM employees e
    JOIN specializations s ON e.specialization_id = s.id
    ORDER BY e.full_name
');
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список врачей</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Список врачей</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ФИО</th>
                    <th>Специализация</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?= htmlspecialchars($employee['full_name']) ?></td>
                    <td><?= htmlspecialchars($employee['specialization']) ?></td>
                    <td>
                        <?php if ($employee['is_active']): ?>
                            <span class="badge bg-success">Активен</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Уволен</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_employee.php?id=<?= $employee['id'] ?>" class="btn btn-sm btn-warning">Редактировать</a>
                        <a href="delete_employee.php?id=<?= $employee['id'] ?>" class="btn btn-sm btn-danger">Удалить</a>
                        <a href="schedule.php?id=<?= $employee['id'] ?>" class="btn btn-sm btn-info">График</a>
                        <a href="services.php?id=<?= $employee['id'] ?>" class="btn btn-sm btn-success">Оказанные услуги</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="add_employee.php" class="btn btn-primary">Добавить врача</a>
    </div>
</body>
</html>
