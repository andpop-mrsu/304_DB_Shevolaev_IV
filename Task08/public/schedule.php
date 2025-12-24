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
    SELECT a.id, s.name as service_name, a.appointment_date, a.status
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.employee_id = ?
    ORDER BY a.appointment_date DESC
');
$stmt->execute([$employee_id]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>График работы</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h1 class="mb-4">График работы: <?= htmlspecialchars($employee['full_name']) ?></h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Услуга</th>
                    <th>Дата и время</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                        <td><?= htmlspecialchars((new DateTime($appointment['appointment_date']))->format('H:i d.m.Y')) ?></td>
                        <td>
                            <?php if ($appointment['status'] === 'scheduled'): ?>
                                <span class="badge bg-primary">Запланировано</span>
                            <?php elseif ($appointment['status'] === 'completed'): ?>
                                <span class="badge bg-success">Завершено</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Отменено</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-warning">Редактировать</a>
                            <a href="delete_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-danger">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="add_appointment.php?employee_id=<?= $employee_id ?>" class="btn btn-primary">Добавить запись</a>
        <a href="index.php" class="btn btn-secondary">Назад</a>
    </div>
</body>

</html>