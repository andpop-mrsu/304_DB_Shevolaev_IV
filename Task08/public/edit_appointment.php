<?php
require_once __DIR__ . '/../db/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare('SELECT * FROM appointments WHERE id = ?');
$stmt->execute([$id]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    header('Location: index.php');
    exit;
}

$services = $pdo->query('SELECT id, name FROM services ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('
        UPDATE appointments 
        SET service_id = :service_id, appointment_date = :appointment_date, status = :status
        WHERE id = :id
    ');
    $stmt->execute([
        'id' => $id,
        'service_id' => $_POST['service_id'],
        'appointment_date' => $_POST['appointment_date'],
        'status' => $_POST['status']
    ]);
    header('Location: schedule.php?id=' . $appointment['employee_id']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать запись</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Редактировать запись</h1>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Услуга</label>
                <select name="service_id" class="form-select" required>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id'] ?>" <?= $service['id'] == $appointment['service_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($service['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Дата и время</label>
                <input type="datetime-local" name="appointment_date" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($appointment['appointment_date'])) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select" required>
                    <option value="scheduled" <?= $appointment['status'] === 'scheduled' ? 'selected' : '' ?>>Запланировано</option>
                    <option value="completed" <?= $appointment['status'] === 'completed' ? 'selected' : '' ?>>Завершено</option>
                    <option value="cancelled" <?= $appointment['status'] === 'cancelled' ? 'selected' : '' ?>>Отменено</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="schedule.php?id=<?= $appointment['employee_id'] ?>" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</body>
</html>
