<?php
function connectDatabase(): PDO
{
    $pdo = new PDO('sqlite:' . 'task.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    return $pdo;
}

function selectAllEmployees(PDO $pdo): array
{
    $query = "SELECT id, full_name 
              FROM employees 
              WHERE is_active = 1 
              ORDER BY full_name";

    $result = $pdo->prepare($query);
    $result->execute();

    return $result->fetchAll();
}

function selectCompletedProcedures(PDO $pdo, ?int $employeeId = null): array
{
    $query = "SELECT 
                e.id as employee_id,
                e.full_name as doctor_name,
                cp.completed_at as work_date,
                s.name as service_name,
                s.price as service_price
              FROM completed_procedures cp
              JOIN employees e ON cp.employee_id = e.id
              JOIN services s ON cp.service_id = s.id";

    if ($employeeId !== null):
        $query .= " WHERE e.id = :employee_id";
    endif;

    $query .= " ORDER BY e.full_name, cp.completed_at";

    $result = $pdo->prepare($query);

    if ($employeeId !== null):
        $result->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
    endif;

    $result->execute();

    return $result->fetchAll();
}


$pdo = connectDatabase();
$employees = selectAllEmployees($pdo);

$selectedEmployeeId = null;
$selectedEmployeeName = 'Все врачи';

if (isset($_GET['employee_id']) && $_GET['employee_id'] !== ''):
    $selectedEmployeeId = filter_var($_GET['employee_id'], FILTER_VALIDATE_INT);

    if ($selectedEmployeeId !== false):
        foreach ($employees as $employee):
            if ($employee['id'] == $selectedEmployeeId):
                $selectedEmployeeName = $employee['full_name'];
                break;
            endif;
        endforeach;
    endif;
endif;

$procedures = selectCompletedProcedures($pdo, $selectedEmployeeId);

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отчет по оказанным услугам клиники</title>
</head>

<body>
    <h1>Отчет по оказанным услугам клиники</h1>

    <h2>Фильтр</h2>
    <form method="GET" action="">
        <label for="employee_id">Выберите врача:</label>
        <select name="employee_id" id="employee_id">
            <option value="">Все врачи</option>
            <?php foreach ($employees as $employee): ?>
                <option value="<?= $employee['id'] ?>"
                    <?php if ($selectedEmployeeId == $employee['id']): ?>selected<?php endif; ?>>
                    <?= htmlspecialchars($employee['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Показать</button>
    </form>

    <?php if ($selectedEmployeeId !== null): ?>
        <p>
            <strong>Текущий фильтр:</strong> <?= htmlspecialchars($selectedEmployeeName) ?>
        </p>
    <?php endif; ?>

    <hr>

    <?php if (empty($procedures)): ?>
        <p>Данные не найдены. По выбранному фильтру завершенные процедуры отсутствуют.</p>
    <?php else: ?>
        <h2>Список оказанных услуг</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Номер врача</th>
                    <th>ФИО</th>
                    <th>Дата работы</th>
                    <th>Услуга</th>
                    <th>Стоимость</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($procedures as $procedure): ?>
                    <tr>
                        <td><?= htmlspecialchars($procedure['employee_id']) ?></td>
                        <td><?= htmlspecialchars($procedure['doctor_name']) ?></td>
                        <td><?= htmlspecialchars($procedure['work_date']) ?></td>
                        <td><?= htmlspecialchars($procedure['service_name']) ?></td>
                        <td><?= number_format($procedure['service_price'], 2, '.', '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr>
    <?php endif; ?>
</body>

</html>