<?php
require_once __DIR__ . '/../db/db.php';

$specializations = $pdo->query('SELECT id, name FROM specializations ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('
        INSERT INTO employees (full_name, specialization_id, salary_percent, is_active, hired_date, fired_date)
        VALUES (:full_name, :specialization_id, :salary_percent, :is_active, :hired_date, :fired_date)
    ');
    $stmt->execute([
        'full_name' => $_POST['full_name'],
        'specialization_id' => $_POST['specialization_id'],
        'salary_percent' => $_POST['salary_percent'],
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'hired_date' => $_POST['hired_date'],
        'fired_date' => !empty($_POST['fired_date']) ? $_POST['fired_date'] : null
    ]);
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить врача</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Добавить врача</h1>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">ФИО</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Специализация</label>
                <select name="specialization_id" class="form-select" required>
                    <?php foreach ($specializations as $spec): ?>
                        <option value="<?= $spec['id'] ?>"><?= htmlspecialchars($spec['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Процент зарплаты</label>
                <input type="number" name="salary_percent" class="form-control" step="0.01" min="0.01" max="100" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" checked>
                <label class="form-check-label" for="is_active">Активен</label>
            </div>
            <div class="mb-3">
                <label class="form-label">Дата приема на работу</label>
                <input type="date" name="hired_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Дата увольнения</label>
                <input type="date" name="fired_date" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="index.php" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</body>
</html>
