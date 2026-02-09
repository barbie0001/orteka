<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();

// Получаем график на текущую неделю
$current_week_start = date('Y-m-d', strtotime('monday this week'));
$current_week_end = date('Y-m-d', strtotime('sunday this week'));

$schedules = $pdo->query("
    SELECT s.*, u.full_name as employee_name 
    FROM schedules s 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE s.work_date BETWEEN '$current_week_start' AND '$current_week_end'
    ORDER BY s.work_date, s.start_time
")->fetchAll();

$employees = $pdo->query("SELECT id, full_name FROM users WHERE is_active = 1 ORDER BY full_name")->fetchAll();

// Обработка формы добавления графика
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $user_id = $_POST['user_id'];
    $work_date = $_POST['work_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Проверяем, нет ли уже записи на этот день
    $check_stmt = $pdo->prepare("SELECT id FROM schedules WHERE user_id = ? AND work_date = ?");
    $check_stmt->execute([$user_id, $work_date]);

    if ($check_stmt->fetch()) {
        $error = "У сотрудника уже есть график на выбранную дату";
    } else {
        $stmt = $pdo->prepare("INSERT INTO schedules (user_id, work_date, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $work_date, $start_time, $end_time]);
        $success = "График успешно добавлен";
        // Обновляем список графиков
        header("Location: schedule.php");
        exit;
    }
}

// Обработка удаления графика
if (isset($_GET['delete'])) {
    $schedule_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ?");
    $stmt->execute([$schedule_id]);
    $success = "График успешно удален";
    header("Location: schedule.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>График работы - Ортопедический салон</title>
    <style>
        :root {
            --primary-color: #009AA6;
            --accent-color: #FF6E00;
            --light-gray: #f5f5f5;
            --dark-gray: #333;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--light-gray);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: var(--white);
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
        }

        .logo {
            text-align: center;
            padding: 0 20px 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 120px;
            height: auto;
        }

        .logo h1 {
            color: var(--primary-color);
            font-size: 18px;
            margin-top: 10px;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--dark-gray);
            text-decoration: none;
            transition: all 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: var(--white);
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-name {
            margin-right: 15px;
            font-weight: 500;
        }

        .logout-btn {
            background-color: var(--accent-color);
            color: var(--white);
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .content {
            padding: 30px;
            flex: 1;
        }

        .section-title {
            color: var(--primary-color);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #007a83;
        }

        .btn-accent {
            background-color: var(--accent-color);
        }

        .btn-accent:hover {
            background-color: #e05a00;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .table-container {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: var(--primary-color);
            color: var(--white);
            font-weight: 500;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .form-container {
            background-color: var(--white);
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .week-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background-color: var(--white);
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .week-display {
            font-size: 18px;
            font-weight: 500;
            color: var(--primary-color);
        }

        .nav-btn {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .work-hours {
            font-weight: 500;
            color: var(--primary-color);
        }

        .day-header {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--dark-gray);
        }

        .no-schedule {
            color: #6c757d;
            font-style: italic;
        }

        /* Добавьте эти стили в каждый CSS раздел */
        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            z-index: 10000;
            font-weight: 500;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s ease;
        }

        .message-success {
            background-color: #28a745;
        }

        .message-error {
            background-color: #dc3545;
        }

        .message-info {
            background-color: #17a2b8;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Боковая панель -->
    <div class="sidebar">
        <div class="logo">
            <img src="лого.png" alt="Логотип ортопедического салона">
            <h1>Ортопедический салон</h1>
        </div>

        <ul class="nav-menu">
            <li class="nav-item"><a href="dashboard.php" class="nav-link"><i>📊</i> Панель управления</a></li>
            <li class="nav-item"><a href="appointments.php" class="nav-link"><i>📅</i> Записи на прием</a></li>
            <li class="nav-item"><a href="clients.php" class="nav-link"><i>👥</i> Клиенты</a></li>
            <li class="nav-item"><a href="orders.php" class="nav-link"><i>📋</i> Заказы</a></li>
            <li class="nav-item"><a href="schedule.php" class="nav-link active"><i>⏰</i> График работы</a></li>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <li class="nav-item"><a href="employees.php" class="nav-link"><i>⚙️</i> Сотрудники</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Основной контент -->
    <div class="main-content">
        <div class="header">
            <h2>График работы сотрудников</h2>
            <div class="user-info">
                <div class="user-name">
                    <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                    (<?php echo $_SESSION['role'] == 'admin' ? 'Администратор' : 'Сотрудник'; ?>)
                </div>
                <a href="logout.php" class="logout-btn">Выйти</a>
            </div>
        </div>

        <div class="content">
            <!-- Сообщения -->
            <?php if (isset($success)): ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>


            <!-- Форма добавления графика -->
            <div class="form-container">
                <h3 style="color: var(--primary-color); margin-bottom: 20px;">Добавить график работы</h3>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="user_id">Сотрудник *</label>
                            <select id="user_id" name="user_id" required>
                                <option value="">Выберите сотрудника</option>
                                <?php foreach ($employees as $employee): ?>
                                    <option value="<?php echo $employee['id']; ?>">
                                        <?php echo htmlspecialchars($employee['full_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="work_date">Дата работы *</label>
                            <input type="date" id="work_date" name="work_date" required
                                min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="start_time">Время начала *</label>
                            <input type="time" id="start_time" name="start_time" required value="09:00">
                        </div>

                        <div class="form-group">
                            <label for="end_time">Время окончания *</label>
                            <input type="time" id="end_time" name="end_time" required value="18:00">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="add_schedule" class="btn btn-accent">Добавить график</button>
                    </div>
                </form>
            </div>

            <!-- Таблица графика -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Понедельник<br><?php echo date('d.m', strtotime($current_week_start)); ?></th>
                            <th>Вторник<br><?php echo date('d.m', strtotime($current_week_start . ' +1 day')); ?></th>
                            <th>Среда<br><?php echo date('d.m', strtotime($current_week_start . ' +2 days')); ?></th>
                            <th>Четверг<br><?php echo date('d.m', strtotime($current_week_start . ' +3 days')); ?></th>
                            <th>Пятница<br><?php echo date('d.m', strtotime($current_week_start . ' +4 days')); ?></th>
                            <th>Суббота<br><?php echo date('d.m', strtotime($current_week_start . ' +5 days')); ?></th>
                            <th>Воскресенье<br><?php echo date('d.m', strtotime($current_week_start . ' +6 days')); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Группируем графики по сотрудникам
                        $employee_schedules = [];
                        foreach ($schedules as $schedule) {
                            $employee_schedules[$schedule['user_id']][$schedule['work_date']] = $schedule;
                        }

                        foreach ($employees as $employee):
                            ?>
                            <tr>
                                <td class="day-header"><?php echo htmlspecialchars($employee['full_name']); ?></td>

                                <?php for ($i = 0; $i < 7; $i++):
                                    $current_date = date('Y-m-d', strtotime($current_week_start . " +$i days"));
                                    $schedule = $employee_schedules[$employee['id']][$current_date] ?? null;
                                    ?>
                                    <td>
                                        <?php if ($schedule): ?>
                                            <div class="work-hours">
                                                <?php echo substr($schedule['start_time'], 0, 5); ?> -
                                                <?php echo substr($schedule['end_time'], 0, 5); ?>
                                            </div>
                                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                                <div class="action-buttons" style="margin-top: 5px;">
                                                    <a href="schedule.php?delete=<?php echo $schedule['id']; ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Удалить этот график?')">Удалить</a>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="no-schedule">—</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Быстрый просмотр занятости -->
            <div class="table-container">
                <h3 style="padding: 15px; color: var(--primary-color); border-bottom: 1px solid #eee;">Ближайшие записи
                    на прием</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Дата и время</th>
                            <th>Клиент</th>
                            <th>Сотрудник</th>
                            <th>Тип приема</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $upcoming_appointments = $pdo->query("
                            SELECT a.*, c.full_name as client_name, u.full_name as employee_name 
                            FROM appointments a 
                            LEFT JOIN clients c ON a.client_id = c.id 
                            LEFT JOIN users u ON a.user_id = u.id 
                            WHERE a.appointment_date >= CURDATE() 
                            AND a.status = 'запланирован'
                            ORDER BY a.appointment_date, a.appointment_time 
                            LIMIT 10
                        ")->fetchAll();

                        foreach ($upcoming_appointments as $appointment):
                            ?>
                            <tr>
                                <td>
                                    <?php echo date('d.m.Y', strtotime($appointment['appointment_date'])); ?><br>
                                    <strong><?php echo $appointment['appointment_time']; ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($appointment['client_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['employee_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['appointment_type']); ?></td>
                                <td>
                                    <span
                                        style="background-color: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px;">
                                        <?php echo $appointment['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($upcoming_appointments)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #6c757d; padding: 20px;">
                                    Нет запланированных приемов
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Устанавливаем минимальную дату как сегодня
        document.getElementById('work_date').min = new Date().toISOString().split('T')[0];

        // Автоматическое заполнение времени окончания при изменении времени начала
        document.getElementById('start_time').addEventListener('change', function () {
            const startTime = this.value;
            if (startTime) {
                const [hours, minutes] = startTime.split(':');
                const endHours = (parseInt(hours) + 9) % 24; // Стандартный рабочий день 9 часов
                const endTime = endHours.toString().padStart(2, '0') + ':' + minutes;
                document.getElementById('end_time').value = endTime;
            }
        });
    </script>
</body>

</html>