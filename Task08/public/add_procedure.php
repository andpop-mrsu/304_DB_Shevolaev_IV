<?php
require_once __DIR__ . '/../db/db.php';

$employee_id = $_GET['employee_id'] ?? 0;
$services = $pdo->query('SELECT id, name FROM services ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare('
    SELECT a.id, s.name as service_name 
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.employee_id = ? AND a.status = "completed"
    AND a.id NOT IN (SELECT appointment_id FROM completed_procedures)
');
$stmt->execute([$employee_id]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('
        INSERT INTO completed_procedures (appointment_id, employee_id, service_id, completed_at)
        VALUES (:appointment_id, :employee_id, :service_id, :completed_at)
    ');
    $stmt->execute([
        'appointment_id' => $_POST['appointment_id'],
        'employee_id' => $_POST['employee_id'],
        'service_id' => $_POST['service_id'],
        'completed_at' => $_POST['completed_at']
    ]);
    header('Location: services.php?id=' . $_POST['employee_id']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить процедуру</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Добавить процедуру</h1>
        <form method="POST">
            <input type="hidden" name="employee_id" value="<?= $employee_id ?>">
            <div class="mb-3">
                <label class="form-label">Запись</label>
                <select name="appointment_id" class="form-select" required>
                    <?php foreach ($appointments as $appointment): ?>
                        <option value="<?= $appointment['id'] ?>"><?= htmlspecialchars($appointment['service_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Услуга</label>
                <select name="service_id" class="form-select" required>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id'] ?>"><?= htmlspecialchars($service['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Дата и время выполнения</label>
                <input type="datetime-local" name="completed_at" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="services.php?id=<?= $employee_id ?>" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</body>
</html>
