<?php
require_once __DIR__ . '/../db/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare('SELECT * FROM completed_procedures WHERE id = ?');
$stmt->execute([$id]);
$procedure = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$procedure) {
    header('Location: index.php');
    exit;
}

$services = $pdo->query('SELECT id, name FROM services ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('
        UPDATE completed_procedures 
        SET service_id = :service_id, completed_at = :completed_at
        WHERE id = :id
    ');
    $stmt->execute([
        'id' => $id,
        'service_id' => $_POST['service_id'],
        'completed_at' => $_POST['completed_at']
    ]);
    header('Location: services.php?id=' . $procedure['employee_id']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать процедуру</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Редактировать процедуру</h1>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Услуга</label>
                <select name="service_id" class="form-select" required>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id'] ?>" <?= $service['id'] == $procedure['service_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($service['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Дата и время выполнения</label>
                <input type="datetime-local" name="completed_at" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($procedure['completed_at'])) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="services.php?id=<?= $procedure['employee_id'] ?>" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</body>
</html>
