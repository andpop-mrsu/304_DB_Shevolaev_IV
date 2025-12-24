<?php
require_once __DIR__ . '/../db/db.php';

$employee_id = $_GET['employee_id'] ?? 0;
$services = $pdo->query('SELECT id, name FROM services ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('
        INSERT INTO appointments (employee_id, service_id, appointment_date, status)
        VALUES (:employee_id, :service_id, :appointment_date, :status)
    ');
    $stmt->execute([
        'employee_id' => $_POST['employee_id'],
        'service_id' => $_POST['service_id'],
        'appointment_date' => $_POST['appointment_date'],
        'status' => $_POST['status']
    ]);
    header('Location: schedule.php?id=' . $_POST['employee_id']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить запись</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Добавить запись</h1>
        <form method="POST">
            <input type="hidden" name="employee_id" value="<?= $employee_id ?>">
            <div class="mb-3">
                <label class="form-label">Услуга</label>
                <select name="service_id" class="form-select" required>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id'] ?>"><?= htmlspecialchars($service['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Дата и время</label>
                <input type="datetime-local" name="appointment_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select" required>
                    <option value="scheduled">Запланировано</option>
                    <option value="completed">Завершено</option>
                    <option value="cancelled">Отменено</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="schedule.php?id=<?= $employee_id ?>" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</body>
</html>
