<?php
session_start();
require_once 'config.php';
checkAuth();
checkAdmin();

$pdo = getDBConnection();
$employees = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление сотрудниками - Ортопедический салон</title>
    <style>
        :root {
            --primary-color: #009AA6;
            --accent-color: #FF6E00;
            --light-gray: #f5f5f5;
            --dark-gray: #333;
            --white: #ffffff;
        }

        /* Общие стили из dashboard.php */
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

        .table-container {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
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

        .status-active {
            color: #28a745;
            font-weight: 500;
        }

        .status-inactive {
            color: #dc3545;
            font-weight: 500;
        }

        .role-admin {
            background-color: var(--accent-color);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .role-employee {
            background-color: var(--primary-color);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: var(--white);
            margin: 5% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
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

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
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
            <li class="nav-item"><a href="schedule.php" class="nav-link"><i>⏰</i> График работы</a></li>
            <li class="nav-item"><a href="employees.php" class="nav-link active"><i>⚙️</i> Сотрудники</a></li>
        </ul>
    </div>

    <!-- Основной контент -->
    <div class="main-content">
        <div class="header">
            <h2>Управление сотрудниками</h2>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?> (Администратор)</div>
                <a href="logout.php" class="logout-btn">Выйти</a>
            </div>
        </div>

        <div class="content">
            <div class="section-title">
                <h2>Список сотрудников</h2>
                <button class="btn btn-accent" onclick="openModal()">+ Добавить сотрудника</button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ФИО</th>
                            <th>Логин</th>
                            <th>Email</th>
                            <th>Телефон</th>
                            <th>Роль</th>
                            <th>Статус</th>
                            <th>Дата регистрации</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($employee['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($employee['username']); ?></td>
                                <td><?php echo htmlspecialchars($employee['email']); ?></td>
                                <td><?php echo htmlspecialchars($employee['phone']); ?></td>
                                <td>
                                    <span class="role-<?php echo $employee['role']; ?>">
                                        <?php echo $employee['role'] == 'admin' ? 'Администратор' : 'Сотрудник'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-<?php echo $employee['is_active'] ? 'active' : 'inactive'; ?>">
                                        <?php echo $employee['is_active'] ? 'Активен' : 'Неактивен'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d.m.Y', strtotime($employee['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm"
                                            onclick="editEmployee(<?php echo $employee['id']; ?>)">Редактировать</button>
                                        <button class="btn btn-sm btn-accent"
                                            onclick="toggleEmployee(<?php echo $employee['id']; ?>, <?php echo $employee['is_active'] ? 0 : 1; ?>)">
                                            <?php echo $employee['is_active'] ? 'Деактивировать' : 'Активировать'; ?>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Модальное окно добавления сотрудника -->
    <div id="employeeModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle">Добавить сотрудника</h3>
            <form id="employeeForm" method="POST" action="save_employee.php">
                <input type="hidden" id="employeeId" name="id">

                <div class="form-group">
                    <label for="full_name">ФИО</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>

                <div class="form-group">
                    <label for="username">Логин</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input type="tel" id="phone" name="phone">
                </div>

                <div class="form-group">
                    <label for="role">Роль</label>
                    <select id="role" name="role" required>
                        <option value="employee">Сотрудник</option>
                        <option value="admin">Администратор</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal()">Отмена</button>
                    <button type="submit" class="btn btn-accent">Сохранить</button>
                </div>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm"
                            onclick="editEmployee(<?php echo $employee['id']; ?>)">Редактировать</button>
                        <button class="btn btn-sm <?php echo $employee['is_active'] ? 'btn-warning' : 'btn-success'; ?>"
                            onclick="toggleEmployee(<?php echo $employee['id']; ?>, <?php echo $employee['is_active']; ?>, '<?php echo htmlspecialchars($employee['full_name']); ?>')">
                            <?php echo $employee['is_active'] ? 'Деактивировать' : 'Активировать'; ?>
                        </button>
                        <button class="btn btn-sm btn-danger"
                            onclick="deleteEmployee(<?php echo $employee['id']; ?>, '<?php echo htmlspecialchars($employee['full_name']); ?>')">
                            Удалить
                        </button>
                    </div>
                </td>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('employeeModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Добавить сотрудника';
            document.getElementById('employeeForm').reset();
            document.getElementById('employeeId').value = '';
        }

        function closeModal() {
            document.getElementById('employeeModal').style.display = 'none';
        }

        function editEmployee(id) {
            // Здесь будет AJAX запрос для получения данных сотрудника
            alert('Редактирование сотрудника с ID: ' + id);
        }

        function toggleEmployee(id, newStatus) {
            if (confirm('Вы уверены, что хотите изменить статус сотрудника?')) {
                // Здесь будет AJAX запрос для изменения статуса
                alert('Изменение статуса сотрудника с ID: ' + id + ' на ' + newStatus);
            }
        }

        // Закрытие модального окна при клике вне его
        window.onclick = function (event) {
            const modal = document.getElementById('employeeModal');
            if (event.target == modal) {
                closeModal();
            }
        }
        let currentEmployeeId = null;

        // Открытие модального окна
        function openModal(id = null) {
            const modal = document.getElementById('employeeModal');
            modal.style.display = 'block';

            if (id) {
                currentEmployeeId = id;
                document.getElementById('modalTitle').textContent = 'Редактировать сотрудника';
                loadEmployeeData(id);
            } else {
                currentEmployeeId = null;
                document.getElementById('modalTitle').textContent = 'Добавить сотрудника';
                document.getElementById('employeeForm').reset();
                document.getElementById('password').required = true;
                document.getElementById('password').closest('.form-group').style.display = 'block';
            }
        }

        // Закрытие модального окна
        function closeModal() {
            document.getElementById('employeeModal').style.display = 'none';
            currentEmployeeId = null;
        }

        // Загрузка данных сотрудника
        function loadEmployeeData(id) {
            fetch(`get_employee.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('employeeId').value = data.employee.id;
                        document.getElementById('full_name').value = data.employee.full_name;
                        document.getElementById('username').value = data.employee.username;
                        document.getElementById('email').value = data.employee.email;
                        document.getElementById('phone').value = data.employee.phone || '';
                        document.getElementById('role').value = data.employee.role;
                        document.getElementById('is_active').checked = data.employee.is_active == 1;

                        // Пароль не обязателен при редактировании
                        document.getElementById('password').required = false;
                        document.getElementById('password').closest('.form-group').style.display = 'none';
                        document.getElementById('password').value = '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при загрузке данных');
                });
        }

        // Редактирование сотрудника
        function editEmployee(id) {
            openModal(id);
        }

        // Переключение статуса сотрудника
        function toggleEmployee(id, currentStatus, employeeName) {
            const newStatus = currentStatus == 1 ? 0 : 1;
            const action = newStatus == 1 ? 'активировать' : 'деактивировать';

            if (confirm(`Вы уверены, что хотите ${action} сотрудника "${employeeName}"?`)) {
                fetch('toggle_employee_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}&status=${newStatus}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert('Ошибка: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ошибка при изменении статуса');
                    });
            }
        }

        // Удаление сотрудника
        function deleteEmployee(id, employeeName) {
            if (confirm(`Вы уверены, что хотите удалить сотрудника "${employeeName}"?\n\nВнимание: Если у сотрудника есть связанные записи, он будет деактивирован.`)) {
                fetch('delete_employee.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert('Ошибка: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ошибка при удалении');
                    });
            }
        }

        // Обработка формы
        document.getElementById('employeeForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            if (currentEmployeeId) {
                formData.append('id', currentEmployeeId);
            }
            formData.append('is_active', document.getElementById('is_active').checked ? 1 : 0);

            fetch('save_employee.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        closeModal();
                        location.reload();
                    } else {
                        alert('Ошибка: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при сохранении');
                });
        });

        // Закрытие модального окна при клике вне его
        window.onclick = function (event) {
            const modal = document.getElementById('employeeModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>