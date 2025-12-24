<?php
require_once __DIR__ . '/../db/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare('
    SELECT a.employee_id, s.name as service_name, a.appointment_date
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.id = ?
');
$stmt->execute([$id]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('DELETE FROM appointments WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: schedule.php?id=' . $appointment['employee_id']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить запись</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h1 class="mb-4">Удалить запись</h1>
        <div class="mb-4">
            Вы уверены, что хотите удалить запись на услугу <strong><?= htmlspecialchars($appointment['service_name']) ?></strong>
            от <strong><?= htmlspecialchars((new DateTime($appointment['appointment_date']))->format('H:i d.m.Y')) ?></strong>?
        </div>
        <form method="POST">
            <button type="submit" class="btn btn-danger">Удалить</button>
            <a href="schedule.php?id=<?= $appointment['employee_id'] ?>" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</body>

</html>